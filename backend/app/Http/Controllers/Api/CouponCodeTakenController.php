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
        // Kiểm tra xem người dùng đã đăng nhập chưa
        if (!Auth::check()) {
            return response()->json(['message' => 'Bạn cần đăng nhập để xem mã giảm giá'], 401);
        }

        $user = Auth::user(); // Lấy thông tin người dùng đang đăng nhập

        // Lấy ngày hiện tại
        $currentDate = Carbon::now(); // Sử dụng Carbon để lấy ngày hiện tại

        // Truy vấn với join để kết hợp các bảng
        $coupons = DB::table('coupon_code_takens')  // Bảng coupon_code_takens
            ->join('countdown_vouchers', 'coupon_code_takens.countdownvoucher_id', '=', 'countdown_vouchers.id') // Kết nối với countdown_vouchers
            ->join('users', 'coupon_code_takens.user_id', '=', 'users.id')  // Kết nối với bảng users
            ->join('coupons', 'countdown_vouchers.magiamgia_id', '=', 'coupons.id') // Kết nối với bảng coupons (thay vì vouchers)
            ->where('coupon_code_takens.user_id', $user->id)  // Lọc theo user_id
            ->select(
                'users.ho_ten as user_name',  // Tên người dùng
                'coupons.ma_giam_gia',  // Mã giảm giá
                'coupons.muc_giam_gia',  // Mức giảm giá
                'coupons.Giam_max',  // Mức giảm giá không quá 
                'coupons.gia_don_toi_thieu',  //  giá tối thiểu để áp dụng mã 
                'coupons.mota',  // Mô tả mã giảm giá
                'coupon_code_takens.ngay_nhan',  // Ngày nhận
                'coupon_code_takens.ngay_het_han', // Ngày hết hạn
                DB::raw('IF(coupon_code_takens.ngay_het_han < "' . $currentDate . '" , "Hết hạn", "Còn hạn") as status') // Kiểm tra nếu mã đã hết hạn
            )
            ->get();
        // Xóa các mã giảm giá đã hết hạn hơn 2 ngày
        foreach ($coupons as $coupon) {
            // Kiểm tra nếu mã đã hết hạn hơn 2 ngày
            $expiredDate = Carbon::parse($coupon->ngay_het_han);
            if ($expiredDate->lt($currentDate->subDays(2))) {
                // Xóa mã giảm giá của người dùng
                DB::table('coupon_code_takens')
                    ->where('id', $coupon->id)
                    ->delete();
            }
        }

        return response()->json($coupons);  // Trả về kết quả dưới dạng JSON
    }

    public function spinVoucher(Request $request)
    {
        // Kiểm tra xem người dùng đã đăng nhập chưa
        if (!Auth::check()) {
            return response()->json(['message' => 'Bạn cần đăng nhập để săn mã giảm giá'], 401);
        }

        $user = Auth::user(); // Lấy thông tin người dùng đang đăng nhập

        // Kiểm tra xem mã giảm giá còn khả dụng không
        $countdownVoucher = CountdownVoucher::where('id', $request->countdownvoucher_id) // Kiểm tra mã giảm giá cụ thể
            ->where('trang_thai', 0) // Kiểm tra trạng thái là 0
            ->where('so_luong_con_lai', '>', 0) // Kiểm tra số lượng còn lại > 0
            ->whereDate('ngay', '>=', Carbon::today()->toDateString()) // Ngày phải là hôm nay hoặc tương lai
            ->whereTime('thoi_gian_bat_dau', '<=', Carbon::now()) // Thời gian bắt đầu phải trước hoặc bằng hiện tại
            ->whereTime('thoi_gian_ket_thuc', '>=', Carbon::now()) // Thời gian kết thúc phải sau hiện tại
            ->first();
        // Kiểm tra nếu không tìm thấy voucher
        if (!$countdownVoucher) {
            return response()->json(['message' => 'Mã giảm giá không còn khả dụng hoặc hết thời gian.'], 400);
        }

        // Lưu lại đối tượng
        $countdownVoucher->save();

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
            return response()->json([
                'message' => 'Bạn đã nhận mã giảm giá này rồi.'
            ], 400);
        }
        // Tạo bản ghi CouponCodeTaken mới
        $coupon = CouponCodeTaken::create([
            'countdownvoucher_id' => $request->countdownvoucher_id,
            'user_id' => $user->id, // Dùng id của người dùng đã đăng nhập
        ]);
        // Sau khi thêm mã giảm giá thành công, giảm so_luong_con_lai trong bảng countdown_vouchers
        $countdownVoucher = CountdownVoucher::find($request->countdownvoucher_id);

        if ($countdownVoucher && $countdownVoucher->so_luong_con_lai > 0) {
            $countdownVoucher->so_luong_con_lai -= 1;
            $countdownVoucher->save(); // Lưu lại sự thay đổi
        }
        // Trả về Coupon Code vừa tạo cùng thông báo
        return response()->json([
            'message' => 'Săn mã giảm giá thành công!',
            'coupon' => $coupon,
        ], 201);
    }
}
