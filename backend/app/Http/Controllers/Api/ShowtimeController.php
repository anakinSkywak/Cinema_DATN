<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use App\Models\Room;
use App\Models\Showtime;
use App\Models\Theater;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShowtimeController extends Controller
{


    public function index()
    {
        // xuat all
        $showtimeall = Showtime::with(['movie', 'theater', 'room'])->get();

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


    public function addShowtime()
    {

        $movies = Movie::all();

        if ($movies->isEmpty()) {
            return response()->json([
                'message' => 'Không có phim hãy thêm phim'
            ], 404);
        }

        $theaters = Theater::all();

        if ($theaters->isEmpty()) {
            return response()->json([
                'message' => 'Không có rạp hãy thêm rạp'
            ], 404);
        }

        $rooms = Room::all();

        if ($rooms->isEmpty()) {
            return response()->json([
                'message' => 'Không có phòng hãy thêm phòng'
            ], 404);
        }

        return response()->json([
            'message' => 'Lấy các thông tin đổ ra để thêm ok',
            'data' => [
                'movies' => $movies,
                'theaters' => $theaters,
                'rooms' => $rooms,
            ],
        ], 200);  // 200 có dữ liệu trả về
    }

    public function store(Request $request)
    {
        // them moi show tham , nhieu show tham cho phim de user booking
        // check khi them
        $request->validate([
            'ngay_chieu' => 'required|date',
            'thoi_luong_chieu' => 'string|max:250',
            'phim_id' => 'required|exists:movies,id',
            'rapphim_id' => 'required|exists:theaters,id',
            'room_id' => 'required|exists:rooms,id',
            'gio_chieu' => 'required|date_format:H:i'
        ]);

        // check chieu trung lap khi them moi
        // $checkTimes = Showtime::where('ngay_chieu', $request->ngay_chieu)
        //     ->where('gio_chieu', $request->gio_chieu)
        //     ->where('room_id', $request->room_id)
        //     ->exists();

        // if ($checkTimes) {
        //     return response()->json([
        //         'error' => 'Giờ chiếu này đã được thêm mới trong phòng này.',
        //     ], 400);
        // }

        // truy vấn thêm thời lượng chiếu theo thời lượng của phim đó k cần thêm bằng tay
        $thoi_luong_chieu = DB::table('movies')
            ->where('id', $request->phim_id)
            ->value('thoi_gian_phim');
        // truy van them xuat chieu moi 
        
        $showtimes = Showtime::create([
            'ngay_chieu' => $request->ngay_chieu,
            'thoi_luong_chieu' => $thoi_luong_chieu,
            'phim_id' => $request->phim_id,
            'rapphim_id' => $request->rapphim_id,
            'room_id' => $request->room_id,
            'gio_chieu' => $request->gio_chieu,
        ]);

        // tra ve neu them ok
        return response()->json([
            'message' => 'Thêm mới showtime thành công',
            'data' => $showtimes
        ], 201);
    }



    public function show(string $id)
    {
        // show du lieu theo id

        // Lấy suất chiếu theo id cùng với thông tin phim, rạp và phòng chiếu
        $showtimeID = Showtime::with(['movie', 'theater', 'room'])->find($id);

        if (!$showtimeID) {
            return response()->json([
                'message' => 'Không tìm thấy suất chiếu theo id này'
            ], 404);
        }

        return response()->json([
            'message' => 'Lấy thông tin suất chiếu theo id thành công',
            'data' => $showtimeID,
        ], 200);  // 200 có dữ liệu trả về
    }


    public function editShowtime(string $id)
    {

        // Lấy suất chiếu theo id cùng với thông tin phim, rạp và phòng chiếu
        $showtimeID = Showtime::with(['movie', 'theater', 'room'])->find($id);

        if (!$showtimeID) {
            return response()->json([
                'message' => 'Không tìm thấy suất chiếu theo id này'
            ], 404);
        }

        // đổ all phim rạp phòng nếu có chọn sẽ chọn để thay đổi
        $movies = Movie::all();
        $theaters = Theater::all();
        $rooms = Room::all();

        return response()->json([
            'message' => 'Lấy thông tin suất chiếu theo id thành công',
            'data' => [
                'showtime' => $showtimeID,
                'movies' => $movies,
                'theaters' => $theaters,
                'rooms' => $rooms,
            ],
        ], 200);  // 200 có dữ liệu trả về
    }



    public function update(Request $request, string $id)
    {
        // cap nhat theo id

        // Tìm và cập nhật suất chiếu
        $showtimeID = Showtime::findOrFail($id);

        if (!$showtimeID) {
            return response()->json([
                'message' => 'Không có dữ liệu Showtime phim theo id này',
            ], 404);
        }

        // Xác thực dữ liệu đầu vào
        $validated = $request->validate([
            'ngay_chieu' => 'required|date',
            'thoi_luong_chieu' => 'required|integer',
            'phim_id' => 'required|exists:movies,id',
            'rapphim_id' => 'required|exists:theaters,id',
            'room_id' => 'required|exists:rooms,id',
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
