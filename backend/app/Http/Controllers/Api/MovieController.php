<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use App\Models\MovieGenre;
use App\Models\Seat;
use App\Models\Showtime;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use function PHPUnit\Framework\isEmpty;

class MovieController extends Controller
{

    // xuất all phim với thể loại : admin
    public function index()
    {
        // xuất dữ liệu phim theo id giảm dần
        $movieall = Movie::with('movie_genres')->orderBy('id', 'DESC')->get();

        if ($movieall->isEmpty()) {
            return response()->json([
                'message' => 'Không có dữ liệu Movie nào'
            ], 404);
        }

        return response()->json([
            'message' => 'Hiện thị dữ liệu phim thành công',
            'data' => $movieall
        ], 200);
    }

    // movie ở home trạng thái Đang Chiếu , Sắp Công Chiếu , phim có showtime
    public function movieClient()
    {

        // phim đang chiếu 0 : Đang chiếu
        $movie_chieu = Movie::with('movie_genres')->where('hinh_thuc_phim', 0)->orderBy('id', 'DESC')->get();


        // phim sắp công chiếu 1 : Sắp công chiếu
        $movie_sap_chieu = Movie::with('movie_genres')->where('hinh_thuc_phim', 1)->orderBy('id', 'DESC')->get();


        // phim có xuất chiếu đã tạo showtime theo phim 
        $movie_xuatchieu = Movie::with('movie_genres')
            ->whereHas('showtimes') // Chỉ lấy các phim có suất chiếu
            ->orderBy('id', 'DESC')
            ->get();


        // check rỗng dữ liệu
        if ($movie_chieu->isEmpty()) {

            return response()->json([
                'message' => 'Không có dữ liệu Movie đang chiếu nào !',
                'movie_chieu' => $movie_chieu,
                'movie_sapchieu' => $movie_sap_chieu,
                'movie_xuatchieu' => $movie_xuatchieu
            ], 404);
        }

        // check rỗng dữ liệu
        if ($movie_sap_chieu->isEmpty()) {

            return response()->json([
                'message' => 'Không có dữ liệu Movie sắp công chiếu nào !',
                'movie_chieu' => $movie_chieu,
                'movie_sapchieu' => $movie_sap_chieu,
                'movie_xuatchieu' => $movie_xuatchieu
            ], 404);
        }

        // check rỗng dữ liệu
        if ($movie_xuatchieu->isEmpty()) {

            return response()->json([
                'message' => 'Không có dữ liệu Movie có showtime nào !',
                'movie_chieu' => $movie_chieu,
                'movie_sapchieu' => $movie_sap_chieu,
                'movie_xuatchieu' => $movie_xuatchieu
            ], 404);
        }

        return response()->json([
            'message' => 'Hiện thị dữ liệu thành công',
            'movie_chieu' => $movie_chieu,
            'movie_sapchieu' => $movie_sap_chieu,
            'movie_xuatchieu' => $movie_xuatchieu

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
            'noi_dung' => 'required|string|max:5000',
            'trailer' => 'required|string|url|max:255',
            'gia_ve' => 'required|numeric|min:1',
            'hinh_thuc_phim' => 'required|numeric',  // 0 đang chiếu 1 sắp công chiếu 
            'loaiphim_ids' => 'required|array',
            'loaiphim_ids.*' => 'exists:moviegenres,id',
            'thoi_gian_phim' => 'required|numeric|min:1',
            'quoc_gia' => 'required|string|max:255',
        ]);


        // check tên phim đã có or tồn tại trong bảng chưa
        $checkNameMovie = Movie::where('ten_phim', $validated['ten_phim'])->exists();
        if ($checkNameMovie) {

            return response()->json([
                'message' => 'Tên phim này đã tồn tại rồi !',
            ], 422); //  422 là yêu cầu không hợp lệ
        }

        // xử lý upload ảnh phim
        if ($request->hasFile('anh_phim')) {
            $file = $request->file('anh_phim');
            $filename = $file->getClientOriginalName();
            $filePath = $file->storeAs('uploads/anh_phim', $filename, 'public');
            $validated['anh_phim'] = '/storage/' . $filePath;
        }

        // thêm mới 
        $movie = Movie::create($validated);
        // gắn thể loại phim vào phim
        $movie->movie_genres()->sync($request->loaiphim_ids);

        return response()->json([
            'message' => 'Thêm mới phim thành công',
            'data' => $movie->load('movie_genres'),
            'image_url' => asset($validated['anh_phim']),
        ], 201);
    }


    // show phim theo id truy vấn ở bảng trung gian thể loại phim
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


    // đưa đến from edit movie với các thông tin
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
                'message' =>  'Không có thể loại phim nào - thêm thể loại phim'
            ], 404);
        }

        return response()->json([
            'message' => 'Hiển thị phim theo id và thể loại',
            'movie' => $movieID->load('movie_genres'),
            'all_genre' => $allGenres,
        ], 200);
    }

    // cập nhật phim với các thông tin thay đổi 
    public function update(Request $request, string $id)
    {

        $request->validate([
            'ten_phim' => 'required|string|max:255',
            'anh_phim' => 'nullable|file|mimes:jpeg,png,jpg,gif|max:2048',
            'dao_dien' => 'required|string|max:255',
            'dien_vien' => 'required|string|max:255',
            'noi_dung' => 'required|string|max:5000',
            'trailer' => 'required|string|url|max:255',
            'gia_ve' => 'required|numeric|min:1',
            'hinh_thuc_phim' => 'required|numeric',
            'loaiphim_ids' => 'required|array',
            'loaiphim_ids.*' => 'exists:moviegenres,id',
            'thoi_gian_phim' => 'required|numeric|min:1',
            'quoc_gia' => 'required|string|max:255',
        ]);

        $movie = Movie::find($id);

        if (!$movie) {
            return response()->json([
                'message' => 'Không có phim theo id ' . $id
            ], 404);
        }

        // check nếu thay đổi tên phòng chiếu khác không được trùng với bản ghi id khác
        // nhưng được phép cùng với id bản ghi hiện tại
        $checkNameMovie = Movie::where('ten_phim', $request['ten_phim'])->where('id', '!=', $id)->exists();
        if ($checkNameMovie) {

            return response()->json([
                'message' => 'Tên phim này đã tồn tại rồi',
            ], 422); //  422 là yêu cầu không hợp lệ
        }

        $imagePath = $movie->anh_phim;

        // Xử lý ảnh phim
        if ($request->hasFile('anh_phim')) {
            if ($movie->anh_phim && file_exists(public_path($imagePath))) {
                unlink(public_path($imagePath));
            }

            $file = $request->file('anh_phim');
            $filename = $file->getClientOriginalName();
            $filePath = $file->storeAs('uploads/anh_phim', $filename, 'public');
            $imagePath = '/storage/' . $filePath;
        }

        // Cập nhật dữ liệu phim
        $movie->update([
            'ten_phim' => $request->ten_phim,
            'anh_phim' => $imagePath,
            'dao_dien' => $request->dao_dien,
            'dien_vien' => $request->dien_vien,
            'noi_dung' => $request->noi_dung,
            'trailer' => $request->trailer,
            'gia_ve' => $request->gia_ve,
            'hinh_thuc_phim' => $request->hinh_thuc_phim,
            'thoi_gian_phim' => $request->thoi_gian_phim,
            'quoc_gia' => $request->quoc_gia,
        ]);

        $movie->movie_genres()->sync($request->loaiphim_ids);

        return response()->json([
            'message' => 'Cập nhật thành công',
            'data' => $movie->load('movie_genres'),
            'image_url' => asset($movie->anh_phim),
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


    //1
    // chi tiết phim và đồ ăn showime theo phim đã thêm khi ấn vào phim đưa đến trang chi tiết phim
    //Phương thức movieDetail để nhóm và hiển thị showtime theo ngày

    public function movieDetail($movieID)
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

        // truy vấn đồ ăn với trạng thái là 0 có thể mua 
        $getFoodAll = DB::table('foods')->select('id', 'ten_do_an', 'anh_do_an', 'gia', 'ghi_chu', 'trang_thai')->where('trang_thai', 0)->get();

        if ($getFoodAll->isEmpty()) {
            return response()->json([
                'message' => 'Không có đồ ăn nào - thêm đồ ăn',
            ], 404);
        }

        if ($showtimes->isEmpty()) {
            return response()->json([
                'message' => 'Chưa có thông tin chiếu cho phim này | thêm xuất chiếu cho phim này | đồ ăn all ok',
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


    // 2
    // hàm khi ấn vào showtime theo ngày mong muốn sẽ đổ all gio_chieu có theo ngày ấn đó để chọn giờ chiếu sẽ đổ all theo giờ đó
    // phương thức để lấy tất cả giờ chiếu trong ngày khi chọn ngày

    public function getShowtimesByDate(Request $request, $movieID, $date)
    {

        // truy van showtime cho phim trong ngay da chon
        $showtimes = Showtime::where('phim_id', $movieID)
            ->whereDate('ngay_chieu', $date)->select('id', 'gio_chieu',)
            ->get()
            ->groupBy('gio_chieu');

        if ($showtimes->isEmpty()) {
            return response()->json(['message' => 'Không có suất chiếu nào cho ngày đã chọn.'], 404);
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


    // 3
    // khi ấn vào thời gian đổ ra room nếu có room nhiều phòng cùng 1 giờ
    public function getRoomsByShowtime(Request $request, $movieID, $date, $time)
    {
        // lấy phòng chiếu theo ngày và giờ đã chọn
        $roomsByTime = Showtime::where('phim_id', $movieID)
            ->whereDate('ngay_chieu', $date)
            ->where('gio_chieu', $time)
            ->select('id', 'phim_id', 'room_id', 'ngay_chieu', 'gio_chieu')
            ->with('room')
            ->get();

        if ($roomsByTime->isEmpty()) {
            return response()->json(['message' => 'Không có phòng chiếu nào cho giờ đã chọn.']);
        }

        // khởi tạo mảng để chứa phòng và ghế với trạng thái
        $roomsWithSeats = $roomsByTime->map(function ($showtime) {
            // lấy room_id của từng suất chiếu
            $roomID = $showtime->room_id;

            // lấy tất cả ghế của phòng chiếu đó
            $allSeats = Seat::where('room_id', $roomID)->whereNull('deleted_at')->get();

            // truy vấn trạng thái ghế từ bảng `seat_showtime_status`
            $seatStatuses = DB::table('seat_showtime_status')
                ->where('thongtinchieu_id', $showtime->id)
                ->select('ghengoi_id', 'trang_thai', 'user_id')
                ->get()
                ->keyBy('ghengoi_id'); // key là id ghế ngồi để truy xuất lấy dữ liệu

            // truy vấn trạng thái ghế bảo trì
            $maintenanceSeats = DB::table('seats')
                ->where('room_id', $roomID)
                ->where('trang_thai', 2) // Trạng thái bảo trì
                ->pluck('id');

                //dd($maintenanceSeats);

            // lấy trạng thái của các ghế (đã đặt, bảo trì hoặc trống)
            $seatsWithStatus = $allSeats->map(function ($seat) use ($seatStatuses, $maintenanceSeats) {
               
                if ($maintenanceSeats->contains($seat->id)) {
                    $status = 'Bảo trì';
                    $userId = null;
                } elseif ($seatStatuses->has($seat->id)) {
                    // nếu không phải bảo trì, kiểm tra trạng thái từ seat_showtime_status
                    $statusInfo = $seatStatuses->get($seat->id);
                    $userId = $statusInfo->user_id;
            
                    switch ($statusInfo->trang_thai) {
                        case 1:
                            $status = 'Đã đặt';
                            break;
                        case 3:
                            $status = 'Ghế đang trong hàng đợi';
                            break;
                        default:
                            $status = 'Trống';
                    }
                } else {
                    // trạng thái mặc định là trống
                    $status = 'Trống';
                    $userId = null;
                }
            
                return [
                    'id' => $seat->id,
                    'ten_ghe_ngoi' => $seat->so_ghe_ngoi,
                    'loai_ghe_ngoi' => $seat->loai_ghe_ngoi,
                    'gia_ghe' => $seat->gia_ghe,
                    'trang_thai' => $status,
                    'user_id' => $userId 
                ];
            });
            

            return [
                'room' => $showtime->room,
                'seats' => $seatsWithStatus,
            ];
        });

        return response()->json([
            'message' => 'Lấy danh sách phòng chiếu và trạng thái ghế thành công.',
            'roomsWithSeats' => $roomsWithSeats,
            
        ], 200);
    }
}
