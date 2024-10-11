<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RegisterMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RegisterMemberController extends Controller
{
    public function index()
    {
        $data = RegisterMember::with('member', 'user')->orderBy('id', 'DESC')->paginate(10);

        if ($data->isEmpty()) {
            return response()->json([
                'message' => 'Không có đăng ký hội viên',
            ], 204);
        }

        return response()->json([
            'message' => 'Danh sách đăng ký hội viên',
            'data' => $data->items(),
            'pagination' => [
                'current_page' => $data->currentPage(),
                'total_pages' => $data->lastPage(),
                'total_items' => $data->total(),
                'per_page' => $data->perPage(),
                'next_page_url' => $data->nextPageUrl(),
                'prev_page_url' => $data->previousPageUrl(),
            ]
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'hoivien_id' => 'required|exists:members,id',
            'tong_tien' => 'required|numeric|min:0',
            'ngay_dang_ky' => 'required|date',
            'trang_thai' => 'nullable|in:0,1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        $registerMember = RegisterMember::create($validator->validated());

        return response()->json([
            'message' => 'Đăng ký hội viên thành công!',
            'data' => $registerMember
        ], 201);
    }

    public function show($id)
    {
        $registerMember = RegisterMember::with('member', 'user')->find($id);

        if (!$registerMember) {
            return response()->json([
                'message' => 'Không tìm thấy đăng ký hội viên'
            ], 404);
        }

        return response()->json([
            'message' => 'Thông tin đăng ký hội viên',
            'data' => $registerMember
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $registerMember = RegisterMember::find($id);

        if (!$registerMember) {
            return response()->json([
                'message' => 'Không tìm thấy đăng ký hội viên'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'hoivien_id' => 'required|exists:members,id',
            'tong_tien' => 'required|numeric|min:0',
            'ngay_dang_ky' => 'required|date',
            'trang_thai' => 'nullable|in:0,1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        $registerMember->update($validator->validated());

        return response()->json([
            'message' => 'Cập nhật đăng ký hội viên thành công!',
            'data' => $registerMember
        ], 200);
    }

    // Delete a register member
    public function destroy($id)
    {
        $registerMember = RegisterMember::find($id);

        if (!$registerMember) {
            return response()->json([
                'message' => 'Không tìm thấy đăng ký hội viên'
            ], 404);
        }

        $registerMember->delete();

        return response()->json([
            'message' => 'Xóa đăng ký hội viên thành công!'
        ], 200);
    }
}
