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


    // đổ showtime theo ngày , theo phim


    // đổ all showtime ( có thể dùng hoặc không )

    public function index()
    {

        $showtimeall = Showtime::with(['movie', 'room'])->get();
        if ($showtimeall->isEmpty()) {
            return response()->json([
                'message' => 'Không có xuất chiếu tạo xuất chiếu với phim !'
            ], 404);
        }

        return response()->json([
            'message' => 'Lấy thông tin suất chiếu thành công',
            'data' => $showtimeall,
        ], 200);
    }


    // đưa đến from add đổ all phim rạp phòng để chọn thêm
    public function addShowtime()
    {

        // lấy phim thêm showtime với phim phải là Đang chiếu 

        $movies = Movie::select('id', 'ten_phim', 'hinh_thuc_phim')->where('hinh_thuc_phim', 'Đang Chiếu')->get();
        if ($movies->isEmpty()) {
            return response()->json([
                'message' => 'Không có phim hãy thêm phim !'
            ], 404);
        }

        // lấy room ra với tổng ghế phòng phải có lớn hơn 0
        $rooms = Room::select('id', 'ten_phong_chieu')->where('tong_ghe_phong', '>', 0)->get();
        if ($rooms->isEmpty()) {
            return response()->json([
                'message' => 'Không có phòng hãy thêm phòng !'
            ], 404);
        }

        return response()->json([
            'message' => 'Lấy các thông tin phim and phòng thành công',
            'data' => [
                'movies' => $movies,
                'rooms' => $rooms,
            ],
        ], 200);
    }


    // thêm mới showtime với movie , nhiều room , thông tin của showtime
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

        $thoi_luong_chieu = DB::table('movies')
            ->where('id', $request->phim_id)
            ->value('thoi_gian_phim');

        $showtimes = [];

        foreach ($request->room_ids as $room_id) { // thêm showtime với nhiều phòng cùng 1 gio

            foreach ($request->gio_chieu as $gio) { // thêm nhiều giờ với cùng 1 phim 1 room

                $gio = $gio . ':00';
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


    // show chi tiết theo id showtime 
    public function show(string $id)
    {

        // truy vấn thông tin phim phòng thông tin showtime theo id showtime
        $showtimeID = Showtime::with(['movie:id,ten_phim,thoi_gian_phim', 'room:id,ten_phong_chieu'])
            ->select('id', 'ngay_chieu', 'gio_chieu', 'phim_id', 'room_id', 'thoi_luong_chieu')
            ->find($id);

        if (!$showtimeID) {
            return response()->json([
                'message' => 'Không tìm thấy suất chiếu theo id này !'
            ], 404);
        }

        return response()->json([
            'message' => 'Lấy thông tin suất chiếu theo id thành công',
            //'showtime' => $showtimeID,
            'movie' => $showtimeID->movie->ten_phim,
            'thoi_luong_chieu' => $showtimeID->thoi_luong_chieu,
            'ngay_chieu' => $showtimeID->ngay_chieu,
            'gio_chieu' => $showtimeID->gio_chieu,
            'room' => $showtimeID->room->ten_phong_chieu,
        ], 200);
    }


    // đưa đến from edit với thông tin movie , room
    public function editShowtime(string $id)
    {

        // Lấy suất chiếu theo id cùng với thông tin phim, phòng chiếu
        $showtimeID = Showtime::with(['movie:id,ten_phim', 'room:id,ten_phong_chieu'])->find($id);

        if (!$showtimeID) {
            return response()->json([
                'message' => 'Không tìm thấy suất chiếu theo id này'
            ], 404);
        }

        // đổ all phim chọn để thay đổi với điều kiện phim Đang Chiếu
        $movies = Movie::select('id', 'ten_phim')->where('hinh_thuc_phim', 'Đang Chiếu')->get();
        if ($movies->isEmpty()) {
            return response()->json([
                'message' => 'Không có phim nào thêm phim !',
            ], 404);
        }

        // truy vấn đổ tất cả phòng có ghế rồi để thêm
        $rooms = Room::select('id', 'ten_phong_chieu')->where('tong_ghe_phong', '>', 0)->get();
        if ($rooms->isEmpty()) {
            return response()->json([
                'message' => 'Không có room nào thêm room mới và thêm ghế cho room đó !',
            ], 404);
        }

        //$movies = Movie::all();
        //$theaters = Theater::all();
        //$rooms = Room::all();

        return response()->json([
            'message' => 'Lấy thông tin suất chiếu theo id thành công , đổ tất cả : movie , room để chọn nếu thay đổi',
            'data' => [
                'showtime' => $showtimeID,
                'movies' => $movies,
                'rooms' => $rooms,
            ],
        ], 200);
    }


    // update với thông tin ngày mới giờ mới
    public function update(Request $request, string $id)
    {

        $showtime = Showtime::find($id);
        if (!$showtime) {
            return response()->json([
                'message' => 'Không có dữ liệu Showtime theo ID này!',
            ], 404);
        }

        $validated = $request->validate([
            'ngay_chieu' => 'required|date',
            'phim_id' => 'required|exists:movies,id',
            'room_id' => 'required|exists:rooms,id',
            'gio_chieu' => 'required|date_format:H:i',
        ]);


        $thoi_luong_chieu = DB::table('movies')
            ->where('id', $request->phim_id)
            ->value('thoi_gian_phim');

        $gio_chieu_moi = Carbon::createFromFormat('H:i', $request->gio_chieu);
        $gio_ket_thuc_moi = $gio_chieu_moi->copy()->addMinutes($thoi_luong_chieu + 15);

        // lấy tất cả suất chiếu trong cùng phòng, cùng ngày nhưng không bao gồm suất hiện tại
        $suat_chieu_khac = Showtime::where('room_id', $request->room_id)
            ->where('ngay_chieu', $request->ngay_chieu)
            ->where('id', '!=', $id)
            ->get();

        foreach ($suat_chieu_khac as $suat) {
            // lấy giờ bắt đầu và kết thúc của suất chiếu khác
            $gio_bat_dau_khac = Carbon::createFromFormat('H:i:s', $suat->gio_chieu);
            $gio_ket_thuc_khac = $gio_bat_dau_khac->copy()->addMinutes($suat->movie->thoi_gian_phim + 15);

            // kiểm tra trùng giờ hoặc khoảng cách quá gần
            if (
                $gio_chieu_moi->between($gio_bat_dau_khac, $gio_ket_thuc_khac) ||
                $gio_ket_thuc_moi->between($gio_bat_dau_khac, $gio_ket_thuc_khac) ||
                $gio_bat_dau_khac->between($gio_chieu_moi, $gio_ket_thuc_moi)
            ) {
                return response()->json([
                    'error' => "Giờ chiếu mới |{$gio_chieu_moi->toTimeString()}| trùng or quá gần với suất chiếu khác (giờ: {$gio_bat_dau_khac->toTimeString()}) thời lượng phim là |{$thoi_luong_chieu}-phút| + 15p dọn dẹp không được thêm giờ chiếu mới dưới thời gian này !",
                ], 400);
            }
        }

        $showtime->update($validated);

        return response()->json([
            'message' => 'Cập nhật dữ liệu Showtime thành công!',
            'data' => $showtime,
        ], 200);
    }


    // xoa showtime theo id
    public function delete(string $id)
    {

        $showtimeID = Showtime::find($id);
        if (!$showtimeID) {
            return response()->json([
                'message' => 'Không có dữ liệu Showtime theo id này !',
            ], 404);
        }

        $showtimeID->delete();

        return response()->json([
            'message' => 'Xóa Showtime theo id thành công'
        ], 200);
    }

    
}
