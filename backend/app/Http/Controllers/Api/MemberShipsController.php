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
        $membership = Memberships::with('registerMember')->find($id);

        if (!$membership) {
            return response()->json([
                'message' => 'Thẻ hội viên không tồn tại!'
            ], 404); // Trả về 404 nếu không tìm thấy
        }

        // Xác định trạng thái thẻ dựa trên ngày
        $currentDate = now();
        if ($membership->ngay_het_han && $membership->ngay_het_han < $currentDate) {
            $membership->status = 'expired';

            // Thêm thông báo yêu cầu đăng ký thẻ mới
            $membership->renewal_message = "Thẻ hội viên đã hết hạn. Vui lòng đăng ký lại thẻ hội viên mới!";
        } else {
            $membership->status = 'active';
            $membership->renewal_message = null;  // Không có thông báo nếu thẻ còn hạn
        }

        return response()->json([
            'message' => 'Hiển thị thông tin thẻ hội viên thành công',
            'data' => $membership
        ], 200); // Trả về 200 nếu tìm thấy
    }


    /**
     * Xóa thẻ hội viên theo ID.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $membership = memberships::find($id);

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
