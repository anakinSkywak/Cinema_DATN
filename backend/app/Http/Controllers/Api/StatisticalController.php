<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Food;
use App\Models\Room;
use App\Models\Movie;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\BookingDetail;

class StatisticalController extends Controller
{
    /**
     * Thống kê doanh thu linh hoạt theo loại
     * @param Request $request
     * @param string $type Loại thống kê (ve, do_an, phim, phong, tat_ca_phim_ngay)
     * @param int|null $id ID của phim hoặc phòng (tùy theo type)
     */
    public function thongKeDoanhThu(Request $request, $type = 've', $id = null)
    {
        try {
            $trangThai = 'Đã hoàn thành';
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');

            // Khởi tạo query cơ bản
            $query = Payment::query()->where('trang_thai', $trangThai);

            // Áp dụng bộ lọc ngày tháng
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

            // Xử lý theo từng loại thống kê
            // 
            $ketQua = match($type) {
                'do_an' => $this->tinhDoanhThuDoAn(
                    Booking::whereIn('id', $query->pluck('booking_id'))->get()
                ),
                'phim' => $this->tinhDoanhThuPhim($query, $id),
                'phong' => $this->tinhDoanhThuPhong($query, $id),
                'tat_ca_phim_ngay' => $this->tinhDoanhThuTatCaPhim($query),
                've' => $query->sum('tong_tien'),
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
        return $query->whereIn('booking_id', 
            Booking::join('showtimes', 'bookings.thongtinchieu_id', '=', 'showtimes.id')
                ->where('showtimes.phim_id', $id)
                ->pluck('bookings.id')
        )->sum('tong_tien');
    }

    /**
     * Tính doanh thu phòng chiếu theo ID
     */
    private function tinhDoanhThuPhong($query, $id)
    {
        return $query->whereIn('booking_id',
            Booking::join('showtimes', 'bookings.thongtinchieu_id', '=', 'showtimes.id')
                ->where('showtimes.room_id', $id)
                ->pluck('bookings.id')
        )->sum('tong_tien');
    }

    /**
     * Tính doanh thu tất cả phim theo ngày
     */
    private function tinhDoanhThuTatCaPhim($query)
    {
        return $query->join('bookings', 'payments.booking_id', '=', 'bookings.id')
            ->join('showtimes', 'bookings.thongtinchieu_id', '=', 'showtimes.id')
            ->join('movies', 'showtimes.phim_id', '=', 'movies.id')
            ->select('movies.ten_phim', DB::raw('SUM(bookings.tong_tien) as tong_doanh_thu'))
            ->groupBy('movies.id', 'movies.ten_phim')
            ->get();
    }

    /**
     * 2. Thống kê theo trạng thái hoặc phương thức
     */
    public function thongKeTheoTrangThai(Request $request, $filterBy = 'trang_thai')
    {
        $conditions = [
            'dangXuLy' => 'Đang chờ xử lý',
            'thanhCong' => 'Đã hoàn thành', 
            'khongThanhCong' => 'Không thành công',
            'hoanLai' => 'Đã hoàn lại',
            'huy' => 'Hủy',
        ];

        if ($filterBy === 'phuong_thuc_thanh_toan') {
            $result = [
                'tienMat' => 'cash',
                'thanhToanOnline' => Payment::where($filterBy, '!=', 'cash')->count()
            ];

            return response()->json([
                'message' => 'Thống kê theo ' . $filterBy . ' thành công',
                'data' => $result,
            ], 200);
        }

        $result = [];
        foreach ($conditions as $key => $value) {
            $result[$key] = Payment::where($filterBy, $value)->count();
        }

        return response()->json([
            'message' => 'Thống kê theo ' . $filterBy . ' thành công',
            'data' => $result,
        ], 200);
    }

    /**
     * 3. Thống kê top N (người dùng hoặc phim)
     */
    public function thongKeTop(Request $request, $type = 'user', $limit = 5)
    {
        $query = null;

        if ($type === 'user') {
            $query = BookingDetail::join('bookings', 'booking_details.booking_id', '=', 'bookings.id')
                ->join('users', 'bookings.user_id', '=', 'users.id')
                ->select('users.ho_ten', 'users.email', DB::raw('COUNT(booking_details.booking_id) as total_tickets'))
                ->groupBy('users.id', 'users.ho_ten', 'users.email')
                ->orderBy('total_tickets', 'DESC')
                ->limit($limit);
        } elseif ($type === 'movie') {
            $query = BookingDetail::join('bookings', 'booking_details.booking_id', '=', 'bookings.id')
                ->join('showtimes', 'bookings.thongtinchieu_id', '=', 'showtimes.id')
                ->join('movies', 'showtimes.phim_id', '=', 'movies.id')
                ->select('movies.ten_phim', 'movies.anh_phim', DB::raw('COUNT(showtimes.phim_id) as total_tickets'))
                ->groupBy('movies.id', 'movies.ten_phim', 'movies.anh_phim')
                ->orderBy('total_tickets', 'DESC')
                ->limit($limit);
        }

        $data = $query->get();

        if ($data->isEmpty()) {
            return response()->json([
                'message' => 'Không có dữ liệu thống kê.',
                'data' => [],
            ], 404);
        }

        return response()->json([
            'message' => 'Thống kê top ' . ($type === 'user' ? 'người dùng' : 'phim') . ' thành công',
            'data' => $data,
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
     * 5. Doanh thu theo tháng
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
