<?php

namespace App\Http\Controllers\Api;

use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class StatisticsController extends Controller
{
    /**
     * Thống kê tổng số mã trong bảng coupons
     */
    public function totalsCoupons()
    {
        try {
            // Đếm tổng số mã trong bảng coupons
            $tongSoMa = Coupon::count();

            return response()->json([
                'message' => 'Lấy tổng số mã coupon thành công.',
                'tong_so_ma' => $tongSoMa
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Đã xảy ra lỗi khi lấy tổng số mã coupon.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Thống kê tổng mã trong countdown_vouchers và coupon_code_takens
     */
    public function totalCoupons()
    {
        try {
            // Đếm tổng số mã đã tạo trong countdown_vouchers
            $totalCoupons = DB::table('countdown_vouchers')->count();
    
            // Đếm tổng số lượt nhận mã trong coupon_code_takens
            $totalClaimed = DB::table('coupon_code_takens')->count();
            // Tính tổng số lượng mã  trong countdown_vouchers
            $rtotalVouchers = DB::table('countdown_vouchers')->sum('so_luong');
    
            // Tính tổng số lượng mã còn lại trong countdown_vouchers
            $remainingVouchers = DB::table('countdown_vouchers')->sum('so_luong_con_lai');
    
            // Kiểm tra nếu không có dữ liệu nào trong bảng countdown_vouchers
            if ($totalCoupons === 0) {
                return response()->json([
                    'message' => 'Hiện tại không có mã giảm giá nào.',
                    'tong_ma_giam_gia_da_tao' => $totalCoupons,
                    'ma_da_san_thanh_cong' => $totalClaimed,
                    'tong_so_luong' => $rtotalVouchers,
                    'so_luong_ma_con_lai' => $remainingVouchers
                ], 200); // HTTP 200 - OK
            }
    
            // Trả về JSON response khi có dữ liệu
            return response()->json([
                'message' => 'Lấy thông kê tổng số mã giảm giá thành công.',
                'tong_ma_giam_gia_da_tao' => $totalCoupons,
                'ma_da_san_thanh_cong' => $totalClaimed,
                'tong_so_luong' => $rtotalVouchers,
                'so_luong_ma_con_lai' => $remainingVouchers
            ], 200); // HTTP 200 - OK
    
        } catch (\Exception $e) {
            // Xử lý lỗi nếu có
            return response()->json([
                'message' => 'Đã xảy ra lỗi khi lấy thông kê mã giảm giá.',
                'error' => $e->getMessage()
            ], 500); // HTTP 500 - Internal Server Error
        }
    }
}
