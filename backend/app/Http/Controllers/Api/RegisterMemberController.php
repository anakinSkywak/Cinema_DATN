<?php

namespace App\Http\Controllers\Api;

use App\Models\RegisterMember;
use App\Models\Member; // Import thêm Member để lấy giá từ bảng hội viên
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;

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
        'user_id' => 'required|integer|exists:users,id',
        'hoivien_id' => 'required|integer|exists:members,id',
        'trang_thai' => 'required|integer',
    ]);

    // Lấy giá hội viên
    $member = Member::find($validated['hoivien_id']);
    if (!$member) {
        return response()->json(['message' => 'Hội viên không tồn tại!'], 404);
    }

    // Tính toán tổng tiền
    $tong_tien = $member->gia;

    // Tạo mới RegisterMember
    $registerMember = RegisterMember::create([
        'user_id' => $validated['user_id'],
        'hoivien_id' => $validated['hoivien_id'],
        'tong_tien' => $tong_tien,  // Thêm trường tong_tien
        'ngay_dang_ky' => Carbon::now(),
        'trang_thai' => $validated['trang_thai'],
    ]);

    return response()->json([
        'message' => 'Thêm mới RegisterMember thành công',
        'data' => $registerMember
    ], 200);
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
            'ngay_dang_ky' => 'required|date',
            'trang_thai' => 'required|integer',
        ]);

        // Lấy thông tin hội viên để tính giá mới
        $member = Member::find($request->hoivien_id);
        if (!$member) {
            return response()->json([
                'message' => 'Hội viên không tồn tại'
            ], 404);
        }

        // Cập nhật giá dựa trên loại hội viên
        $validated['tong_tien'] = $member->gia;

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
