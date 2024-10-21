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
        $memberships = MemberShips::with('registerMember')->orderBy('id', 'DESC')->paginate(10);

        return response()->json([
            'data' => $memberships,
            'pagination' => [
                'current_page' => $memberships->currentPage(),
                'total_pages' => $memberships->lastPage(),
                'total_items' => $memberships->total(),
                'per_page' => $memberships->perPage(),
                'next_page_url' => $memberships->nextPageUrl(),
                'prev_page_url' => $memberships->previousPageUrl(),
            ]
        ], 200);
    }

    /**
     * Tạo mới một thẻ hội viên.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Xác thực dữ liệu đầu vào
        $validated = $request->validate([
            'register_member_id' => 'required|exists:register_members,id',
            'so_the' => 'required|string|max:255',
            'ngay_cap' => 'required|date',
            'ngay_het_han' => 'required|date|after:ngay_cap',
        ]);

        $membership = MemberShips::create($validated);

        return response()->json([
            'data' => $membership,
            'message' => 'Thêm thẻ hội viên thành công!'
        ], 201); // Trả về 201 khi thêm thành công
    }

    /**
     * Hiển thị thông tin thẻ hội viên theo ID.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $membership = MemberShips::with('registerMember')->find($id);

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
        $membership = MemberShips::find($id);

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
        $membership = MemberShips::find($id);

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
