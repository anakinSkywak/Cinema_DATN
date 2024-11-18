<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\CouponCodeTaken;
use App\Models\CountdownVoucher;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CouponCodeTakenController extends Controller
{
    public function showVoucherCodes()
    {
        // Kiểm tra xem người dùng đã đăng nhập chưa
        if (!Auth::check()) {
            return response()->json(['message' => 'Bạn cần đăng nhập để xem mã giảm giá'], 401);
        }

        $user = Auth::user(); // Lấy thông tin người dùng đang đăng nhập

        // Lấy tất cả các mã giảm giá mà người dùng đã nhận
        $voucherCodes = CouponCodeTaken::where('user_id', $user->id)->get();

        // Kiểm tra nếu không có mã giảm giá nào
        if ($voucherCodes->isEmpty()) {
            return response()->json(['message' => 'Bạn chưa nhận mã giảm giá nào.'], 404);
        }

        // Trả về danh sách mã giảm giá
        return response()->json([
            'message' => 'Danh sách mã giảm giá của bạn',
            'voucher_codes' => $voucherCodes,
        ]);
    }
    public function spinVoucher(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'message' => 'Vui lòng đăng nhập để quay',
            ], 401);
        }
        // Kiểm tra lượt quay
        if ($user->so_luot_quay <= 0) {
            return response()->json(['message' => 'Bạn đã hết lượt quay.'], 400);
        }

        // Tìm đợt quay còn hoạt động
        $countdown = CountdownVoucher::where('trang_thai', '0')
            ->where('so_luong_con_lai', '>', 0) // Kiểm tra số lượng còn lại > 0
            ->whereDate('ngay', '>=', now()->toDateString()) // Ngày phải là hôm nay hoặc tương lai
            ->whereTime('thoi_gian_bat_dau', '<=', now()) // Thời gian bắt đầu phải trước hoặc bằng thời điểm hiện tại
            ->whereTime('thoi_gian_ket_thuc', '>=', now()) // Thời gian kết thúc phải sau thời điểm hiện tại
            ->first();

        if (!$countdown) {
            return response()->json(['message' => 'Hiện tại không có đợt quay nào khả dụng.'], 404);
        }

        // Giảm số lượng còn lại
        $countdown->decrement('so_luong_con_lai', 1);

        // Nếu số lượng còn lại bằng 0, cập nhật trạng thái thành '1' (không hoạt động)
        if ($countdown->so_luong_con_lai <= 0) {
            $countdown->update(['trang_thai' => '1']);
        }

        $voucherCode = 'VC-' . strtoupper(uniqid()); // Tạo mã ngẫu nhiên (hoặc lấy từ danh sách mã có sẵn)

        // Lưu lịch sử mã giảm giá
        CouponCodeTaken::create([
            'countdownvoucher_id' => $countdown->id,
            'user_id' => $user->id,  // Thay 15 bằng ID người dùng thực tế nếu cần
            'created_at' => now(),
            'ma_giam_gia' => $voucherCode,
        ]);

        // Giảm lượt quay của người dùng (nếu cần)
        // $user->decrement('so_luot_quay', 1);

        return response()->json([
            'message' => 'Quay mã giảm giá thành công!',
            'ma_giam_gia' => $voucherCode,
        ]);
    }
}
