<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Food;
use App\Models\Movie;
use App\Models\MovieGenre;
use App\Models\Showtime;
use Illuminate\Http\Request;

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
        ], [
            'ten_phim.required' => 'Tên phim không được để trống !',
            'ten_phim.string' => 'Tên phim phải là chuỗi!',
            'ten_phim.max' => 'Tên phim tối đa 250 ký tự !'
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
                'message' => 'Không có phim theo id ' .$id
            ],404);
        }

        $allGenres = MovieGenre::all();
        if (!$allGenres) {
            return response()->json([
                'message' =>  'Không có thể loại phim nào' 
            ],404);
        }

        return response()->json([
            'message' => 'Hiển thị phim và thể loại',
            'movie' => $movieID->load('movie_genres'),
            'all_genre' => $allGenres,
        ],200);
    }

    // cập nhật phim với các thông tin thay đổi đang lỗi fix sau
    public function update(Request $request, string $id)
    {
        $movie = Movie::findOrFail($id);

        $validated = $request->validate([
            'ten_phim' => 'required|string|max:255',
            'anh_phim' => 'nullable|file|mimes:jpeg,png,jpg,gif|max:2048',
            'dao_dien' => 'required|string|max:255',
            'dien_vien' => 'required|string|max:255',
            'noi_dung' => 'required|string|max:255',
            'trailer' => 'required|string|max:255',
            'gia_ve' => 'required|numeric',
            'hinh_thuc_phim' => 'required|string|max:255',
            'loaiphim_ids' => 'required|array',
            'loaiphim_ids.*' => 'exists:moviegenres,id',
        ]);

        if ($request->hasFile('anh_phim')) {
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
            'image_url' => asset($movie->anh_phim),
        ]);
    }


    // xóa theo id
    public function delete(string $id)
    {
        $movieID = Movie::find($id);

        if (!$movieID) {
            return response()->json([
                'message' => 'Không tìm thấy phim theo id ' .$id
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


    // chi tiết phim , đổ all showtime theo phim đó , all ghế
    public function movieDetail($movieID)
    {

        // truy vấn show các showtime khi ấn vào phim theo id phim đó
        // truy vấn ấn vào phim đổ all thông tin phim đó theo id và các showtime theo id phim và ghế của phòng đó
        $movieDetailID = Movie::with(['showtimes.room.seat'])->find($movieID);

        $getFoodAll = Food::all();

        // check xem có showtime hay ko
        $checkShowtimes = Showtime::where('phim_id', $movieID)->exists();

        if (!$checkShowtimes) {
            return response()->json([
                'message' => 'Chưa có thông tin chiếu cho phim này | thêm thông tin chiếu cho phim',
                'movie-detail' => $movieDetailID   // trả về phim với các thông tin chiếu của phim đó
            ], 200);
        } else {
            return response()->json([
                'message' => 'Lấy thông tin phim và showtime đó theo id phim ok ',
                'movie-detail' => $movieDetailID, // trả về phim với các thông tin chiếu của phim đó
                'foods' => $getFoodAll,
            ], 200);
        }
    }
}
