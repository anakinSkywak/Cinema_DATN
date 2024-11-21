<?php

namespace App\Http\Controllers\Api;

use App\Models\MemberShips;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;

class MemberShipsController extends Controller
{
    /**
     * Hiển thị danh sách tất cả thẻ hội viên.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        // Lấy tất cả dữ liệu từ bảng Membership
        $data = Memberships::with('registerMember')->get();

        if ($data->isEmpty()) {
            return response()->json([
                'message' => 'Không có dữ liệu Membership nào'
            ], 200);
        }

        // Dữ liệu đã có status tự động qua Accessor
        return response()->json([
            'message' => 'Hiển thị dữ liệu thành công',
            'data' => $data
        ]);
    }

    public function show($id)
    {
        // Kiểm tra nếu người dùng đã đăng nhập
        // if (!auth()->check()) {
        //     return response()->json([
        //         'message' => 'Bạn cần đăng nhập để xem thông tin thẻ hội viên!'
        //     ], 401);  // 401 Unauthorized nếu chưa đăng nhập
        // }

        // Lấy thông tin thẻ hội viên của người dùng đã đăng nhập
        // $membership = Memberships::with('registerMember')
        //     ->where('dangkyhoivien_id', auth()->user()->id) // Chỉ lấy thẻ của người dùng hiện tại
        //     ->find($id);

        //test thử
        $membership = Memberships::with('registerMember')->find($id);

        if (!$membership) {
            return response()->json([
                'message' => 'Thẻ hội viên không tồn tại!'
            ], 404);
        }

        if (!$membership) {
            return response()->json([
                'message' => 'Thẻ hội viên không tồn tại hoặc không phải của bạn!'
            ], 404); // 404 nếu không tìm thấy thẻ hoặc thẻ không phải của người dùng hiện tại
        }

        // Xác định trạng thái thẻ dựa trên ngày hết hạn
        $currentDate = now(); // Lấy thời gian hiện tại (bao gồm ngày và giờ)
        $expirationDate = Carbon::parse($membership->ngay_het_han);

        if ($expirationDate->isBefore($currentDate)) {
            // Thẻ đã hết hạn
            $membership->trang_thai = 1;  // Thẻ hết hạn => Trạng thái = 1
            $membership->renewal_message = "Thẻ hội viên đã hết hạn. Vui lòng đăng ký lại thẻ hội viên mới!";

            // Kiểm tra nếu thẻ đã hết hạn và không gia hạn trong vòng 2 ngày
            if ($expirationDate->addDays(2)->isBefore($currentDate)) {
                // Nếu đã qua 2 ngày sau khi hết hạn mà không gia hạn, xóa thẻ
                $membership->delete();
                return response()->json([
                    'message' => 'Thẻ hội viên đã hết hạn hơn 2 ngày và đã bị xóa!',
                ], 200);
            }
        } else {
            // Kiểm tra nếu thẻ còn ít nhất 2 ngày nữa sẽ hết hạn
            if ($expirationDate->diffInDays($currentDate) <= 2) {
                $membership->renewal_message = "Thẻ hội viên sắp hết hạn!!!. Vui lòng gia hạn thẻ!";
            }

            // Thẻ còn hạn
            $membership->trang_thai = 0;  // Thẻ còn hạn => Trạng thái = 0
            $membership->renewal_message = $membership->renewal_message ?? null;  // Không có thông báo nếu thẻ còn hạn
        }

        // Cập nhật thẻ nếu có thay đổi trạng thái
        $membership->save();

        return response()->json([
            'message' => 'Hiển thị thông tin thẻ hội viên thành công',
            'data' => $membership
        ], 200);
    }




    /**
     * Xóa thẻ hội viên theo ID.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $membership = Memberships::find($id);

        if (!$membership) {
            return response()->json([
                'message' => 'Thẻ hội viên không tồn tại!'
            ], 404); // Trả về 404 nếu không tìm thấy
        }

        $membership->delete();

        return response()->json([
            'message' => 'Xóa thẻ hội viên thành công!'
        ], 200); // Trả về 200 khi xóa thành công
    }
}
