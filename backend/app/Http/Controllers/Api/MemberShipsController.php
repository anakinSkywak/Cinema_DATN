<?php

namespace App\Http\Controllers\Api;

use App\Models\MemberShips;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
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
        if (!auth()->check()) {
            return response()->json([
                'message' => 'Bạn cần đăng nhập để xem thông tin thẻ hội viên!'
            ], 401);  // 401 Unauthorized nếu chưa đăng nhập
        }

        // Lấy user_id của người dùng đã đăng nhập
        $user_id = auth()->user()->id;

        $membership = Memberships::with('registerMember')
            ->whereHas('registerMember', function ($query) use ($user_id) {
                $query->where('user_id', $user_id);  
            })
            ->where('id', $id)  
            ->first();

        // Kiểm tra xem thẻ hội viên có tồn tại không và có thuộc người dùng này không
        if (!$membership) {
            return response()->json(['message' => 'Thẻ hội viên không tồn tại hoặc không phải của bạn!'], 404);
        }

        // Thẻ hội viên hợp lệ, tiếp tục xử lý thông tin
        $currentDate = now();
        $expirationDate = Carbon::parse($membership->ngay_het_han);

        if ($expirationDate->isBefore($currentDate)) {
            $membership->trang_thai = 1;
            $membership->renewal_message = "Thẻ hội viên đã hết hạn. Vui lòng đăng ký lại thẻ hội viên mới!";
        } else {
            if ($expirationDate->diffInDays($currentDate) <= 2) {
                $membership->renewal_message = "Thẻ hội viên sắp hết hạn!!!. Vui lòng gia hạn thẻ!";
            }
            $membership->trang_thai = 0;
            $membership->renewal_message = $membership->renewal_message ?? "Thẻ còn thời gian sử dụng.";
        }

        // Cập nhật trạng thái thẻ hội viên
        $membership->save();

        return response()->json(['message' => 'Hiển thị thông tin thẻ hội viên thành công', 'data' => $membership], 200);
    }









    public function getUserMembership()
    {
        // Kiểm tra nếu người dùng đã đăng nhập
        if (!auth()->check()) {
            return response()->json([
                'message' => 'Bạn cần đăng nhập để xem thông tin thẻ hội viên!'
            ], 401);  // 401 Unauthorized nếu chưa đăng nhập
        }
    
        // Lấy user_id của người dùng đã đăng nhập
        $user_id = auth()->user()->id;
    
        // Truy vấn để lấy thẻ hội viên của người dùng
        $membership = Memberships::with('registerMember')  // Quan hệ với RegisterMember
            ->whereHas('registerMember', function ($query) use ($user_id) {
                $query->where('user_id', $user_id);  // Lọc theo user_id trong bảng register_members
            })
            ->first();  // Lấy thẻ hội viên đầu tiên (nếu có)
    
        // Kiểm tra xem thẻ hội viên có tồn tại không
        if (!$membership) {
            return response()->json([
                'message' => 'Bạn chưa đăng ký thẻ hội viên!'
            ], 404);
        }
    
        // Nếu thẻ hội viên tồn tại, trả về thông tin thẻ hội viên
        return response()->json([
            'message' => 'Thông tin thẻ hội viên',
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
