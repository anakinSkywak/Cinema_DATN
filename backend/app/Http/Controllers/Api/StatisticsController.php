<?php

namespace App\Http\Controllers\Api;

use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class StatisticsController extends Controller
{
    // thống kê tổng số mã giảm giá đã tạo 
    public function totalCoupons()
    {
        try {
            // Đếm tổng số mã giảm giá
            $totalCoupons = DB::table('countdown_vouchers')->count();
    
            // Đếm tổng số lượt nhận mã giảm giá
            $totalClaimed = DB::table('coupon_code_takens')->count();
    
            // Tính tổng số lượng mã giảm giá còn lại
            $remainingVouchers = DB::table('countdown_vouchers')->sum('so_luong_con_lai');
    
            // Kiểm tra nếu không có dữ liệu nào trong bảng coupons
            if ($totalCoupons === 0) {
                return response()->json([
                    'message' => 'Hiện tại không có mã giảm giá nào.',
                    'Mã giảm giá đã tạo' => $totalCoupons,
                    'mã đã săn thành công' => $totalClaimed,
                    'số lượng mã còn lại' => $remainingVouchers
                ], 200); // HTTP 200 - OK
            }
    
            // Trả về JSON response khi có dữ liệu
            return response()->json([
                'message' => 'Lấy tổng số mã giảm giá thành công.',
                'Mã giảm giá đã tạo' => $totalCoupons,
                'mã đã săn thành công' => $totalClaimed,
                'số lượng mã còn lại' => $remainingVouchers
            ], 200); // HTTP 200 - OK
    
        } catch (\Exception $e) {
            // Xử lý lỗi nếu có
            return response()->json([
                'message' => 'Đã xảy ra lỗi khi lấy thống kê mã giảm giá.',
                'error' => $e->getMessage()
            ], 500); // HTTP 500 - Internal Server Error
        }
    }
    
}
