<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Member;
use Illuminate\Http\Request;
use App\Models\RegisterMember;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class RegisterMemberController extends Controller
{
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

    public function store(Request $request)
    {
        // Xác thực dữ liệu khi tạo RegisterMember mới
        $validated = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'hoivien_id' => 'required|integer|exists:members,id',
            'trang_thai' => 'required|integer',
            'phuong_thuc_thanh_toan' => 'required|in:credit_card,paypal,cash,bank_transfer',
        ]);

        // Lấy thông tin hội viên
        $member = Member::find($validated['hoivien_id']);
        if (!$member) {
            return response()->json(['message' => 'Hội viên không tồn tại!'], 404);
        }

        // Tính toán tổng tiền và ngày hết hạn
        $tong_tien = $member->gia * $member->thoi_gian;
        $ngay_dang_ky = Carbon::now();
        $ngay_het_han = $ngay_dang_ky->copy()->addMonths($member->thoi_gian);

        DB::beginTransaction();
        try {
            // Tạo mới RegisterMember
            $registerMember = RegisterMember::create([
                'user_id' => $validated['user_id'],
                'hoivien_id' => $validated['hoivien_id'],
                'tong_tien' => $tong_tien,
                'ngay_dang_ky' => $ngay_dang_ky,
                'ngay_het_han' => $ngay_het_han,
                'trang_thai' => 0, // Đăng ký chưa thanh toán
            ]);

            // Gọi phương thức thanh toán
            $paymentController = new PaymentController();
            $paymentResponse = $paymentController->processPaymentForRegister($request, $registerMember);

            // Nếu thanh toán không thành công, rollback
            if ($paymentResponse->getStatusCode() !== 200) {
                DB::rollBack();
                return $paymentResponse;
            }

            // Commit khi cả RegisterMember và Payment thành công
            DB::commit();

            return response()->json([
                'message' => 'Thêm mới RegisterMember và thanh toán thành công',
                'data' => $registerMember
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi khi tạo RegisterMember hoặc thanh toán', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Có lỗi xảy ra khi tạo RegisterMember',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function update(Request $request, $id)
    {
        // Cập nhật RegisterMember theo ID
        $dataID = RegisterMember::find($id);

        if (!$dataID) {
            return response()->json(['message' => 'Không tìm thấy RegisterMember theo ID'], 404);
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
            return response()->json(['message' => 'Hội viên không tồn tại'], 404);
        }

        // Cập nhật giá dựa trên loại hội viên
        $validated['tong_tien'] = $member->gia * $member->thoi_gian;

        // Cập nhật RegisterMember
        $dataID->update($validated);

        // Gọi PaymentController để xử lý thanh toán nếu trạng thái chưa thanh toán
        if ($validated['trang_thai'] == 0) {
            app('App\Http\Controllers\Api\PaymentController')->processPaymentForRegister($request, $dataID->id);
        }

        return response()->json([
            'message' => 'Cập nhật dữ liệu và thanh toán thành công',
            'data' => $dataID,
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
