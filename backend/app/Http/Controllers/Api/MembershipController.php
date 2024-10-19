<?php

namespace App\Http\Controllers\Api;

use App\Models\Membership;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MembershipController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Lấy tất cả dữ liệu từ bảng Membership
        $data = Membership::with('registerMember')->get();

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
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate dữ liệu khi tạo Membership mới
        $validated = $request->validate([
            'dangkyhoivien_id' => 'required|exists:register_members,id',
            'ngay_dang_ky' => 'required|date',
            'trang_thai' => 'required|integer',
        ]);

        // Tạo mới Membership
        $membership = Membership::create($validated);

        return response()->json([
            'message' => 'Thêm mới Membership thành công',
            'data' => $membership
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Hiển thị Membership theo ID
        $dataID = Membership::with('registerMember')->find($id);

        if (!$dataID) {
            return response()->json([
                'message' => 'Không có dữ liệu Membership theo id'
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
        // Cập nhật Membership theo ID
        $dataID = Membership::find($id);

        if (!$dataID) {
            return response()->json([
                'message' => 'Không tìm thấy Membership theo ID'
            ], 404);
        }

        // Validate dữ liệu khi cập nhật Membership
        $validated = $request->validate([
            'dangkyhoivien_id' => 'required|exists:register_members,id',
            'ngay_dang_ky' => 'required|date',
            'trang_thai' => 'required|integer',
        ]);

        // Cập nhật Membership
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
        // Xóa Membership theo ID
        $dataID = Membership::find($id);

        if (!$dataID) {
            return response()->json([
                'message' => 'Không tìm thấy Membership theo ID'
            ], 404);
        }

        $dataID->delete();

        return response()->json([
            'message' => 'Xóa Membership thành công'
        ], 200);
    }
}
