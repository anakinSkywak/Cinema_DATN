<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Food;
use App\Models\Movie;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Voucher;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

// chức năng thống kê
class StatisticalController extends Controller
{
    // thống kê số lượng phim
    public function soLuongPhim()
    {
        $data = Movie::query()->count(); // Truy vấn trực tiếp số lượng phim

        if ($data === 0) {
            return response()->json([
                'message' => "Không có phim nào trong cơ sở dữ liệu",
                'data' => $data
            ], 404);
        }

        return response()->json([
            'message' => "Lấy tổng số lượng phim thành công",
            'data' => $data
        ], 200);
    }

    // thông kê doanh thu bán vé, theo ngày,tuần,tháng,năm

    public function doanhThuBanVe(Request $request)
    {
        // Lấy trạng thái thanh toán và thời gian từ request
        $trangThai = 'Đã hoàn thành';
        $startDate = $request->input('start_date'); // Ngày bắt đầu
        $endDate = $request->input('end_date'); // Ngày kết thúc

        // Tạo truy vấn cơ bản
        $query = Payment::query()->where('trang_thai', $trangThai);

        // Nếu người dùng nhập ngày bắt đầu hoặc kết thúc
        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay()
            ]);
        } elseif ($startDate) { // Chỉ nhập ngày bắt đầu
            $query->where('created_at', '>=', Carbon::parse($startDate)->startOfDay());
        } elseif ($endDate) { // Chỉ nhập ngày kết thúc
            $query->where('created_at', '<=', Carbon::parse($endDate)->endOfDay());
        }

        // Tính tổng doanh thu
        $tongDoanhThu = $query->sum('tong_tien');

        // Trả về doanh thu khi có dữ liệu
        return response()->json([
            'message' => "Thống kê doanh thu bán vé thành công",
            'data' => $tongDoanhThu
        ], 200);
    }


//  nhập thời gian để thống kê doanh thu theo thời gian
    public function doanhThuDoAn(Request $request)
    {
        // Lấy trạng thái thanh toán và tham số thời gian từ request
        $trangThai = 'Đã hoàn thành';
        $startDate = $request->input('start_date'); // Ngày bắt đầu
        $endDate = $request->input('end_date'); // Ngày kết thúc
    
        // Truy vấn các booking_id từ bảng payments
        $query = Payment::query()
            ->where('trang_thai', $trangThai);
    
        // Lọc theo khoảng thời gian nếu có tham số
        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay()
            ]);
        } elseif ($startDate) { // Chỉ có ngày bắt đầu
            $query->where('created_at', '>=', Carbon::parse($startDate)->startOfDay());
        } elseif ($endDate) { // Chỉ có ngày kết thúc
            $query->where('created_at', '<=', Carbon::parse($endDate)->endOfDay());
        }
    
        // Lấy danh sách ID booking từ các thanh toán thành công
        $idThanhToanThanhCong = $query->pluck('booking_id');
    
        // Truy vấn các booking kèm thông tin món ăn
        $bookings = Booking::query()
            ->with('food') // Quan hệ với bảng foods
            ->whereIn('id', $idThanhToanThanhCong) // Lọc booking theo các ID đã thanh toán thành công
            ->get();
    
        // Tính tổng doanh thu từ đồ ăn
        $tongTienDoAn = 0;
        foreach ($bookings as $booking) {
            if ($booking->food) {
                $tongTienDoAn += $booking->so_luong_do_an * $booking->food->gia;
            }
        }
    
        return response()->json([
            'message' => 'Thống kê doanh thu đồ ăn thành công',
            'data' => $tongTienDoAn
        ], 200);
    }
    

    // thông kê số lượng voucher người dùng lấy được
    public function thongKeSoLuongVoucher()
    {
        // Lấy danh sách voucher
        $vouchers = Voucher::query()->get(['muc_giam_gia', 'so_luong', 'so_luong_da_su_dung']);

        // Duyệt qua từng voucher và tính số lượng còn lại
        $voucherThongKe = $vouchers->map(function ($voucher) {
            return [
                'muc_giam_gia' => $voucher->muc_giam_gia,
                'so_luong' => $voucher->so_luong,
                'so_luong_da_su_dung' => $voucher->so_luong_da_su_dung ?? 0,
                'so_luong_con_lai' => $voucher->so_luong - ($voucher->so_luong_da_su_dung ?? 0),
            ];
        });

        return response()->json([
            'message' => 'Thống kê số lượng voucher thành công',
            'data' => $voucherThongKe,
        ], 200);
    }

    // nhập id phim để xem doanh thu
    public function thongKeDoanhThuPhim($id)
    {
        // Lấy tất cả các booking liên quan đến phim
        $bookings = Booking::join('showtimes', 'bookings.thongtinchieu_id', '=', 'showtimes.id')
            ->where('showtimes.phim_id', $id)
            ->select('bookings.*', 'showtimes.id as showtime_id')  // Alias cho showtimes.id
            ->get();

        // Kiểm tra nếu không có booking nào
        if ($bookings->isEmpty()) {
            return response()->json([
                'message' => 'Không tìm thấy doanh thu cho phim này',
                'data' => 0,
            ], 404);
        }

        // Lấy danh sách ID booking
        $bookingIds = $bookings->pluck('id');

        // Tính tổng doanh thu từ bảng payments
        $trangThai = 'Đã hoàn thành';
        $tongDoanhThu = Payment::query()
            ->whereIn('booking_id', $bookingIds) // Sử dụng whereIn để kiểm tra danh sách ID
            ->where('trang_thai', $trangThai) // Kiểm tra trạng thái thanh toán
            ->sum('tong_tien'); // Tính tổng doanh thu

        return response()->json([
            'message' => 'Thống kê doanh thu phim thành công',
            'data' => $tongDoanhThu, // Trả về tổng doanh thu
        ], 200);
    }
}
