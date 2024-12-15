<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\CouponCodeTaken;
use App\Models\CountdownVoucher;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CouponCodeTakenController extends Controller
{
    public function showVoucherCodes()
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['message' => 'Chưa đăng nhập, vui lòng đăng nhập'], 401);
        }

        $currentDate = Carbon::now();

        $coupons = DB::table('coupon_code_takens')
            ->join('countdown_vouchers', 'coupon_code_takens.countdownvoucher_id', '=', 'countdown_vouchers.id')
            ->join('users', 'coupon_code_takens.user_id', '=', 'users.id')
            ->join('coupons', 'countdown_vouchers.magiamgia_id', '=', 'coupons.id')
            ->where('coupon_code_takens.user_id', $user->id)
            ->select(
                'users.ho_ten as user_name',
                'coupons.ma_giam_gia',
                'coupons.muc_giam_gia',
                'coupons.Giam_max',
                'coupons.gia_don_toi_thieu',
                'coupons.mota',
                'coupon_code_takens.ngay_nhan',
                'coupon_code_takens.ngay_het_han',
                DB::raw('IF(coupon_code_takens.ngay_het_han < "' . $currentDate . '" , "Hết hạn", "Còn hạn") as status')
            )
            ->get();

        foreach ($coupons as $coupon) {
            if (!isset($coupon->id)) continue;

            $expiredDate = Carbon::parse($coupon->ngay_het_han);
            if ($expiredDate->lt($currentDate->subDays(2))) {
                DB::table('coupon_code_takens')
                    ->where('id', $coupon->id)
                    ->delete();
            }
        }

        return response()->json($coupons);
    }

    public function spinVoucher(Request $request)
    {
        // Kiểm tra xem người dùng đã đăng nhập chưa
        if (!Auth::check()) {
            return response()->json(['message' => 'Bạn cần đăng nhập để săn mã giảm giá'], 401);
        }

        $user = Auth::user(); // Lấy thông tin người dùng đang đăng nhập

        // Đặt timezone thành giờ Việt Nam
        $timezone = 'Asia/Ho_Chi_Minh';
        $now = Carbon::now($timezone); // Lấy thời gian hiện tại theo giờ Việt Nam

        // Kiểm tra xem mã giảm giá còn khả dụng không
        $countdownVoucher = CountdownVoucher::where('id', $request->countdownvoucher_id) // Kiểm tra mã giảm giá cụ thể
            ->where('trang_thai', 0) // Kiểm tra trạng thái là 0
            ->where('so_luong_con_lai', '>', 0) // Kiểm tra số lượng còn lại > 0
            ->whereDate('ngay', '=', $now->toDateString()) // Ngày phải là hôm nay
            ->whereTime('thoi_gian_bat_dau', '<=', $now) // Thời gian bắt đầu phải trước hoặc bằng giờ hiện tại
            ->whereTime('thoi_gian_ket_thuc', '>', $now) // Thời gian kết thúc phải sau hiện tại
            ->first();

        // Kiểm tra nếu không tìm thấy voucher
        if (!$countdownVoucher) {
            return response()->json(['message' => 'Mã giảm giá không còn khả dụng hoặc hết thời gian.'], 400);
        }

        // Kiểm tra xem người dùng đã nhận mã giảm giá này chưa
        $existingCoupon = CouponCodeTaken::where([
            ['user_id', '=', $user->id],
            ['countdownvoucher_id', '=', $request->countdownvoucher_id]
        ])->first();

        if ($existingCoupon) {
            // Người dùng đã nhận mã này trước đó
            return response()->json(['message' => 'Bạn đã nhận mã giảm giá này rồi.'], 400);
        }

        // Tạo bản ghi CouponCodeTaken mới
        $coupon = CouponCodeTaken::create([
            'countdownvoucher_id' => $request->countdownvoucher_id,
            'user_id' => $user->id, // Dùng id của người dùng đã đăng nhập
            'ngay_nhan' => $now, // Ngày nhận theo giờ Việt Nam
            'ngay_het_han' => $now->addDays(7), // Giả sử mã giảm giá có hạn 7 ngày
            'trang_thai' => 0, // Chưa sử dụng
        ]);

        // Giảm số lượng còn lại trong bảng countdown_vouchers
        $countdownVoucherModel = CountdownVoucher::find($request->countdownvoucher_id);
        if ($countdownVoucherModel && $countdownVoucherModel->so_luong_con_lai > 0) {
            $countdownVoucherModel->so_luong_con_lai -= 1;
            $countdownVoucherModel->save(); // Lưu lại sự thay đổi
        }
        // Kiểm tra nếu số lượng còn lại bằng 0 thì cập nhật trạng thái
        if ($countdownVoucherModel->so_luong_con_lai == 0) {
            $countdownVoucherModel->trang_thai = 1; // Đặt trạng thái thành 11
        }
        // Trả về Coupon Code vừa tạo cùng thông báo
        return response()->json([
            'message' => 'Săn mã giảm giá thành công!',
            'coupon' => $coupon,
        ], 201);
    }
}
