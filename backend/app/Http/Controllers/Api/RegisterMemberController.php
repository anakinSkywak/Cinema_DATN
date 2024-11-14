<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Member;
use Illuminate\Http\Request;
use App\Models\RegisterMember;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\MemberShips;
use Illuminate\Support\Facades\Log;

class RegisterMemberController extends Controller
{
    public function index()
    {
        // Lấy tất cả dữ liệu từ bảng RegisterMember
        $data = RegisterMember::with('membership', 'member', 'payments')->get();

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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'hoivien_id' => 'required|integer|exists:members,id',
            'trang_thai' => 'required|integer',
        ]);

        $member = Member::find($validated['hoivien_id']);
        if (!$member) {
            return response()->json(['message' => 'Hội viên không tồn tại!'], 404);
        }

        $tong_tien = $member->gia * $member->thoi_gian;
        $ngay_dang_ky = Carbon::now();
        $ngay_het_han = $ngay_dang_ky->copy()->addMonths($member->thoi_gian);

        DB::beginTransaction();
        try {
            // Tạo mới RegisterMember mà không thêm Membership
            $registerMember = RegisterMember::create([
                'user_id' => $validated['user_id'],
                'hoivien_id' => $validated['hoivien_id'],
                'tong_tien' => $tong_tien,
                'ngay_dang_ky' => $ngay_dang_ky,
                'ngay_het_han' => $ngay_het_han,
                'trang_thai' => 0, // Đăng ký chưa thanh toán
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Đăng ký thành công, vui lòng chọn phương thức thanh toán.',
                'data' => $registerMember
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi khi tạo RegisterMember', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Có lỗi xảy ra khi tạo RegisterMember',
                'error' => $e->getMessage(),
            ], 500);
        }
    }






    public function update(Request $request, $id)
    {
        // Tìm RegisterMember theo ID
        $registerMember = RegisterMember::find($id);

        if (!$registerMember) {
            return response()->json(['message' => 'Không tìm thấy RegisterMember theo ID'], 404);
        }

        // Xác thực dữ liệu khi cập nhật RegisterMember
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'hoivien_id' => 'required|exists:members,id',
            'ngay_dang_ky' => 'required|date',
            'trang_thai' => 'required|integer',
        ]);

        // Lấy thông tin hội viên mới và cũ
        $newMember = Member::find($validated['hoivien_id']);
        $currentMember = $registerMember->member;

        if (!$newMember) {
            return response()->json(['message' => 'Hội viên mới không tồn tại'], 404);
        }

        // Tính giá mới
        $tong_tien_moi = $newMember->gia * $newMember->thoi_gian;

        // Kiểm tra nếu người dùng đang nâng cấp từ thành viên thường lên VIP để áp dụng giảm giá 10%
        if ($currentMember->loai_thanh_vien === 'thuong' && $newMember->loai_thanh_vien === 'vip') {
            $tong_tien_moi *= 0.8; // Giảm giá 10%
        }

        // Cập nhật thông tin
        $registerMember->update([
            'user_id' => $validated['user_id'],
            'hoivien_id' => $validated['hoivien_id'],
            'ngay_dang_ky' => $validated['ngay_dang_ky'],
            'tong_tien' => $tong_tien_moi,
            'trang_thai' => $validated['trang_thai'],
        ]);

        // Xử lý thanh toán nếu trạng thái là chưa thanh toán
        if ($validated['trang_thai'] == 0) {
            app('App\Http\Controllers\Api\PaymentController')->processPaymentForRegister($request, $registerMember->id);
        }

        return response()->json([
            'message' => 'Cập nhật thành công, đã áp dụng giảm giá nếu có',
            'data' => $registerMember,
        ], 200);
    }


    public function destroy($id)
    {
        $dataID = RegisterMember::find($id);

        if (!$dataID) {
            return response()->json(['message' => 'Không tìm thấy RegisterMember theo ID'], 404);
        }
        $dataID->delete();
        return response()->json(['message' => 'Xóa RegisterMember thành công'], 200);
    }
}
