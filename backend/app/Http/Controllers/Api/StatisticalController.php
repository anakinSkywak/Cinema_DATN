<?php

namespace App\Http\Controllers\Api;

use App\Models\Food;
use App\Models\Movie;
use App\Models\Payment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Voucher;

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

    // thông kê doanh thu bán vé
    public function doanhThuBanVe()
    {
        // Chỉ tính doanh thu từ các thanh toán có trạng thái "Đã hoàn thành"
        $trangThai = 'Đã hoàn thành';
        $tongDoanhThu = Payment::query()
            ->where('trang_thai', $trangThai)
            ->sum('tong_tien');

        // Kiểm tra nếu doanh thu bằng 0
        if ($tongDoanhThu === 0) {
            return response()->json([
                'message' => "Không có doanh thu nào được ghi nhận cho trạng thái: $trangThai",
                'data' => $tongDoanhThu
            ], 404);
        }

        // Trả về doanh thu khi có dữ liệu
        return response()->json([
            'message' => "Thống kê doanh thu bán vé thành công",
            'data' => $tongDoanhThu
        ], 200);
    }

    // doanh thu đồ ăn 
    public function doanhThuDoAn()
    {
        // Lấy danh sách ID thanh toán thành công
        $trangThai = 'Đã hoàn thành';
        $idThanhToanThanhCong = Payment::query()
            ->where('trang_thai', $trangThai)
            ->pluck('booking_id'); // Trả về một mảng các ID

        // Lấy các booking kèm thông tin món ăn, lọc theo ID thanh toán thành công
        $bookings = Booking::query()
            ->with('food') // Quan hệ tới bảng foods
            ->whereIn('id', $idThanhToanThanhCong) // Lọc booking theo payment_id
            ->get();

        // Tính tổng doanh thu
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
            // $bookings = Booking::all();

        // Kiểm tra nếu không có booking nào
        if ($bookings->isEmpty()) {
            return response()->json([
                'message' => 'Không tìm thấy doanh thu cho phim này',
                'data' => 0,
            ], 404);
        }

        // Lấy danh sách ID booking
        $bookingIds = $bookings->pluck('id');

        // Tính tổng doanh thu từ bảng payments'

        $trangThai = 'Đã hoàn thành';
        $tongDoanhThu = Payment::query()
            ->where('trang_thai', $trangThai)
            // ->whereIn('id', $bookingIds) // Sử dụng whereIn để kiểm tra danh sách ID
            ->sum('tong_tien');

        return response()->json([
            'message' => 'Thống kê doanh thu phim thành công',
            'data' => $tongDoanhThu,
        ], 200);
    }
}
