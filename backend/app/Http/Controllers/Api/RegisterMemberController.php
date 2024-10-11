<?php

namespace App\Http\Controllers\Api;

use App\Models\RegisterMember;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RegisterMemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Lấy tất cả dữ liệu từ bảng RegisterMember
        $data = RegisterMember::with('member', 'payments')->get();

        if ($data->isEmpty()) {
            return response()->json([
                'message' => 'Không có dữ liệu RegisterMember nào'
            ], 200);
        }

        return response()->json([
            'message' => 'Hiển thị dữ liệu thành công',
            'data' => $data
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate dữ liệu khi tạo RegisterMember mới
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'hoivien_id' => 'required|exists:members,id',
            'tong_tien' => 'required|numeric',
            'ngay_dang_ky' => 'required|date',
            'trang_thai' => 'required|integer',
        ]);

        // Tạo mới RegisterMember
        $registerMember = RegisterMember::create($validated);

        return response()->json([
            'message' => 'Thêm mới RegisterMember thành công',
            'data' => $registerMember
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Hiển thị RegisterMember theo ID
        $dataID = RegisterMember::with('member', 'payments')->find($id);

        if (!$dataID) {
            return response()->json([
                'message' => 'Không có dữ liệu RegisterMember theo id'
            ], 404);
        }

        return response()->json([
            'message' => 'Dữ liệu show theo ID thành công',
            'data' => $dataID,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Cập nhật RegisterMember theo ID
        $dataID = RegisterMember::find($id);

        if (!$dataID) {
            return response()->json([
                'message' => 'Không tìm thấy RegisterMember theo ID'
            ], 404);
        }

        // Validate dữ liệu khi cập nhật RegisterMember
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'hoivien_id' => 'required|exists:members,id',
            'tong_tien' => 'required|numeric',
            'ngay_dang_ky' => 'required|date',
            'trang_thai' => 'required|integer',
        ]);

        // Cập nhật RegisterMember
        $dataID->update($validated);

        return response()->json([
            'message' => 'Cập nhật dữ liệu thành công',
            'data' => $dataID,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Xóa RegisterMember theo ID
        $dataID = RegisterMember::find($id);

        if (!$dataID) {
            return response()->json([
                'message' => 'Không tìm thấy RegisterMember theo ID'
            ], 404);
        }

        $dataID->delete();

        return response()->json([
            'message' => 'Xóa RegisterMember thành công'
        ], 200);
    }
}
