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
        $movieall = Movie::with('movie_genres')->orderBy('id', 'DESC')->get();
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
            'loaiphim_ids' => 'required|array',
            'loaiphim_ids.*' => 'exists:moviegenres,id',
            'thoi_gian_phim' => 'required|numeric',
        ]);

        // check ko chấp nhận kiểu ảnh webp : check sau

        // xu ly upload ảnh 

        if ($request->hasFile('anh_phim')) {
            $file = $request->file('anh_phim');
            $filename = $file->getClientOriginalName();
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
        try {
            // Tìm phim theo id
            $movie = Movie::find($id);

            if (!$movie) {
                return response()->json([
                    'message' => 'Không tìm thấy phim với id ' . $id
                ], 404);
            }

            // Xác thực dữ liệu đầu vào
            $validated = $request->validate([
                // Tên phim: chuỗi tối đa 255 ký tự, không bắt buộc
                'ten_phim' => 'sometimes|string|max:255',
                
                // Ảnh phim: file ảnh jpeg/png/jpg/gif tối đa 2MB nếu có upload, không bắt buộc
                'anh_phim' => $request->hasFile('anh_phim') ? 'file|mimes:jpeg,png,jpg,gif|max:2048' : 'nullable',
                
                // Đạo diễn: chuỗi tối đa 255 ký tự, không bắt buộc  
                'dao_dien' => 'sometimes|string|max:255',
                
                // Diễn viên: chuỗi tối đa 255 ký tự, không bắt buộc
                'dien_vien' => 'sometimes|string|max:255',
                
                // Nội dung: chuỗi tối đa 255 ký tự, không bắt buộc
                'noi_dung' => 'sometimes|string|max:255',
                
                // Trailer: URL hợp lệ tối đa 255 ký tự, không bắt buộc
                'trailer' => 'sometimes|string|url|max:255',
                
                // Giá vé: số, không bắt buộc
                'gia_ve' => 'sometimes|numeric',
                
                // Hình thức phim: chuỗi tối đa 255 ký tự, không bắt buộc
                'hinh_thuc_phim' => 'sometimes|string|max:255',
                
                // Mảng ID thể loại phim, không bắt buộc
                'loaiphim_ids' => 'sometimes|array',
                
                // Mỗi ID trong mảng phải tồn tại trong bảng moviegenres
                'loaiphim_ids.*' => 'exists:moviegenres,id',
                
                // Thời gian phim: số, không bắt buộc
                'thoi_gian_phim' => 'sometimes|numeric',
            ]);

            // Xử lý upload ảnh mới nếu có
            if ($request->hasFile('anh_phim')) {
                // Xóa ảnh cũ nếu tồn tại
                if ($movie->anh_phim && file_exists(public_path($movie->anh_phim))) {
                    unlink(public_path($movie->anh_phim));
                }

                $file = $request->file('anh_phim');
                $filename = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('uploads/anh_phim', $filename, 'public');
                $validated['anh_phim'] = '/storage/' . $filePath;
            } else {
                // Giữ nguyên ảnh cũ nếu không có ảnh mới
                unset($validated['anh_phim']);
            }

            // Cập nhật thông tin phim
            $movie->update($validated);
            
            // Cập nhật thể loại phim
            if (isset($validated['loaiphim_ids'])) {
                $movie->movie_genres()->sync($validated['loaiphim_ids']);
            }

            return response()->json([
                'message' => 'Cập nhật phim thành công',
                'data' => $movie->load('movie_genres'),
                'image_url' => asset($movie->anh_phim),
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Có lỗi xảy ra khi cập nhật phim',
                'error' => $e->getMessage()
            ], 500);
        }
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
        // Truy vấn thông tin phim và các showtimes của phim
        $movieDetailID = Movie::find($movieID);

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

        $getFoodAll = DB::table('foods')->select('id', 'ten_do_an', 'anh_do_an', 'gia', 'ghi_chu', 'trang_thai')->where('trang_thai' , 0)->get();

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


    // 3
    // khi ấn vào thời gian đổ ra room nếu có room nhiều phòng cùng 1 giờ
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
                // Xác định trạng thái của ghế
                if ($bookedSeats->contains($seat->id)) {
                    $status = 'đã đặt'; // Ghế đã được đặt
                } elseif ($maintenanceSeats->contains($seat->id)) {
                    $status = 'bảo trì'; // Ghế đang bảo trì
                } else {
                    $status = 'trống'; // Ghế còn lại là trống
                }
    
                return [
                    'id' => $seat->id,
                    'ten_ghe_ngoi' => $seat->so_ghe_ngoi, // Tên ghế (số ghế ngồi)
                    'trang_thai' => $status // Trạng thái ghế
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




    // bỏ
    public function getSeatsByShowtime($movieID, $showtimeID)
    {
        // Truy vấn thông tin suất chiếu cụ thể (showtime)
        $showtime = Showtime::with(['room'])->find($showtimeID);


        if (!$showtime) {
            return response()->json([
                'message' => 'Không tìm thấy thông tin suất chiếu.'
            ], 404);
        }

        // Kiểm tra xem showtime c thuộc về bộ phim không
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
    } // bỏ
}
