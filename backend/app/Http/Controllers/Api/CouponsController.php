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
                'message' => 'Không có dữ liệu mã giảm giá!',
            ], 200);
        }

        return response()->json([
            'message' => 'Xuất dữ liệu mã giảm giá thành công',
            'data' => $coupons,
        ], 200);
    }

    /**
     * Thêm mới một Coupon.
     */
    public function store(Request $request)
    {
        // Validate dữ liệu đầu vào
        $validated = $request->validate([
            'ma_giam_gia' => 'required|string|max:255',
            'muc_giam_gia' => 'required|numeric|min:0|max:100',
            'gia_don_toi_thieu' => 'required|numeric|min:0',
            'Giam_max' => 'required|numeric|min:0',
            'mota' => 'required|string|max:255',
            'so_luong_da_su_dung' => 'nullable|integer|min:0',
            'trang_thai' => 'nullable|boolean',
        ], [
            'ma_giam_gia.required' => 'Mã giảm giá không được để trống.',
            'ma_giam_gia.max' => 'Mã giảm giá không được vượt quá 255 ký tự.',
            'muc_giam_gia.required' => 'Mức giảm giá không được để trống.',
            'muc_giam_gia.numeric' => 'Mức giảm giá phải là một số.',
            'muc_giam_gia.min' => 'Mức giảm giá không được nhỏ hơn 0.',
            'muc_giam_gia.max' => 'Mức giảm giá không được lớn hơn 100.',
            'gia_don_toi_thieu.required' => 'Giá đơn tối thiểu không được để trống.',
            'gia_don_toi_thieu.numeric' => 'Giá đơn tối thiểu phải là một số.',
            'gia_don_toi_thieu.min' => 'Giá đơn tối thiểu không được nhỏ hơn 0.',
            'Giam_max.required' => 'Giảm tối đa không được để trống.',
            'Giam_max.numeric' => 'Giảm tối đa phải là một số.',
            'Giam_max.min' => 'Giảm tối đa không được nhỏ hơn 0.',
            'mota.required' => 'Mô tả không được để trống.',
            'mota.max' => 'Mô tả không được vượt quá 255 ký tự.',
            'so_luong_da_su_dung.integer' => 'Số lượng đã sử dụng phải là số nguyên.',
            'so_luong_da_su_dung.min' => 'Số lượng đã sử dụng không được nhỏ hơn 0.',
        ]);

        // Kiểm tra xem mã giảm giá đã tồn tại chưa
        $existingCoupon = Coupon::where('ma_giam_gia', $validated['ma_giam_gia'])->first();
        if ($existingCoupon) {
            return response()->json([
                'message' => 'Mã giảm giá với tên này đã tồn tại. Vui lòng chọn mã khác.',
            ], 400); // Mã trạng thái HTTP 400 - Bad Request
        }
        // Tạo mới Coupon
        $coupon = Coupon::create($validated);

        return response()->json([
            'message' => 'Thêm mới mã giảm giá thành công',
            'data' => $coupon,
        ], 201); // Mã trạng thái HTTP 201 - Created
    }

    /**
     * Lấy thông tin chi tiết một Coupon.
     */
    public function show(string $id)
    {
        $coupon = Coupon::find($id); // Tìm Coupon theo ID

        if (!$coupon) {
            return response()->json([
                'message' => 'Không có dữ liệu mã giảm giá theo ID này',
            ], 404);
        }

        return response()->json([
            'message' => 'Lấy thông tin mã giảm giá thành công',
            'data' => $coupon,
        ], 200);
    }

    /**
     * Cập nhật thông tin một Coupon.
     */
    public function update(Request $request, string $id)
    {
        // Tìm Coupon theo ID
        $coupon = Coupon::find($id);

        if (!$coupon) {
            return response()->json([
                'message' => 'Không có dữ liệu mã giảm giá theo ID này',
            ], 404); // Mã trạng thái HTTP 404 - Not Found
        }
        // Validate dữ liệu đầu vào
        $validated = $request->validate([
            'ma_giam_gia' => 'required|string|max:255',
            'muc_giam_gia' => 'required|numeric|min:0|max:100',
            'gia_don_toi_thieu' => 'required|numeric|min:0',
            'Giam_max' => 'required|numeric|min:0',
            'mota' => 'required|string|max:255',

            'so_luong_da_su_dung' => 'nullable|integer|min:0',
            'trang_thai' => 'nullable|boolean',
        ], [
            'ma_giam_gia.required' => 'Mã giảm giá không được để trống.',
            'ma_giam_gia.max' => 'Mã giảm giá không được vượt quá 255 ký tự.',
            'muc_giam_gia.required' => 'Mức giảm giá không được để trống.',
            'muc_giam_gia.numeric' => 'Mức giảm giá phải là một số.',
            'muc_giam_gia.min' => 'Mức giảm giá không được nhỏ hơn 0.',
            'muc_giam_gia.max' => 'Mức giảm giá không được lớn hơn 100.',
            'gia_don_toi_thieu.required' => 'Giá đơn tối thiểu không được để trống.',
            'gia_don_toi_thieu.numeric' => 'Giá đơn tối thiểu phải là một số.',
            'gia_don_toi_thieu.min' => 'Giá đơn tối thiểu không được nhỏ hơn 0.',
            'Giam_max.required' => 'Giảm tối đa không được để trống.',
            'Giam_max.numeric' => 'Giảm tối đa phải là một số.',
            'Giam_max.min' => 'Giảm tối đa không được nhỏ hơn 0.',
            'mota.required' => 'Mô tả không được để trống.',
            'mota.max' => 'Mô tả không được vượt quá 255 ký tự.',
            'so_luong_da_su_dung.integer' => 'Số lượng đã sử dụng phải là số nguyên.',
            'so_luong_da_su_dung.min' => 'Số lượng đã sử dụng không được nhỏ hơn 0.',

        ]);

        // Kiểm tra mã giảm giá đã tồn tại (trừ mã hiện tại)
        if (isset($validated['ma_giam_gia'])) {
            $existingCoupon = Coupon::where('ma_giam_gia', $validated['ma_giam_gia'])
                ->where('id', '!=', $id)
                ->first();
            if ($existingCoupon) {
                return response()->json([
                    'message' => 'Mã giảm giá với tên này đã tồn tại. Vui lòng chọn mã khác.',
                ], 400); // Mã trạng thái HTTP 400 - Bad Request
            }
        }

        // Cập nhật dữ liệu
        $coupon->update($validated);

        return response()->json([
            'message' => 'Cập nhật dữ liệu mã giảm giá thành công',
            'data' => $coupon,
        ], 200); // Mã trạng thái HTTP 200 - OK
    }

    /**
     * Xóa một Coupon.
     */
    public function destroy(string $id)
    {
        $coupon = Coupon::find($id); // Tìm Coupon theo ID

        if (!$coupon) {
            return response()->json([
                'message' => 'Không có dữ liệu mã giảm giá theo ID này',
            ], 404);
        }

        $coupon->delete(); // Xóa Coupon

        return response()->json([
            'message' => 'Xóa mã giảm giá thành công',
        ], 200);
    }
    // thống kê 
    public function totalCoupons()
    {
        // Đếm tổng số mã giảm giá
        $totalCoupons = Coupon::count();

        // Trả về JSON response
        return response()->json([
            'message' => 'Lấy tổng số mã giảm giá thành công',
            'total_coupons' => $totalCoupons
        ], 200); // Mã trạng thái HTTP 200 - OK
    }
}
