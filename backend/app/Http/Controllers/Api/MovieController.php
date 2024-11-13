<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Food;
use App\Models\Movie;
use App\Models\MovieGenre;
use App\Models\Seat;
use App\Models\Showtime;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MovieController extends Controller
{

    // xuất all phim với thể loại đã chọn khi thêm phim
    public function index()
    {
        // call show all du lieu ra 
        $movieall = Movie::with('movie_genres')->get();
        //dd($data);
        if ($movieall->isEmpty()) {

            return response()->json([
                'message' => 'Không có dữ liệu Movie nào'
            ], 404);
        }

        return response()->json([
            'message' => 'Hiện thị dữ liệu thành công',
            'data' => $movieall
        ], 200);
    }


    // đổ all thể loại phim để chọn ghi thêm mới phim
    public function getMovieGenre()
    {
        $getMovieGenre = MovieGenre::all();


        if ($getMovieGenre->isEmpty()) {
            return response()->json([
                'message' => 'Không có thể loại phim nào'
            ], 404);
        }

        return response()->json([
            'message' => 'Thể loại phim',
            'data' => $getMovieGenre
        ]);
    }


    // thêm mới phim với các thể loại
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ten_phim' => 'required|string|max:255',
            'anh_phim' => 'required|file|mimes:jpeg,png,jpg,gif|max:2048',
            'dao_dien' => 'required|string|max:255',
            'dien_vien' => 'required|string|max:255',
            'noi_dung' => 'required|string|max:255',
            'trailer' => 'required|string|url|max:255',
            'gia_ve' => 'required|numeric',
            'hinh_thuc_phim' => 'required|string|max:255',
            'loaiphim_ids' => 'required|array', // Xác thực mảng thể loại phim
            'loaiphim_ids.*' => 'exists:moviegenres,id', // Xác thực các thể loại phim tồn tại
            'thoi_gian_phim' => 'required|numeric',
        ]);

        // check ko chấp nhận kiểu ảnh webp : check sau

        // xu ly upload ảnh 

        if ($request->hasFile('anh_phim')) {
            $file = $request->file('anh_phim');
            $filename = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('uploads/anh_phim', $filename, 'public');
            $validated['anh_phim'] = '/storage/' . $filePath;
        }

        $movie = Movie::create($validated);
        $movie->movie_genres()->sync($request->loaiphim_ids);

        return response()->json([
            'message' => 'Thêm mới phim thành công',
            'data' => $movie->load('movie_genres'),
            'image_url' => asset($validated['anh_phim']),
        ], 201);
    }


    // show phim theo id truy vấn ở bảng trung gian 
    public function show(string $id)
    {
        $movieID = Movie::with('movie_genres')->find($id);

        if (!$movieID) {
            return response()->json([
                'message' => 'Không tìm thấy phim theo id :' . $id
            ], 404);
        }

        return response()->json([
            'message' => 'Dữ liệu show theo ID thành công',
            'data' => $movieID,
        ], 200);
    }


    public function showEditID(string $id)
    {
        $movieID = Movie::find($id);
        if (!$movieID) {
            return response()->json([
                'message' => 'Không có phim theo id ' . $id
            ], 404);
        }

        $allGenres = MovieGenre::all();
        if (!$allGenres) {
            return response()->json([
                'message' =>  'Không có thể loại phim nào'
            ], 404);
        }

        return response()->json([
            'message' => 'Hiển thị phim và thể loại',
            'movie' => $movieID->load('movie_genres'),
            'all_genre' => $allGenres,
        ], 200);
    }

    // cập nhật phim với các thông tin thay đổi đang lỗi fix sau
    public function update(Request $request, string $id)
    {
        $movie = Movie::find($id);

        if (!$movie) {
            return response()->json([
                'message' => 'Không có phim theo id ' . $id
            ], 404);
        }

        $validated = $request->validate([
            'ten_phim' => 'required|string|max:255',
            'anh_phim' => 'required|file|mimes:jpeg,png,jpg,gif|max:2048',
            'dao_dien' => 'required|string|max:255',
            'dien_vien' => 'required|string|max:255',
            'noi_dung' => 'required|string|max:255',
            'trailer' => 'required|string|max:255',
            'gia_ve' => 'required|numeric',
            'hinh_thuc_phim' => 'required|string|max:255',
            'loaiphim_ids' => 'required|array',
            'loaiphim_ids.*' => 'exists:moviegenres,id',
            'thoi_gian_phim' => 'required|numeric',
        ]);


        if ($request->hasFile('anh_phim')) {
            // Xóa file cũ nếu cần
            if ($movie->anh_phim) {
                unlink(public_path($movie->anh_phim));
            }

            $file = $request->file('anh_phim');
            $filename = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('uploads/anh_phim', $filename, 'public');

            $validated['anh_phim'] = '/storage/' . $filePath;
        } else {
            $validated['anh_phim'] = $movie->anh_phim;
        }

        $movie->update($validated);
        $movie->movie_genres()->sync($request->loaiphim_ids);

        return response()->json([
            'message' => 'Cập nhật thành công',
            'data' => $movie->load('movie_genres'),
            //'image_url' => asset($movie->anh_phim),
        ], 200);
    }


    // xóa theo id
    public function delete(string $id)
    {
        $movieID = Movie::find($id);

        if (!$movieID) {
            return response()->json([
                'message' => 'Không tìm thấy phim theo id ' . $id
            ], 404);
        }

        $movieID->delete();

        return response()->json([
            'message' => 'Xóa thành công'
        ]);
    }


    public function movieFilter(string $id)
    {
        $movies = Movie::with('movie_genres')->whereHas('movie_genres', function ($query) use ($id) {
            $query->where('moviegenres.id', $id);
        })->get();

        if ($movies->isEmpty()) {
            return response()->json([
                'message' => 'Không có phim nào'
            ], 404);
        }

        return response()->json([
            'data' => $movies,
        ]);
    }


    public function movieFilterKeyword(Request $request)
    {
        // $keyword = $request->input('keyword');
        $keyword = $request->query('keyword');
        $movies = Movie::with('movie_genres')->where('ten_phim', 'like', '%' . $keyword . '%')->get();

        if ($movies->isEmpty()) {
            return response()->json([
                'message' => 'Không tìm thấy phim'
            ], 404);
        }

        return response()->json([
            'data' => $movies,
        ]);
    }



    // chi tiết phim và đồ ăn showime theo phim đã thêm khi ấn vào phim đưa đến trang chi tiết phim
    //Phương thức movieDetail để nhóm và hiển thị showtime theo ngày
    public function movieDetail($movieID)
    {
        // Truy vấn thông tin phim và các showtimes của phim
        $movieDetailID = Movie::find($movieID);

        if (!$movieDetailID) {
            return response()->json([
                'message' => 'Không tìm thấy phim.'
            ], 404);
        }

        // Kiểm tra xem có showtime nào cho phim hay không
        $showtimes = Showtime::where('phim_id', $movieID)->orderBy('ngay_chieu')->select('id', 'ngay_chieu', 'room_id')->get()->groupBy(function ($showtime) {
            return Carbon::parse($showtime->ngay_chieu)->format('Y-m-d');
        })->map(function ($group) {
            return $group->first();
        });

        $getFoodAll = Food::all();

        if (!$showtimes) {
            return response()->json([
                'message' => 'Chưa có thông tin chiếu cho phim này | thêm thông tin chiếu cho phim',
                'movie-detail' => $movieDetailID,
                'showtime-days' => $showtimes,
                'foods' => $getFoodAll,
            ], 404);
        } else {
            return response()->json([
                'message' => 'Lấy thông tin phim và showtime, all food theo id phim ok',
                'movie-detail' => $movieDetailID,
                'showtime-days' => $showtimes,
                'foods' => $getFoodAll,
            ], 200);
        }
    }



    // hàm khi ấn vào showtime theo ngày mong muốn sẽ đổ all gio_chieu có theo ngày ấn đó để chọn giờ chiếu sẽ đổ all theo giờ đó
    // Phương thức để lấy tất cả giờ chiếu trong ngày khi chọn ngày
    public function getShowtimesByDate(Request $request, $movieID, $date)
    {

        // truy van showtime cho phim trong ngay da chon
        $showtimes = Showtime::where('phim_id', $movieID)
            ->whereDate('ngay_chieu', $date)->select('id' , 'gio_chieu')
            ->get();

        if ($showtimes->isEmpty()) {
            return response()->json(['message' => 'Không có suất chiếu nào cho ngày đã chọn.']);
        }

        return response()->json([
            'message' => 'Lấy danh sách giờ chiếu thành công.',
            'showtimes' => $showtimes
        ], 200);
    }



    // khi ấn vào gio mong muon do all ghe 
    //Phương thức getSeatsByShowtime để lấy ghế khi chọn giờ chiếu
    public function getSeatsByShowtime($movieID, $showtimeID)
    {
        // Truy vấn thông tin suất chiếu cụ thể (showtime)
        $showtime = Showtime::with(['room'])->find($showtimeID);

        if (!$showtime) {
            return response()->json([
                'message' => 'Không tìm thấy thông tin suất chiếu.'
            ], 404);
        }

        // Kiểm tra xem showtime có thuộc về bộ phim không
        if ($showtime->phim_id != $movieID) {
            return response()->json([
                'message' => 'Suất chiếu này không thuộc về phim này.'
            ], 400);
        }

        // Lấy room_id từ showtime
        $room_id = $showtime->room->id;

        // Truy vấn tất cả ghế trong phòng chiếu của suất chiếu
        $allSeats = Seat::where('room_id', $room_id)->get();

        // Truy vấn trạng thái của ghế đã đặt
        $bookedSeats = DB::table('seat_showtime_status')
            ->where('thongtinchieu_id', $showtimeID) // Lấy trạng thái ghế cho showtime này
            ->where('trang_thai', 1) // Ghế đã đặt
            ->pluck('ghengoi_id'); // Lấy danh sách ghế đã đặt

        // Lấy trạng thái của các ghế (đã đặt hoặc trống)
        $seatsWithStatus = $allSeats->map(function ($seat) use ($bookedSeats) {
            return [
                'id' => $seat->id,
                'ten_ghe_ngoi' => $seat->so_ghe_ngoi,
                'trang_thai' => $bookedSeats->contains($seat->id) ? 'đã đặt' : 'trống'
            ];
        });

        return response()->json([
            'message' => 'Lấy danh sách ghế và trạng thái ghế thành công.',
            'showtime' => $showtime,
            'seats' => $seatsWithStatus
        ], 200);
    }
}
