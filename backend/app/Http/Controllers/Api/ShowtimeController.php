<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use App\Models\Room;
use App\Models\Showtime;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShowtimeController extends Controller
{


    public function index()
    {
        // xuat all
        $showtimeall = Showtime::with(['movie', 'room'])->get();

        if (!$showtimeall) {
            return response()->json([
                'message' => 'Không tìm thấy suất chiếu'
            ], 404);
        }

        return response()->json([
            'message' => 'Lấy thông tin suất chiếu thành công',
            'data' => $showtimeall,
        ], 200);  // 200 có dữ liệu trả về
    }


    // đưa đến from add đổ all phim rạp phòng để chọn thêm
    public function addShowtime()
    {

        $movies = Movie::select('id', 'ten_phim' , 'hinh_thuc_phim')->where('hinh_thuc_phim' , 'Đang Chiếu')->get();
        if ($movies->isEmpty()) {
            return response()->json([
                'message' => 'Không có phim hãy thêm phim'
            ], 404);
        }

        //$rooms = Room::all();
        $rooms = Room::select('id', 'ten_phong_chieu')->get();
        if ($rooms->isEmpty()) {
            return response()->json([
                'message' => 'Không có phòng hãy thêm phòng'
            ], 404);
        }

        return response()->json([
            'message' => 'Lấy các thông tin đổ ra để thêm ok',
            'data' => [
                'movies' => $movies,
                'rooms' => $rooms,
            ],
        ], 200);  
    }


    public function store(Request $request)
    {
        $request->validate([
            'ngay_chieu' => 'required|date',
            'phim_id' => 'required|exists:movies,id',
            'room_ids' => 'required|array',
            'room_ids.*' => 'exists:rooms,id',
            'gio_chieu' => 'required|array',
            'gio_chieu.*' => 'required|date_format:H:i'
        ]);

        // Truy vấn thời gian chiếu của phim từ cơ sở dữ liệu
        $thoi_luong_chieu = DB::table('movies')
            ->where('id', $request->phim_id)
            ->value('thoi_gian_phim');

        $showtimes = [];

        foreach ($request->room_ids as $room_id) { // thêm showtime với nhiều phòng cùng 1 giờ

            foreach ($request->gio_chieu as $gio) {

                $gio = $gio . ':00';  // Thêm phần giây nếu không có
                $gio_chieu = Carbon::createFromFormat('H:i:s', $gio); // Tạo Carbon instance từ giờ chiếu

                // Kiểm tra xem ngày chiếu và giờ chiếu đã tồn tại trong phòng này chưa
                $exists = Showtime::where('ngay_chieu', $request->ngay_chieu)
                    ->where('room_id', $room_id)
                    ->where('gio_chieu', $gio)
                    ->exists();

                if ($exists) {
                    return response()->json([
                        'error' => "Giờ chiếu |$gio| vào ngày |{$request->ngay_chieu}| đã tồn tại trong phòng",
                    ], 400);
                }

                // Kiểm tra giờ chiếu trước đó trong cùng phòng và ngày
                // Không kiểm tra ngày, chỉ kiểm tra trong cùng phòng
                $last_showtime = Showtime::where('room_id', $room_id)
                    ->where('gio_chieu', '<', $gio_chieu->toTimeString())
                    ->orderBy('gio_chieu', 'desc') // Lấy giờ chiếu gần nhất
                    ->first();

                if ($last_showtime) {
                    // Tính thời gian kết thúc của giờ chiếu trước đó (bao gồm thời gian chiếu + 15 phút dọn dẹp)
                    $gio_truoc = Carbon::createFromFormat('H:i:s', $last_showtime->gio_chieu);

                    $thoi_gian_ket_thuc_truoc = $gio_truoc->copy()->addMinutes($thoi_luong_chieu + 15);

                    // Kiểm tra giờ chiếu mới phải lớn hơn giờ kết thúc của giờ chiếu trước
                    if ($gio_chieu->lessThanOrEqualTo($thoi_gian_ket_thuc_truoc)) {

                        $gio_ket_thuc = $thoi_gian_ket_thuc_truoc->format('H:i:s');

                        return response()->json([
                            'error' => "Giờ chiếu |$gio| không thể thêm vì quá gần với giờ chiếu trước đó là: {$last_showtime->gio_chieu} phải thêm mới với lớn hơn {$gio_ket_thuc}.",
                        ], 400);
                    }
                }

                // Tạo mới showtime
                $showtime = Showtime::create([
                    'ngay_chieu' => $request->ngay_chieu,
                    'thoi_luong_chieu' => $thoi_luong_chieu,
                    'phim_id' => $request->phim_id,
                    //'room_id' => $request->room_id,
                    'room_id' => $room_id,
                    'gio_chieu' => $gio,
                ]);

                $showtimes[] = $showtime;
            }
        }

        return response()->json([
            'message' => 'Thêm mới showtime thành công',
            'data' => $showtimes
        ], 201);
    }


    public function show(string $id)
    {

        // Lấy suất chiếu theo id cùng với thông tin phim, rạp và phòng chiếu
        $showtimeID = Showtime::with(['movie:id,ten_phim', 'room:id,ten_phong_chieu'])
            ->select('id', 'ngay_chieu', 'gio_chieu', 'phim_id', 'room_id')
            ->find($id);

        if (!$showtimeID) {
            return response()->json([
                'message' => 'Không tìm thấy suất chiếu theo id này'
            ], 404);
        }

        return response()->json([
            'message' => 'Lấy thông tin suất chiếu theo id thành công',
            'ngay_chieu' => $showtimeID->ngay_chieu,
            'gio_chieu' => $showtimeID->gio_chieu,
            'movie' => $showtimeID->movie->ten_phim,
            'room' => $showtimeID->room->ten_phong_chieu,
        ], 200);
    }


    public function editShowtime(string $id)
    {

        // Lấy suất chiếu theo id cùng với thông tin phim, phòng chiếu
        $showtimeID = Showtime::with(['movie:id,ten_phim', 'room:id,ten_phong_chieu'])->find($id);

        if (!$showtimeID) {
            return response()->json([
                'message' => 'Không tìm thấy suất chiếu theo id này'
            ], 404);
        }

        // đổ all phim chọn để thay đổi
        $movies = Movie::select('id', 'ten_phim')->get();
        if ($movies->isEmpty()) {
            return response()->json([
                'message' => 'Không có phim nào thêm phim',
            ], 404);
        }

        $rooms = Room::select('id', 'ten_phong_chieu')->get();
        if ($rooms->isEmpty()) {
            return response()->json([
                'message' => 'Không có room nào của rạp phim này thêm room với rạp đó',
            ], 404);
        }

        //$movies = Movie::all();
        //$theaters = Theater::all();
        //$rooms = Room::all();

        return response()->json([
            'message' => 'Lấy thông tin suất chiếu theo id thành công , đổ all movie , theater , room để chọn nếu thay đổi',
            'data' => [
                'showtime' => $showtimeID,
                'movies' => $movies,
                'rooms' => $rooms,
            ],
        ], 200);
    }


    // xu ly sau
    public function update(Request $request, string $id)
    {
        // cap nhat theo id

        // Tìm và cập nhật suất chiếu
        $showtimeID = Showtime::find($id);

        if (!$showtimeID) {
            return response()->json([
                'message' => 'Không có dữ liệu Showtime phim theo id này',
            ], 404);
        }

        // Xác thực dữ liệu đầu vào
       $validated =  $request->validate([
            'ngay_chieu' => 'required|date',
            'phim_id' => 'required|exists:movies,id',
            'room_ids' => 'required|array',
            'room_ids.*' => 'exists:rooms,id',
            '//gio_chieu' => 'required|array',
            'gio_chieu' => 'required|date_format:H:i'
        ]);

        // cap nhat
        $showtimeID->update($validated);

        // trả về 
        return response()->json([
            'message' => 'Cập nhật dữ liệu Showtime theo id thành công',
            'data' => $showtimeID
        ], 200);
    }


    public function delete(string $id)
    {
        // xoa theo id
        $showtimeID = Showtime::find($id);

        // check xem co du lieu hay ko
        if (!$showtimeID) {
            return response()->json([
                'message' => 'Không có dữ liệu Showtime theo id này',
            ], 404);
        }

        $showtimeID->delete();

        return response()->json([
            'message' => 'Xóa Showtime theo id thành công'
        ], 200);
    }
}
