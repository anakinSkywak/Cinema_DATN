<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\CouponCodeTaken;
use App\Http\Controllers\Controller;


class CouponCodeTakenController extends Controller
{
    // Phương thức lấy tất cả countdown_voucher của người dùng
    public function getUserCountdownVouchers($userId)
    {
        // Lấy người dùng theo ID
        $user = User::find($userId);

        // Kiểm tra nếu người dùng không tồn tại
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Lấy tất cả coupon_code_takens mà người dùng này đã nhận
        $coupons = $user->couponCodeTakens()->with('countdownvoucher')->get();

        // Trả về danh sách countdown_vouchers
        return response()->json([
            'user' => $user->ho_ten,  // Trả về tên người dùng (hoặc các thông tin khác nếu cần)
            'countdown_vouchers' => $coupons->map(function ($coupon) {
                return [
                    'id' => $coupon->countdownvoucher->id,
                    'muc_giam_gia' => $coupon->countdownvoucher->muc_giam_gia,
                    'so_luong_con_lai' => $coupon->countdownvoucher->so_luong_con_lai,
                ];
            })
        ]);
    }
}