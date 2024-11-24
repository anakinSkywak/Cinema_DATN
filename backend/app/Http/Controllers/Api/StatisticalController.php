<?php

namespace App\Http\Controllers\Api;

use App\Models\Food;
use App\Models\Movie;
use App\Models\Payment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Booking;

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
            'data' => $tongDoanhThu,
            'trang_thai' => $trangThai
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
            'tongTienDoAn' => $tongTienDoAn
        ], 200);
    }
}
