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
    public function showTodayDiscounts()
    {
        // Lấy ngày hôm nay theo giờ Việt Nam
        $today = Carbon::now('Asia/Ho_Chi_Minh')->toDateString();  // Lấy ngày theo định dạng yyyy-mm-dd
    
        // Truy vấn các mã giảm giá có trạng thái = 0 và ngày là hôm nay
        $countdownVouchers = CountdownVoucher::where('trang_thai', 0)
            ->whereDate('ngay', $today)
            ->with('coupon') // Nếu bạn cần lấy thông tin liên quan đến coupon
            ->get();
    
        // Trả về kết quả
        return response()->json($countdownVouchers);
    }
    public function getCoupons()
    {
        // Lấy tất cả mã giảm giá từ bảng coupons
        $coupons = Coupon::select('id', 'ma_giam_gia')->get();
    
        // Trả về dữ liệu JSON
        return response()->json($coupons);
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
        ], [
            'magiamgia_id.required' => 'Mã giảm giá không được để trống.',
            'magiamgia_id.exists' => 'Mã giảm giá không tồn tại.',
            'ngay.required' => 'Ngày không được để trống.',
            'ngay.date' => 'Ngày phải là định dạng ngày hợp lệ.',
            'ngay.after_or_equal' => 'Ngày phải từ hôm nay trở đi.',
            'thoi_gian_bat_dau.required' => 'Thời gian bắt đầu không được để trống.',
            'thoi_gian_bat_dau.date_format' => 'Thời gian bắt đầu phải đúng định dạng H:i:s.',
            'thoi_gian_ket_thuc.required' => 'Thời gian kết thúc không được để trống.',
            'thoi_gian_ket_thuc.date_format' => 'Thời gian kết thúc phải đúng định dạng H:i:s.',
            'thoi_gian_ket_thuc.after' => 'Thời gian kết thúc phải sau thời gian bắt đầu.',
            'so_luong.required' => 'Số lượng không được để trống.',
            'so_luong.integer' => 'Số lượng phải là một số nguyên.',
            'so_luong.min' => 'Số lượng phải lớn hơn hoặc bằng 1.',
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
            'so_luong_con_lai' => 'required|integer|min:0|lte:so_luong',
            'trang_thai' => 'nullable|integer|in:0,1',
        ]);

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
