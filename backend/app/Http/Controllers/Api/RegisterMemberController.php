<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RegisterMember;
use App\Models\Member; // Import thêm Member để lấy giá từ bảng hội viên
use Illuminate\Http\Request;

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
            'phuong_thuc_thanh_toan' => 'required|in:credit_card,paypal,cash,bank_transfer', // Thêm phần phương thức thanh toán
        ]);

        // Lấy giá hội viên và thời gian
        $member = Member::find($validated['hoivien_id']);
        if (!$member) {
            return response()->json(['message' => 'Hội viên không tồn tại!'], 404);
        }

        // Tính toán tổng tiền
        $tong_tien = $member->gia * $member->thoi_gian;

        // Tính toán ngày hết hạn
        $ngay_dang_ky = now();
        $ngay_het_han = $ngay_dang_ky->copy()->addMonths($member->thoi_gian);

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
        $paymentResponse = app('App\Http\Controllers\Api\PaymentController')->processPaymentForRegister($request, $registerMember);

        // Nếu thanh toán không thành công, xóa bản ghi RegisterMember đã tạo
        if ($paymentResponse->getStatusCode() !== 200) {
            $registerMember->delete();
            return $paymentResponse;
        }

        return response()->json([
            'message' => 'Thêm mới RegisterMember và thanh toán thành công',
            'data' => $registerMember
        ], 201); 
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
        // Xóa RegisterMember theo ID
        $dataID = RegisterMember::find($id);

        if (!$dataID) {
            return response()->json(['message' => 'Không tìm thấy RegisterMember theo ID'], 404);
        }

        $dataID->delete();

        return response()->json(['message' => 'Xóa RegisterMember thành công'], 200);
    }
}
