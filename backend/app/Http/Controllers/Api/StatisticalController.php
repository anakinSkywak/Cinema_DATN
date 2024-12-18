<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Food;
use App\Models\Room;
use App\Models\Seat;
use App\Models\Movie;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Voucher;
use App\Models\Showtime;
use Illuminate\Http\Request;
use App\Models\BookingDetail;
use App\Models\RegisterMember;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class StatisticalController extends Controller
{
    /**
     * 1. Thống kê doanh thu linh hoạt theo loại
     */
    public function thongKeDoanhThu(Request $request, $type = 've', $id = null)
    {
        try {

            //            payment // 0 Đang chờ xử lý , 1 Đã hoàn thành  2 Không thành công  , 3 Đã hủy, 4 Đã hoàn lại
            //            Booking   // 0 Chưa thanh toán , 1 là Đã thanh toán , 2 Đã hủy đơn , 3 Lỗi đơn hàng ,

            $trangThai = 1;
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');

            // Khởi tạo query cơ bản và chỉ rõ trang_thai thuộc bảng payments
            $query = Payment::query()->where('payments.trang_thai', $trangThai);

            // Áp dụng bộ lọc ngày tháng
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

            // Xử lý theo từng loại thống kê
            // match là một cú pháp mới của PHP 8.0, được sử dụng để thực hiện các so sánh và trả về giá trị dựa trên các điều kiện khác nhau.
            $ketQua = match ($type) {
                'do_an' => $this->tinhDoanhThuDoAn(
                    Booking::whereIn('id', $query->pluck('booking_id'))->get()
                ),
                'phim' => $this->tinhDoanhThuPhim($query, $id),
                'phong' => $this->tinhDoanhThuPhong($query, $id),
                'tat_ca_phim_ngay' => $this->tinhDoanhThuTatCaPhim($query),
                've' => $query->sum('tong_tien'),
                'voucher' => $this->thongKeVoucherSuDung($query),
                'quoc_gia' => $this->tinhDoanhThuTheoQuocGia($query),
                'ghe' => $this->thongKeTheoGhe($query),
                default => throw new \InvalidArgumentException('Loại thống kê không hợp lệ')
            };

            return response()->json([
                'success' => true,
                'message' => 'Thống kê doanh thu thành công',
                'data' => $ketQua
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Tính doanh thu phim theo ID
     */
    private function tinhDoanhThuPhim($query, $id)
    {
        return $query->whereIn(
            'booking_id',
            Booking::join('showtimes', 'bookings.thongtinchieu_id', '=', 'showtimes.id')
                ->where('showtimes.phim_id', $id)
                ->pluck('bookings.id')
        )->sum('tong_tien');
    }

    /**
     * Tính doanh thu tất cả phòng chiếu 
     */
    private function tinhDoanhThuPhong($query, $id)
    {
        // whereIn được sử dụng để lọc các bản ghi dựa trên một danh sách các giá trị.
        return $query->whereIn(
            'booking_id',
            Booking::join('showtimes', 'bookings.thongtinchieu_id', '=', 'showtimes.id')
                ->where('showtimes.room_id', $id)
                ->pluck('bookings.id')
        )->sum('tong_tien');
    }

    /**
     * Tính doanh thu tất cả phim theo ngày
     * tổng doanh thu
     * tổng xuất chiếu
     * 
     */

    private function tinhDoanhThuTatCaPhim($query)
    {
        // Lấy thống kê cơ bản của phim
        $movies = $query->join('bookings', 'payments.booking_id', '=', 'bookings.id')
            ->join('showtimes', 'bookings.thongtinchieu_id', '=', 'showtimes.id')
            ->join('movies', 'showtimes.phim_id', '=', 'movies.id')
            ->where('payments.trang_thai', 1) // 1 = Đã hoàn thành
            ->select(
                'movies.id',
                'movies.ten_phim',
                DB::raw('SUM(payments.tong_tien) as tong_doanh_thu'),
                DB::raw('COUNT(DISTINCT bookings.id) as so_ve_ban_ra'),
                DB::raw('COUNT(DISTINCT showtimes.id) as so_xuat_chieu')
            )
            ->groupBy('movies.id', 'movies.ten_phim')
            ->get();

        // Đối với mỗi phim, lấy chi tiết suất chiếu    
        foreach ($movies as $movie) {
            $showtimes = DB::table('showtimes')
                ->join('bookings', 'showtimes.id', '=', 'bookings.thongtinchieu_id')
                ->join('payments', 'bookings.id', '=', 'payments.booking_id')
                ->where('showtimes.phim_id', $movie->id)
                ->where('payments.trang_thai', 1) // 1 = Đã hoàn thành
                ->select(
                    'showtimes.gio_chieu as time',
                    DB::raw('SUM(payments.tong_tien) as doanh_thu'),
                    DB::raw('COUNT(DISTINCT bookings.id) as so_ve_ban_ra')
                )
                ->groupBy('showtimes.gio_chieu')
                ->get();

            $movie->xuatchieu = $showtimes;
            unset($movie->id);
        }

        return $movies;
    }

    /**
     * 2. Thống kê theo trạng thái hoặc phương thức
     */
    public function thongKeTheoTrangThai($filterBy)
    {
        try {
            // Kiểm tra tham số lọc hợp lệ
            if (!in_array($filterBy, ['trang_thai', 'phuong_thuc_thanh_toan'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tham số lọc không hợp lệ.',
                    'data' => []
                ], 400);
            }

            // Thống kê theo phương thức thanh toán
            // thống kê theo trạng thái thanh toán
            if ($filterBy === 'phuong_thuc_thanh_toan') {
                $result = [
                    'tienMat' => Payment::where('phuong_thuc_thanh_toan', 'cash')->count(),
                    'thanhToanOnline' => Payment::where('phuong_thuc_thanh_toan', '!=', 'cash')->count()
                ];

                return response()->json([
                    'success' => true,
                    'message' => 'Thống kê theo phương thức thanh toán thành công',
                    'data' => $result
                ], 200);
            }

            // Thống kê theo trạng thái thanh toán
            $result = [
                'dangXuLy' => Payment::where($filterBy, 0)->count(),
                'thanhCong' => Payment::where($filterBy, 1)->count(),
                'khongThanhCong' => Payment::where($filterBy, 2)->count(),
                'huy' => Payment::where($filterBy, 3)->count(),
                'hoanLai' => Payment::where($filterBy, 4)->count()
            ];

            return response()->json([
                'success' => true,
                'message' => 'Thống kê theo trạng thái thành công',
                'data' => $result
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }


    /**
     * 3. Thống kê top N (người dùng hoặc phim)
     */
    public function thongKeTop($type, $limit)
    {
        $query = null;

        if ($type === 'user') {
            $query = BookingDetail::join('bookings', 'booking_details.booking_id', '=', 'bookings.id')
                ->join('users', 'bookings.user_id', '=', 'users.id')
                ->select('users.ho_ten', 'users.email', DB::raw('COUNT(booking_details.booking_id) as total_tickets'))
                ->groupBy('users.id', 'users.ho_ten', 'users.email')
                ->orderBy('total_tickets', 'DESC')
                ->limit($limit)
                ->get();
        } elseif ($type === 'movie') {
            $query = BookingDetail::join('bookings', 'booking_details.booking_id', '=', 'bookings.id')
                ->join('showtimes', 'bookings.thongtinchieu_id', '=', 'showtimes.id')
                ->join('movies', 'showtimes.phim_id', '=', 'movies.id')
                ->select('movies.ten_phim', 'movies.anh_phim', DB::raw('COUNT(showtimes.phim_id) as total_tickets'))
                ->groupBy('movies.id', 'movies.ten_phim', 'movies.anh_phim')
                ->orderBy('total_tickets', 'DESC')
                ->limit($limit)
                ->get();
        }


        if ($query->isEmpty()) {
            return response()->json([
                'message' => 'Không có dữ liệu thống kê.',
                'data' => [],
            ], 404);
        }

        return response()->json([
            'message' => 'Thống kê top ' . ($type === 'user' ? 'người dùng' : 'phim') . ' thành công',
            'data' => $query,
        ], 200);
    }

    /**
     * 4. Tính toán doanh thu đồ ăn
     */
    private function tinhDoanhThuDoAn($bookings)
    {
        $tongTienDoAn = 0;
        foreach ($bookings as $booking) {
            $items = explode(', ', $booking->do_an);
            foreach ($items as $item) {
                preg_match('/(.*) \(x(\d+)\)/', $item, $matches);
                if (count($matches) === 3) {
                    $foodName = $matches[1];
                    $quantity = (int)$matches[2];
                    $food = Food::where('ten_do_an', $foodName)->first();
                    if ($food) {
                        $tongTienDoAn += $food->gia * $quantity;
                    }
                }
            }
        }
        return $tongTienDoAn;
    }


    /**
     * Thống kê voucher đã sử dụng theo bookings và payments
     */
    public function thongKeVoucherSuDung($query)
    {
        try {
            $data = $query->select(
                'vouchers.id',
                'vouchers.ma_giam_gia',
                DB::raw('COUNT(bookings.id) as so_lan_su_dung'),
                DB::raw('SUM(bookings.tien_giam_gia) as tong_tien_giam'),
                DB::raw('MIN(payments.created_at) as ngay_su_dung_dau'),
                DB::raw('MAX(payments.created_at) as ngay_su_dung_cuoi')
            )
                ->groupBy('vouchers.id', 'vouchers.ma_giam_gia')
                ->orderBy('so_lan_su_dung', 'desc')
                ->get();

            if ($data->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không có dữ liệu voucher đã sử dụng trong khoảng thời gian này',
                    'data' => []
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Thống kê voucher đã sử dụng thành công',
                'data' => $data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * 6. Doanh thu theo phim theo quốc gia
     */
    private function tinhDoanhThuTheoQuocGia($query)
    {
        // Thực hiện truy vấn
        $data = $query->join('bookings', 'payments.booking_id', '=', 'bookings.id')
            ->join('showtimes', 'bookings.thongtinchieu_id', '=', 'showtimes.id')
            ->join('movies', 'showtimes.phim_id', '=', 'movies.id')
            ->select(
                'movies.quoc_gia',
                'movies.ten_phim',
                DB::raw('SUM(payments.tong_tien) as tong_doanh_thu')
            )
            ->groupBy('movies.quoc_gia', 'movies.ten_phim')
            ->orderByRaw('movies.quoc_gia, SUM(payments.tong_tien) DESC') // Sắp xếp theo nhiều cột
            ->get();

        // Kiểm tra nếu không có dữ liệu
        if ($data->isEmpty()) {
            return response()->json([
                'message' => 'Không có dữ liệu doanh thu theo quốc gia',
                'data' => [],
            ], 404);
        }

        // Trả về kết quả
        return response()->json([
            'message' => 'Thống kê doanh thu theo quốc gia thành công',
            'data' => $data,
        ], 200);
    }


    // 7. thống kê số lượng phim
    public function thongKeSoLuongPhim()
    {
        $movieCount = Movie::count();
        $showtimeCount = Showtime::count();
        $roomCount = Room::count();
        return response()->json([
            'message' => 'Thống kê số lượng phim thành công',
            'data' => [
                'data' => $movieCount,
                'so_xuat_chieu' => $showtimeCount,
                'so_phong' => $roomCount
            ]
        ], 200);
    }

    // thống kê theo ghê

    private function thongKeTheoGhe($query)
    {
        $data = $query->join('bookings', 'payments.booking_id', '=', 'bookings.id')
            ->join('showtimes', 'bookings.thongtinchieu_id', '=', 'showtimes.id')
            ->join('rooms', 'showtimes.room_id', '=', 'rooms.id')
            ->join('seats', 'rooms.id', '=', 'seats.room_id')
            ->select(
                'rooms.id as room_id',
                'rooms.ten_phong_chieu',
                'rooms.tong_ghe_phong', 
                'seats.loai_ghe_ngoi',
                DB::raw('MIN(seats.gia_ghe) as gia_ghe'), // Sử dụng MIN để lấy một giá trị duy nhất cho mỗi loại ghế
                DB::raw('GROUP_CONCAT(DISTINCT bookings.ghe_ngoi) as ghe_ngoi'),
                DB::raw('SUM(payments.tong_tien) as tong_doanh_thu'),
                DB::raw('COUNT(DISTINCT seats.id) as so_luong_ghe')
            )
            ->groupBy(
                'rooms.id',
                'rooms.ten_phong_chieu',
                'rooms.tong_ghe_phong',
                'seats.loai_ghe_ngoi'  // Bỏ seats.gia_ghe ra khỏi GROUP BY
            )
            ->orderBy('tong_doanh_thu', 'desc')
            ->get();

        $organizedData = [];
        foreach ($data as $record) {
            if (!isset($organizedData[$record->ten_phong_chieu])) {
                $organizedData[$record->ten_phong_chieu] = [
                    'ten_phong' => $record->ten_phong_chieu,
                    'tong_ghe_phong' => $record->tong_ghe_phong,
                    'tong_doanh_thu' => 0,
                    'tong_ghe_da_dat' => 0,
                    'chi_tiet_ghe' => []
                ];
            }

            // Tách ghế ngồi và đếm số lượng ghế đã đặt
            $allSeats = $record->ghe_ngoi ? array_unique(explode(',', $record->ghe_ngoi)) : [];

            // Đếm số ghế đã được đặt từ bảng seat_showtime_status
            $soLuongGheDaDat = 0;
            foreach ($allSeats as $seat) {
                $soLuongGheDaDat += Seat::where('room_id', $record->room_id)
                    ->where('loai_ghe_ngoi', $record->loai_ghe_ngoi)
                    ->where('so_ghe_ngoi', trim($seat))
                    ->count();
            }

            $tongTienGhe = $soLuongGheDaDat * $record->gia_ghe;

            // Kiểm tra xem loại ghế đã tồn tại chưa
            $loaiGheExists = false;
            foreach ($organizedData[$record->ten_phong_chieu]['chi_tiet_ghe'] as &$chiTiet) {
                if ($chiTiet['loai_ghe'] === $record->loai_ghe_ngoi) {
                    // Cộng dồn thông tin nếu đã tồn tại
                    $chiTiet['so_luong_ghe'] += $record->so_luong_ghe;
                    $chiTiet['so_ghe_da_dat'] += $soLuongGheDaDat;
                    $chiTiet['doanh_thu'] += $tongTienGhe;
                    $loaiGheExists = true;
                    break;
                }
            }

            // Nếu loại ghế chưa tồn tại, thêm mới
            if (!$loaiGheExists) {
                $organizedData[$record->ten_phong_chieu]['chi_tiet_ghe'][] = [
                    'loai_ghe' => $record->loai_ghe_ngoi,
                    'so_luong_ghe' => $record->so_luong_ghe,
                    'so_ghe_da_dat' => $soLuongGheDaDat,
                    'doanh_thu' => $tongTienGhe
                ];
            }

            $organizedData[$record->ten_phong_chieu]['tong_doanh_thu'] += $tongTienGhe;
            $organizedData[$record->ten_phong_chieu]['tong_ghe_da_dat'] += $soLuongGheDaDat;
        }

        return array_values($organizedData);
    }

    /**
     *  Doanh thu theo tháng
     */
    public function doanhThuThang()
    {
        $data = Payment::selectRaw('Month(created_at) as month, Year(created_at) as year, SUM(tong_tien) as total')
            ->where('trang_thai', 'Đã hoàn thành')
            ->groupByRaw('Year(created_at), Month(created_at)')
            ->orderByRaw('Year(created_at), Month(created_at)')
            ->get();

        return response()->json([
            'message' => 'Thống kê doanh thu theo tháng thành công',
            'data' => $data,
        ], 200);
    }
}
