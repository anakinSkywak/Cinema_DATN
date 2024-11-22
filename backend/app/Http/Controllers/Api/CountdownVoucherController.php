<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Voucher;
use Illuminate\Http\Request;
use App\Models\CountdownVoucher;
use App\Http\Controllers\Controller;

class CountdownVoucherController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        // Cập nhật trạng thái tự động
        CountdownVoucher::where('ngay', '<', $today)->update(['trang_thai' => 1]); // Hết hạn
        CountdownVoucher::where('ngay', '=', $today)->update(['trang_thai' => 0]); // Đang hoạt động
        CountdownVoucher::where('ngay', '>', $today)->update(['trang_thai' => 0]); // Chưa bắt đầu

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
            'trang_thai' => 'nullable|integer|in:0,1',
        ]);
        
        // Lấy số lượng từ bảng 'vouchers' dựa trên 'magiamgia_id'
        $voucher = Voucher::find($validated['magiamgia_id']);
        
        if ($validated['so_luong'] > $voucher->so_luong) {
            return response()->json([
                'message' => 'Số lượng trong countdown voucher không thể lớn hơn số lượng trong voucher.',
            ], 400);
        }
        
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
        // Tạo CountdownVoucher mới, so_luong_con_lai tự động bằng so_luong
        $countdownVoucher = CountdownVoucher::create([
            'magiamgia_id' => $validated['magiamgia_id'],
            'ngay' => $validated['ngay'],
            'thoi_gian_bat_dau' => $validated['thoi_gian_bat_dau'],
            'thoi_gian_ket_thuc' => $validated['thoi_gian_ket_thuc'],
            'so_luong' => $validated['so_luong'],
            'so_luong_con_lai' => $validated['so_luong'],
            'trang_thai' => 0, // Mặc định trạng thái hoạt động
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
            'ngay' => 'required|date|after_or_equal:today', // Ngày phải là hôm nay hoặc tương lai
            'thoi_gian_bat_dau' => 'required|date_format:H:i:s',
            'thoi_gian_ket_thuc' => 'required|date_format:H:i:s|after:thoi_gian_bat_dau',
            'so_luong' => 'required|integer|min:1',
            'so_luong_con_lai' => 'nullable|integer|min:0',
            'trang_thai' => 'nullable|integer|in:0,1',
        ]);
    
        // Lấy số lượng từ bảng 'vouchers'
        $voucher = Voucher::find($validated['magiamgia_id']);
        if ($validated['so_luong'] > $voucher->so_luong) {
            return response()->json([
                'message' => 'Số lượng trong countdown voucher không thể lớn hơn số lượng trong voucher.',
            ], 400);
        }
    
        // Kiểm tra số lượng còn lại không được lớn hơn tổng số lượng
        if (isset($validated['so_luong_con_lai']) && $validated['so_luong_con_lai'] > $validated['so_luong']) {
            return response()->json([
                'message' => 'Số lượng còn lại không thể lớn hơn tổng số lượng.',
            ], 400);
        }
    
        // Kiểm tra nếu ngày là hôm nay thì `thoi_gian_bat_dau` phải lớn hơn hoặc bằng thời gian hiện tại
        if (Carbon::parse($validated['ngay'])->isToday()) {
            $currentTime = Carbon::now()->format('H:i:s');
            if (Carbon::parse($validated['thoi_gian_bat_dau'])->lt($currentTime)) {
                return response()->json([
                    'message' => 'Thời gian bắt đầu phải lớn hơn hoặc bằng thời gian hiện tại (' . $currentTime . ').'
                ], 400);
            }
        }
    
        // Kiểm tra `thoi_gian_ket_thuc` không vượt quá 23:59:59
        $maxEndTime = '23:59:59';
        if (Carbon::parse($validated['thoi_gian_ket_thuc'])->gt($maxEndTime)) {
            return response()->json([
                'message' => 'Thời gian kết thúc không được lớn hơn 23:59:59.',
            ], 400);
        }
    
        // Cập nhật dữ liệu trong CountdownVoucher
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
