<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use App\Models\Room;
use App\Models\SeatShowtimeStatu;
use App\Models\Showtime;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShowtimeController extends Controller
{

    //1
    // đổ ra những showtime có phim khác nhau ở list showtime

    public function listshowtimeByMovie(Request $request)
    {

        // lấy danh sách các phim có showtime
        $showtimes = Showtime::select(DB::raw('MIN(id) as id'), 'phim_id',)
            ->groupBy('phim_id')
            ->with(['movie:id,ten_phim'])->get();

        // check rỗng 
        if ($showtimes->isEmpty()) {
            return response()->json([
                'message' => 'Chưa có xuất chiếu của bất kì phim nào thêm xuất chiếu !',
                'data' => $showtimes,
            ], 404);
        }

        return response()->json([
            'message' => 'List xuất chiếu thành công',
            'data' => $showtimes,
        ], 200);
    }

    //2
    // đổ all showtime ngày theo phim id đó
    public function showtimeByDateMovie(Request $request, $movieID)
    {

        // truy vấn lấy showtime theo khác nhau
        $showtimeByMovieByDate = Showtime::where('phim_id', $movieID)
            ->selectRaw('DATE(ngay_chieu) as ngay_chieu')
            ->distinct()
            ->orderBy('ngay_chieu', 'asc')
            ->get();

        if (!$showtimeByMovieByDate) {
            return response()->json([
                'message' => 'Không ngày chiếu theo id phim này !',
                'data' => $showtimeByMovieByDate,
            ], 400);
        }


        if ($showtimeByMovieByDate->isEmpty()) {
            return response()->json([
                'message' => 'Không ngày chiếu theo id phim này , thêm xuất chiếu với phim đó !',
                'data' => $showtimeByMovieByDate,
            ], 404);
        }

        return response()->json([
            'message' => 'Tất cả ngày chiếu của xuất chiếu theo phim id',
            'data' => $showtimeByMovieByDate,
        ], 200);
    }


    //3
    // đổ all giờ khi ấn vào ngày 
    public function getShowtimesTimeByDate(Request $request, $movieID)
    {
        $validated = $request->validate([
            'ngay_chieu' => 'required|date',
        ]);

        $date = $validated['ngay_chieu'];


        $allTimeByDate = Showtime::with('movie:id,ten_phim', 'room:id,ten_phong_chieu')
            ->where('phim_id', $movieID)
            ->whereDate('ngay_chieu', $date)
            ->orderBy('gio_chieu', 'asc')
            ->get();

        if ($allTimeByDate->isEmpty()) {
            return response()->json([
                'message' => 'Không có giờ chiếu nào của ngày đã chọn !',
                'data' => [],
            ], 404);
        }

        return response()->json([
            'message' => 'Lấy tất cả giờ chiếu theo ngày thành công',
            'data' => $allTimeByDate,
        ], 200);
    }



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
                        'error' => "Giờ chiếu |$gio| vào ngày |{$request->ngay_chieu}| đã tồn tại trong phòng !",
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

                //
                $seats = DB::table('seats')->where('room_id', $room_id)->get();

                foreach ($seats as $seat) {
                    SeatShowtimeStatu::create([
                        'thongtinchieu_id' => $showtime->id,
                        'ghengoi_id' => $seat->id,
                        'gio_chieu' => $gio,
                        'trang_thai' => 0, // Trạng thái = 0 (trống)
                    ]);
                }
                //

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


    // hàm chuyển hóa loại bỏ đầu vào chuyển đổi dấu thanh ko dấu trước khi truy vấn
    public function normalizeVietnameseString($str)
    {
        $map = [
            'a' => ['á', 'à', 'ả', 'ã', 'ạ', 'ă', 'ắ', 'ằ', 'ẳ', 'ẵ', 'ặ', 'â', 'ấ', 'ầ', 'ẩ', 'ẫ', 'ậ'],
            'd' => ['đ'],
            'e' => ['é', 'è', 'ẻ', 'ẽ', 'ẹ', 'ê', 'ế', 'ề', 'ể', 'ễ', 'ệ'],
            'i' => ['í', 'ì', 'ỉ', 'ĩ', 'ị'],
            'o' => ['ó', 'ò', 'ỏ', 'õ', 'ọ', 'ô', 'ố', 'ồ', 'ổ', 'ỗ', 'ộ', 'ơ', 'ớ', 'ờ', 'ở', 'ỡ', 'ợ'],
            'u' => ['ú', 'ù', 'ủ', 'ũ', 'ụ', 'ư', 'ứ', 'ừ', 'ử', 'ữ', 'ự'],
            'y' => ['ý', 'ỳ', 'ỷ', 'ỹ', 'ỵ']
        ];

        foreach ($map as $ascii => $unicode) {
            $str = str_replace($unicode, $ascii, $str);
        }

        return strtolower($str);
    }



    // chức năng tìm kiếm showtime : phim , ngày , phòng , giờ 
    public function searchShowtimes(Request $request)
    {

        // lấy dữ liệu từ đầu vào input resquet
        $ten_phim  = $request->input('ten_phim');
        $ten_phong_chieu  = $request->input('ten_phong_chieu');
        $ngay_chieu  = $request->input('ngay_chieu');
        $gio_chieu  = $request->input('gio_chieu');

        $query = Showtime::query();

        $message = [];

        // tham gia với bảng movies để tìm theo tên phim
        if ($ten_phim) {
            $query->whereHas('movie', function ($subQuery) use ($ten_phim) {
                $subQuery->where('ten_phim', 'LIKE', '%' . $ten_phim . '%');
            });
        }

        // tham gia với bảng rooms để tìm theo tên phòng
        if ($ten_phong_chieu) {
            $query->whereHas('room', function ($subQuery) use ($ten_phong_chieu) {
                $subQuery->where('ten_phong_chieu', 'LIKE', '%' . $ten_phong_chieu . '%');
            });
        }

        // nêu có nhập ngày chiếu và giờ chiếu
        if ($ngay_chieu) {
            $query->where('ngay_chieu', $ngay_chieu);
        }

        if ($gio_chieu) {
            //  khoảng giờ 07:00 đến 08:00
            $start_time = $gio_chieu . ':00';
            $end_time = date('H:i:s', strtotime($start_time . ' +1 hour'));

            $query->whereBetween('gio_chieu', [$start_time, $end_time]);
        }

        // lấy danh sách showtime có phù hợp
        $showtimes = $query->with(['movie', 'room'])->get();
        //$showtimes = $query->get();

        if ($ten_phim && $showtimes->isEmpty()) {
            $message[] = 'Không tìm thấy suất chiếu cho phim: ' . $ten_phim;
        }

        if ($ten_phong_chieu && $showtimes->isEmpty()) {
            $message[] = 'Không tìm thấy suất chiếu cho phòng: ' . $ten_phong_chieu;
        }

        if ($ngay_chieu && $showtimes->isEmpty()) {
            $message[] = 'Không tìm thấy suất chiếu cho ngày: ' . $ngay_chieu;
        }

        if ($gio_chieu && $showtimes->isEmpty()) {
            $message[] = 'Không tìm thấy suất chiếu cho giờ: ' . $gio_chieu;
        }

        if ($showtimes->isEmpty()) {
            return response()->json([
                'message' => $message ?: ['Không tìm thấy suất chiếu nào phù hợp!'],
            ], 404);
        }

        $result = $showtimes->map(function ($showtime) {
            return [
                'ten_phim' => $showtime->movie->ten_phim,
                'thoi_luong_chieu' => $showtime->thoi_luong_chieu,
                'ten_phong_chieu' => $showtime->room->ten_phong_chieu,
                'ngay_chieu' => $showtime->ngay_chieu,
                'gio_chieu' => $showtime->gio_chieu,
            ];
        });

        return response()->json([
            'message' => 'Kết quả tìm kiếm suất chiếu theo yêu cầu',
            'data' => $result
        ]);
    }
}
