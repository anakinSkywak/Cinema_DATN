<?php

namespace App\Http\Controllers\Api;

use App\Models\MemberShips;
use Illuminate\Http\Request;
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
        $data = memberships::with('registerMember')->get();

        if ($data->isEmpty()) {
            return response()->json([
                'message' => 'Không có dữ liệu Membership nào'
            ], 200);
        }

        return response()->json([
            'message' => 'Hiển thị dữ liệu thành công',
            'data' => $data
        ]);
    }

    /**
     * Hiển thị thông tin thẻ hội viên theo ID.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $membership = memberships::with('registerMember')->find($id);

        if (!$membership) {
            return response()->json([
                'message' => 'Thẻ hội viên không tồn tại!'
            ], 404); // Trả về 404 nếu không tìm thấy
        }

        return response()->json([
            'data' => $membership
        ], 200); // Trả về 200 nếu tìm thấy
    }

    /**
     * Cập nhật thông tin thẻ hội viên theo ID.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $membership = memberships::find($id);

        if (!$membership) {
            return response()->json([
                'message' => 'Thẻ hội viên không tồn tại!'
            ], 404); // Trả về 404 nếu không tìm thấy
        }

        // Xác thực dữ liệu đầu vào
        $validated = $request->validate([
            'register_member_id' => 'sometimes|required|exists:register_members,id',
            'so_the' => 'sometimes|required|string|max:255',
            'ngay_cap' => 'sometimes|required|date',
            'ngay_het_han' => 'sometimes|required|date|after:ngay_cap',
        ]);

        $membership->update($validated);

        return response()->json([
            'data' => $membership,
            'message' => 'Cập nhật thẻ hội viên thành công!'
        ], 200); // Trả về 200 khi cập nhật thành công
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
