<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Food;
use App\Models\Room;
use App\Models\Movie;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Voucher;
use Illuminate\Http\Request;
use App\Models\RegisterMember;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\BookingDetail;

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

    public function thongKeDoanhThuPhim(Request $request, $id)
    {
        // Lấy tham số ngày/tháng/năm từ request
        $startDate = $request->input('start_date'); // Ngày bắt đầu
        $endDate = $request->input('end_date'); // Ngày kết thúc
        $trangThai = 'Đã hoàn thành';

        // Lấy tất cả các booking liên quan đến phim
        $bookings = Booking::join('showtimes', 'bookings.thongtinchieu_id', '=', 'showtimes.id')
            ->where('showtimes.phim_id', $id)
            ->select('bookings.*', 'showtimes.id as showtime_id')
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

        // Truy vấn tổng doanh thu từ bảng payments
        $query = Payment::query()
            ->whereIn('booking_id', $bookingIds) // Lọc theo booking_id
            ->where('trang_thai', $trangThai); // Lọc trạng thái thanh toán

        // Lọc theo thời gian nếu có tham số
        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay()
            ]);
        } elseif ($startDate) {
            $query->where('created_at', '>=', Carbon::parse($startDate)->startOfDay());
        } elseif ($endDate) {
            $query->where('created_at', '<=', Carbon::parse($endDate)->endOfDay());
        }

        // Tính tổng doanh thu
        $tongDoanhThu = $query->sum('tong_tien');

        return response()->json([
            'message' => 'Thống kê doanh thu phim thành công',
            'data' => $tongDoanhThu, // Trả về tổng doanh thu
        ], 200);
    }

    // Doanh thu theo từng phòng chiếu.

    public function doanhThuPhongChieu(Request $request, $id)
    {
        // Lấy tham số ngày/tháng/năm từ request
        $startDate = $request->input('start_date'); // Ngày bắt đầu
        $endDate = $request->input('end_date'); // Ngày kết thúc
        $trangThai = 'Đã hoàn thành';

        // Lọc các thanh toán liên quan đến room_id
        $query = Payment::query()
            ->join('bookings', 'payments.booking_id', '=', 'bookings.id')
            ->join('showtimes', 'bookings.thongtinchieu_id', '=', 'showtimes.id')
            ->join('rooms', 'showtimes.room_id', '=', 'rooms.id')
            ->where('rooms.id', $id) // Lọc theo phòng chiếu
            ->where('payments.trang_thai', $trangThai); // Lọc trạng thái thanh toán

        // Thêm lọc thời gian nếu có tham số
        if ($startDate && $endDate) {
            $query->whereBetween('payments.created_at', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay()
            ]);
        } elseif ($startDate) {
            $query->where('payments.created_at', '>=', Carbon::parse($startDate)->startOfDay());
        } elseif ($endDate) {
            $query->where('payments.created_at', '<=', Carbon::parse($endDate)->endOfDay());
        }

        // Tính tổng doanh thu
        $tongDoanhThu = $query->sum('payments.tong_tien');

        // Trả về kết quả
        return response()->json([
            'message' => 'Thống kê doanh thu phòng chiếu thành công',
            'data' => $tongDoanhThu,
        ], 200);
    }

    // phân loại người dùng

    public function phanLoaiNguoiDung()
    {
        // Đếm tổng số người dùng đã đăng ký
        $tongNguoiDung = RegisterMember::query()->count();

        // Phân loại người dùng dựa trên loại hội viên
        $phanLoai = RegisterMember::join('members', 'members.id', '=', 'register_members.hoivien_id')
            ->select('members.loai_hoi_vien', DB::raw('COUNT(register_members.hoivien_id) as so_luong'))
            ->groupBy('members.loai_hoi_vien') // Nhóm theo loại hội viên
            ->get();

        return response()->json([
            'message' => 'Phân loại người dùng thành công',
            'tongNguoiDangKy' => $tongNguoiDung,
            'data' => $phanLoai, // Danh sách loại hội viên và số lượng
        ], 200);
    }

    // thống kê số lượng vé 
    public function tinhTrangVe()
    {
        // Thống kê số lượng vé theo từng trạng thái
        $dangXuLy = Payment::query()->where('trang_thai', 'Đang chờ xử lý')->count();
        $thanhCong = Payment::query()->where('trang_thai', 'Đã hoàn thành')->count();
        $khongthanhCong = Payment::query()->where('trang_thai', 'Không thành công')->count();
        $hoanLai = Payment::query()->where('trang_thai', 'Đã hoàn lại')->count();
        $huy = Payment::query()->where('trang_thai', 'Hủy')->count();

        // 'Đang chờ xử lý','Đã hoàn thành','Không thành công','Đã hoàn lại','Đã hủy'
        return response()->json([
            'message' => 'Thống kê tình trạng vé thành công',
            'data' => [
                'dangXuLy' => $dangXuLy,
                'thanhCong' => $thanhCong,
                'khongthanhcong' => $khongthanhCong,
                'hoanlai' => $hoanLai,
                'huy' => $huy,
            ],
        ], 200);
    }

    // thống kê theo hình thức thanh toán

    public function hinhThucThanhToan()
    {

        // tien mặt

        $tienMat = Payment::query()->where('phuong_thuc_thanh_toan', 'cash')->count();

        $tongThanhToan =  Payment::query()->count('phuong_thuc_thanh_toan');

        $thanhToanOnline = $tongThanhToan - $tienMat;

        return response()->json([
            'message' => 'Thống kê hình thức thanh toán thành công',
            'data' => [
                'tienMat' => $tienMat,
                'thanhToanOnline' => $thanhToanOnline
            ],
        ], 200);
    }

    // top người mua vé

    public function topNguoiMuaVeNhieuNhat($limit = 5)
    {
        // Thống kê số lượng vé đã đặt của từng người
        $data = BookingDetail::join('bookings', 'booking_details.booking_id', '=', 'bookings.id')
            ->join('users', 'bookings.user_id', '=', 'users.id')
            ->select('users.ho_ten', 'users.email', DB::raw('COUNT(booking_details.booking_id) as total_tickets')) // tạo 1 cột total_tickets để tính tổng booking id có trong booking detail
            ->groupBy('users.id', 'users.ho_ten', 'users.email') // Nhóm theo người dùng
            ->orderBy('total_tickets', 'DESC') // Sắp xếp theo số lượng vé giảm dần
            ->limit($limit) // Lấy top N người
            ->get();

        // Kiểm tra nếu không có dữ liệu
        if ($data->isEmpty()) {
            return response()->json([
                'message' => 'Không có dữ liệu thống kê.',
                'data' => [],
            ], 404);
        }

        // Trả về kết quả
        return response()->json([
            'message' => 'Thống kê top người mua vé thành công',
            'data' => $data,
        ], 200);
    }

    // top phim có lượt đặt vé cao
    public function topPhimLuotveCao($limit = 5){
        $data = BookingDetail::join('bookings', 'booking_details.booking_id', '=', 'bookings.id')
        ->join('showtimes', 'bookings.thongtinchieu_id', '=', 'showtimes.id')
        ->join('movies', 'showtimes.phim_id', '=', 'movies.id')
        ->select('movies.ten_phim', 'movies.anh_phim', DB::raw('COUNT(showtimes.phim_id) as total_tickets')) // tạo 1 cột total_tickets để tính tổng booking id có trong booking detail
        ->groupBy('movies.id', 'movies.ten_phim', 'movies.anh_phim') // Nhóm theo người dùng
        ->orderBy('total_tickets', 'DESC') // Sắp xếp theo số lượng vé giảm dần
        ->limit($limit) // Lấy top N người
        ->get();


         // Kiểm tra nếu không có dữ liệu
         if ($data->isEmpty()) {
            return response()->json([
                'message' => 'Không có dữ liệu thống kê.',
                'data' => [],
            ], 404);
        }

        // Trả về kết quả
        return response()->json([
            'message' => 'Thống kê top vé phim thành công',
            'data' => $data,
        ], 200);
    }
}
