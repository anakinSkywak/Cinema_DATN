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
        // Kiểm tra người dùng đã đăng nhập hay chưa
        if (!Auth::check()) {
            return response()->json(['message' => 'Bạn cần đăng nhập để săn mã giảm giá'], 401);
        }

        // Xác nhận dữ liệu từ request
        $request->validate([
            'countdownvoucher_id' => 'required|integer|exists:countdown_vouchers,id',
        ], [
            'countdownvoucher_id.required' => 'ID mã giảm giá là bắt buộc.',
            'countdownvoucher_id.integer' => 'ID mã giảm giá phải là số nguyên.',
            'countdownvoucher_id.exists' => 'ID mã giảm giá không tồn tại trong hệ thống.',
        ]);

        $user = Auth::user();

        $countdownVoucher = CountdownVoucher::where('id', $request->countdownvoucher_id)
            ->where('trang_thai', 0)
            ->where('so_luong_con_lai', '>', 0)
            ->whereDate('ngay', '=', Carbon::today()->toDateString())
            ->whereTime('thoi_gian_bat_dau', '<=', Carbon::now())
            ->whereTime('thoi_gian_ket_thuc', '>=', Carbon::now())
            ->first();

        if (!$countdownVoucher) {
            return response()->json(['message' => 'Mã giảm giá không còn khả dụng hoặc hết thời gian.'], 400);
        }

        $existingCoupon = CouponCodeTaken::where([
            ['user_id', '=', $user->id],
            ['countdownvoucher_id', '=', $request->countdownvoucher_id]
        ])->first();

        if ($existingCoupon) {
            return response()->json(['message' => 'Bạn đã nhận mã giảm giá này rồi.'], 400);
        }

        DB::beginTransaction();

        try {
            $coupon = CouponCodeTaken::create([
                'countdownvoucher_id' => $request->countdownvoucher_id,
                'user_id' => $user->id,
                'ngay_nhan' => Carbon::now(),
                'ngay_het_han' => Carbon::now()->addDays(7), // Giả sử mã giảm giá có hạn 7 ngày
                'trang_thai' => 0, // Chưa sử dụng
            ]);

            $countdownVoucher = CountdownVoucher::find($request->countdownvoucher_id);

            if ($countdownVoucher && $countdownVoucher->so_luong_con_lai > 0) {
                $countdownVoucher->so_luong_con_lai -= 1;
                $countdownVoucher->save();
            }

            DB::commit();

            return response()->json([
                'message' => 'Săn mã giảm giá thành công!',
                'coupon' => $coupon,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Đã xảy ra lỗi khi săn mã giảm giá.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
