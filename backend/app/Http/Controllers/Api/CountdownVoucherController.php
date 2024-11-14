<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\CouponCodeTaken;
use App\Models\CountdownVoucher;
use App\Http\Controllers\Controller;
use Carbon\Carbon; // Để làm việc với ngày tháng

class CountdownVoucherController extends Controller
{
    public function index()
    {
        $countdownVouchers = CountdownVoucher::with('voucher')->get();
        return response()->json($countdownVouchers);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'magiamgia_id' => 'required|exists:vouchers,id',
            'ngay' => 'required|date|after_or_equal:today', // Ngày phải là hôm nay hoặc trong tương lai
            'thoi_gian_bat_dau' => 'required|date_format:H:i:s',
            'thoi_gian_ket_thuc' => 'required|date_format:H:i:s|after:thoi_gian_bat_dau',
            'so_luong' => 'required|integer|min:1',
            'so_luong_con_lai' => 'required|integer|min:0|max:' . $request->so_luong, // so_luong_con_lai phải nhỏ hơn hoặc bằng so_luong
            'trang_thai' => 'nullable|integer|in:0,1',
        ]);
    
        // Kiểm tra nếu ngày là hôm nay thì thoi_gian_bat_dau phải lớn hơn hoặc bằng thời gian hiện tại
        if (Carbon::parse($validated['ngay'])->isToday()) {
            $currentTime = Carbon::now()->format('H:i:s');
            // So sánh thời gian bắt đầu với thời gian hiện tại
            if (Carbon::parse($validated['thoi_gian_bat_dau'])->lt($currentTime)) {
                return response()->json([
                    'message' => 'Thời gian bắt đầu phải lớn hơn hoặc bằng thời gian hiện tại.'
                ], 400);
            }
        }
    
        // Kiểm tra thoi_gian_ket_thuc không vượt quá 23:59:59 và phải lớn hơn thoi_gian_bat_dau
        $maxEndTime = '23:59:59';
        if (Carbon::parse($validated['thoi_gian_ket_thuc'])->gt($maxEndTime)) {
            return response()->json([
                'message' => 'Thời gian kết thúc không được lớn hơn 23:59:59.'
            ], 400);
        }
    
        // Kiểm tra so_luong_con_lai không phải là null và phải nhỏ hơn hoặc bằng so_luong
        if ($validated['so_luong_con_lai'] > $validated['so_luong']) {
            return response()->json([
                'message' => 'Số lượng còn lại không được lớn hơn số lượng.'
            ], 400);
        }
    
        // Tạo CountdownVoucher mới
        $countdownVoucher = CountdownVoucher::create([
            'magiamgia_id' => $validated['magiamgia_id'],
            'ngay' => $validated['ngay'],
            'thoi_gian_bat_dau' => $validated['thoi_gian_bat_dau'],
            'thoi_gian_ket_thuc' => $validated['thoi_gian_ket_thuc'],
            'so_luong' => $validated['so_luong'],
            'so_luong_con_lai' => $validated['so_luong_con_lai'], // Đảm bảo trường này được lưu
            'trang_thai' => $validated['trang_thai'] ?? 0,
        ]);
    
        return response()->json([
            'message' => 'Tạo mã giảm giá thành công.',
            'data' => $countdownVoucher
        ], 201);
    }

    public function show($id)
    {
        $countdownVoucher = CountdownVoucher::with('voucher')->findOrFail($id);
        return response()->json($countdownVoucher);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'magiamgia_id' => 'required|exists:vouchers,id',
            'ngay' => 'required|date|after_or_equal:today', // Ngày phải là hôm nay hoặc trong tương lai
            'thoi_gian_bat_dau' => 'required|date_format:H:i:s',
            'thoi_gian_ket_thuc' => 'required|date_format:H:i:s|after:thoi_gian_bat_dau',
            'so_luong' => 'required|integer|min:1',
            'so_luong_con_lai' => 'nullable|integer|min:0|max:' . $request->so_luong, // so_luong_con_lai phải nhỏ hơn hoặc bằng so_luong
            'trang_thai' => 'nullable|integer|in:0,1',
        ]);

        // Kiểm tra nếu ngày là hôm nay thì thoi_gian_bat_dau phải lớn hơn hoặc bằng thời gian hiện tại
        if (Carbon::parse($validated['ngay'])->isToday()) {
            $currentTime = Carbon::now()->format('H:i:s');
            // So sánh thời gian bắt đầu với thời gian hiện tại
            if (Carbon::parse($validated['thoi_gian_bat_dau'])->lt($currentTime)) {
                return response()->json([
                    'message' => 'Thời gian bắt đầu phải lớn hơn hoặc bằng thời gian hiện tại.'
                ], 400);
            }
        }

        // Kiểm tra thoi_gian_ket_thuc không vượt quá 23:59:59 và phải lớn hơn thoi_gian_bat_dau
        $maxEndTime = '23:59:59';
        if (Carbon::parse($validated['thoi_gian_ket_thuc'])->gt($maxEndTime)) {
            return response()->json([
                'message' => 'Thời gian kết thúc không được lớn hơn 23:59:59.'
            ], 400);
        }

        // Kiểm tra so_luong_con_lai không phải là null và phải nhỏ hơn hoặc bằng so_luong
        if ($validated['so_luong_con_lai'] > $validated['so_luong']) {
            return response()->json([
                'message' => 'Số lượng còn lại không được lớn hơn số lượng.'
            ], 400);
        }

        $countdownVoucher = CountdownVoucher::findOrFail($id);
        $countdownVoucher->update([
            'magiamgia_id' => $validated['magiamgia_id'],
            'ngay' => $validated['ngay'],
            'thoi_gian_bat_dau' => $validated['thoi_gian_bat_dau'],
            'thoi_gian_ket_thuc' => $validated['thoi_gian_ket_thuc'],
            'so_luong' => $validated['so_luong'],
            'so_luong_con_lai' => $validated['so_luong_con_lai'] ?? $countdownVoucher->so_luong_con_lai,
            'trang_thai' => $validated['trang_thai'] ?? $countdownVoucher->trang_thai,
        ]);

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
