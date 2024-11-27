<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponsController extends Controller
{
    /**
     * Lấy danh sách các Coupon.
     */
    public function index()
    {
        $coupons = Coupon::all(); // Lấy tất cả bản ghi trong bảng 'coupons'

        if ($coupons->isEmpty()) {
            return response()->json([
                'message' => 'Không có dữ liệu mã giảm giá !',
            ], 200);
        }

        return response()->json([
            'message' => 'Xuất dữ liệu mã giảm giá  thành công',
            'data' => $coupons,
        ], 200);
    }

    /**
     * Thêm mới một Coupon.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ma_giam_gia' => 'required|string|max:255',
            'muc_giam_gia' => 'required|numeric|min:0',
            'mota' => 'required|string|max:255',
            'so_luong' => 'required|integer|min:1',
            'so_luong_da_su_dung' => 'nullable|integer|min:0',
            'gia_don_toi_thieu' => 'nullable|numeric|min:0',
            'trang_thai' => 'nullable|boolean',
        ]);

        $coupon = Coupon::create($validated); // Tạo mới Coupon

        return response()->json([
            'message' => 'Thêm mới mã giảm giá thành công',
            'data' => $coupon,
        ], 201);
    }

    /**
     * Lấy thông tin chi tiết một Coupon.
     */
    public function show(string $id)
    {
        $coupon = Coupon::find($id); // Tìm Coupon theo ID

        if (!$coupon) {
            return response()->json([
                'message' => 'Không có dữ liệu mã giảm theo ID này',
            ], 404);
        }

        return response()->json([
            'message' => 'Lấy thông tin mã giảm giá  thành công',
            'data' => $coupon,
        ], 200);
    }
}
