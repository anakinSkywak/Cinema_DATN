<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use App\Models\Seat;
use App\Models\Showtime;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingTicketController extends Controller
{



    // list phim có showtime khi thêm showtime vói phim
    public function listMovieBookTicket(Request $request)
    {

        $showtimeMoviesBook = DB::table('movies')
            ->join('showtimes', 'movies.id', '=', 'showtimes.phim_id')
            ->select('movies.*')
            ->distinct()
            ->get();

        if ($showtimeMoviesBook->isEmpty()) {
            return response()->json([
                'message' => 'Không có phim nào đang có xuất chiếu - thêm xuất chiếu.',
            ],  404);
        }

        return response()->json([
            'message' => 'Danh sách phim có xuất chiếu cho nhân viên book vé thành công .',
            'data' => $showtimeMoviesBook
        ], 200);
    }


    // hàm ấn vào phim theo id đổ all xuất chiếu , đồ ăn
    public function MovieByShowtimeID($movieID)
    {

        $movieDetailID = Movie::with('movie_genres')->find($movieID);

        if (!$movieDetailID) {
            return response()->json([
                'message' => 'Không tìm thấy phim.'
            ], 404);
        }

        // Kiểm tra xem có showtime nào cho phim hay không
        $showtimes = Showtime::where('phim_id', $movieID)->orderBy('ngay_chieu')->select('id', 'ngay_chieu')->get()->groupBy(function ($showtime) {
            return Carbon::parse($showtime->ngay_chieu)->format('Y-m-d');
        })->map(function ($group) {
            return $group->first();
        });


        $getFoodAll = DB::table('foods')->select('id', 'ten_do_an', 'anh_do_an', 'gia', 'ghi_chu', 'trang_thai')->where('trang_thai', 0)->get();

        if ($getFoodAll->isEmpty()) {
            return response()->json([
                'message' => 'Không có đồ ăn nào - thêm đồ ăn',
            ], 404);
        }

        if ($showtimes->isEmpty()) {
            return response()->json([
                'message' => 'Chưa có thông tin chiếu cho phim này | thêm thông tin chiếu cho phim | đồ ăn all ok',
                'movie-detail' => $movieDetailID,
                'showtime-days' => $showtimes,
                'foods' => $getFoodAll,
            ], 200);
        } else {
            return response()->json([
                'message' => 'Lấy thông tin phim và showtime, all food theo id phim ok',
                'movie-detail' => $movieDetailID,
                'showtime-days' => $showtimes,
                'foods' => $getFoodAll,
            ], 200);
        }
    }


    // ấn vào ngày đổ all giờ cửa ngày đó
    public function getShowtimesByDate($movieID, $date)
    {

        $showtimes = Showtime::where('phim_id', $movieID)
            ->whereDate('ngay_chieu', $date)->select('id', 'gio_chieu',)
            ->get()
            ->groupBy('gio_chieu');

        if ($showtimes->isEmpty()) {
            return response()->json(['message' => 'Không có suất chiếu nào cho ngày đã chọn.']);
        }

        // lấy giờ chiếu duy nhất nếu có nhiều nhờ chiếu nhưng phòng khác nhau

        $uniqueShowtimes = $showtimes->map(function ($group) {
            // chọn phần tử đầu tiên trong nhóm (giờ chiếu trùng)
            return $group->first();
        })->values();

        return response()->json([
            'message' => 'Lấy danh sách giờ chiếu thành công.',
            'showtimes' => $uniqueShowtimes
        ], 200);
    }


    public function getRoomsByShowtime(Request $request, $movieID, $date, $time)
    {
        // Truy vấn danh sách các phòng chiếu cho giờ và ngày đã chọn
        $roomsByTime = Showtime::where('phim_id', $movieID)
            ->whereDate('ngay_chieu', $date)
            ->where('gio_chieu', $time)
            ->select('id', 'phim_id', 'room_id', 'ngay_chieu', 'gio_chieu')
            ->with('room') // Lấy thông tin phòng
            ->get();

        if ($roomsByTime->isEmpty()) {
            return response()->json(['message' => 'Không có phòng chiếu nào cho giờ đã chọn.']);
        }

        // Khởi tạo mảng để chứa phòng và ghế với trạng thái
        $roomsWithSeats = $roomsByTime->map(function ($showtime) {
            // Lấy room_id của từng suất chiếu
            $roomID = $showtime->room_id;

            // Lấy tất cả ghế của phòng chiếu
            $allSeats = Seat::where('room_id', $roomID)->get();

            // Truy vấn trạng thái của ghế đã đặt cho showtime này
            $bookedSeats = DB::table('seat_showtime_status')
                ->where('thongtinchieu_id', $showtime->id)
                ->where('trang_thai', 1) // Ghế đã đặt
                ->pluck('ghengoi_id');

            // Truy vấn trạng thái bảo trì của ghế từ bảng 'seats
            $maintenanceSeats = DB::table('seats')
                ->where('room_id', $roomID)
                ->where('trang_thai', 2) // Trạng thái bảo trì của ghế
                ->pluck('id');

            // Lấy trạng thái của các ghế (đã đặt, bảo trì hoặc trống)
            $seatsWithStatus = $allSeats->map(function ($seat) use ($bookedSeats, $maintenanceSeats) {
              
                if ($bookedSeats->contains($seat->id)) {
                    $status = 'Đã đặt'; // Ghế đã được đặt
                } elseif ($maintenanceSeats->contains($seat->id)) {
                    $status = 'Bảo trì'; // Ghế đang bảo trì
                } else {
                    $status = 'Trống'; // Ghế còn lại là trống
                }

                return [
                    'id' => $seat->id,
                    'ten_ghe_ngoi' => $seat->so_ghe_ngoi, 
                    'loai_ghe_ngoi' =>$seat->loai_ghe_ngoi,
                    'gia_ghe' => $seat->gia_ghe,
                    'trang_thai' => $status 
                ];
            });

            // Trả về thông tin phòng và ghế với trạng thái
            return [
                'room' => $showtime->room,
                'seats' => $seatsWithStatus // Danh sách ghế với trạng thái
            ];
        });

        return response()->json([
            'message' => 'Lấy danh sách phòng chiếu và trạng thái ghế thành công.',
            'roomsWithSeats' => $roomsWithSeats // Danh sách các phòng chiếu và ghế
        ], 200);
    }

    


}
