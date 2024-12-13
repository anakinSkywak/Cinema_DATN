<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Coupon; // Thay đổi từ Voucher thành Coupon
use Illuminate\Http\Request;
use App\Models\CountdownVoucher;
use App\Http\Controllers\Controller;

class CountdownVoucherController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        CountdownVoucher::where('ngay', '<', $today)->update(['trang_thai' => 1]); // Hết hạn
        CountdownVoucher::where('ngay', '=', $today)->update(['trang_thai' => 0]); // Đang hoạt động
        CountdownVoucher::where('ngay', '>', $today)->update(['trang_thai' => 0]); // Chưa bắt đầu

        $countdownVouchers = CountdownVoucher::with('coupon')->get(); // Thay đổi voucher thành coupon

        return response()->json($countdownVouchers);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'magiamgia_id' => 'required|exists:coupons,id',
            'ngay' => 'required|date|after_or_equal:today',
            'thoi_gian_bat_dau' => 'required|date_format:H:i:s',
            'thoi_gian_ket_thuc' => 'required|date_format:H:i:s|after:thoi_gian_bat_dau',
            'so_luong' => 'required|integer|min:1',
            'trang_thai' => 'nullable|integer|in:0,1',
        ]);

        $validated['so_luong_con_lai'] = $validated['so_luong'];

        if (Carbon::parse($validated['ngay'])->isToday()) {
            $currentTime = Carbon::now()->format('H:i:s');
            if (Carbon::parse($validated['thoi_gian_bat_dau'])->lt($currentTime)) {
                return response()->json([
                    'message' => 'Thời gian bắt đầu phải lớn hơn hoặc bằng thời gian hiện tại.'
                ], 400);
            }
        }

        $maxEndTime = '23:59:59';
        if (Carbon::parse($validated['thoi_gian_ket_thuc'])->gt($maxEndTime)) {
            return response()->json([
                'message' => 'Thời gian kết thúc không được lớn hơn 23:59:59.'
            ], 400);
        }

        $countdownVoucher = CountdownVoucher::create($validated);

        return response()->json([
            'message' => 'Tạo mã giảm giá thành công.',
            'data' => $countdownVoucher
        ], 201);
    }

    public function show($id)
    {
        $countdownVoucher = CountdownVoucher::with('coupon')->findOrFail($id);
        return response()->json($countdownVoucher);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'magiamgia_id' => 'required|exists:coupons,id',
            'ngay' => 'required|date|after_or_equal:today',
            'thoi_gian_bat_dau' => 'required|date_format:H:i:s',
            'thoi_gian_ket_thuc' => 'required|date_format:H:i:s|after:thoi_gian_bat_dau',
            'so_luong' => 'required|integer|min:1',
            'trang_thai' => 'nullable|integer|in:0,1',
        ]);

        $validated['so_luong_con_lai'] = $validated['so_luong'];

        if (Carbon::parse($validated['ngay'])->isToday()) {
            $currentTime = Carbon::now()->format('H:i:s');
            if (Carbon::parse($validated['thoi_gian_bat_dau'])->lt($currentTime)) {
                return response()->json([
                    'message' => 'Thời gian bắt đầu phải lớn hơn hoặc bằng thời gian hiện tại.'
                ], 400);
            }
        }

        $maxEndTime = '23:59:59';
        if (Carbon::parse($validated['thoi_gian_ket_thuc'])->gt($maxEndTime)) {
            return response()->json([
                'message' => 'Thời gian kết thúc không được lớn hơn 23:59:59.'
            ], 400);
        }

        $countdownVoucher = CountdownVoucher::findOrFail($id);
        $countdownVoucher->update($validated);

        return response()->json([
            'message' => 'Cập nhật mã giảm giá thành công.',
            'data' => $countdownVoucher
        ]);
    }

    public function destroy($id)
    {
        $countdownVoucher = CountdownVoucher::findOrFail($id);
        $countdownVoucher->delete();

        return response()->json(['message' => 'Xóa mã giảm giá thành công.']);
    }
}