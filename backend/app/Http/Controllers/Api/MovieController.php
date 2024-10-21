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


    public function index()
    {
        // call show all du lieu ra 
        $data = Movie::with('movie_genres')->get();
        //dd($data);
        if ($data->isEmpty()) {
            return response()->json([
                'message' => 'Không có dữ liệu Movie nào'
            ], 200);
        }

        return response()->json([
            'message' => 'Hiện thị dữ liệu thành công',
            'data' => $data
        ]);
    }



    public function getMovieGenre()
    {
        $getmoviegenre = MovieGenre::all();

        if ($getmoviegenre->isEmpty()) {
            return response()->json([
                'message' => 'Không có thể loại phim nào hãy thêm thể loại phim !!!'
            ], 200);
        }

        return response()->json([
            'message' => 'Thể loại phim',
            'data' => $getmoviegenre
        ], 200);
    }

    public function store(Request $request)
    {
        // call api them movie 
        // check cac truong khi them 
        //dd($request->all());
        $validated = $request->validate([
            'ten_phim' => 'required|string|max:255',
            'anh_phim' => 'required|file|mimes:jpeg,png,jpg,gif|max:2048', // xác thực file hình ảnh
            'dao_dien' => 'required|string|max:255',
            'dien_vien' => 'required|string|max:255',
            'noi_dung' => 'required|string|max:255',
            'trailer' => 'required|string|url|max:255',
            'gia_ve' => 'required|numeric',
            'hinh_thuc_phim' => 'required|string|max:255',
            // 'danh_gia' => 'required|numeric|min:0|max:10',
            'loaiphim_ids' => 'required|array', // Xác thực mảng thể loại phim
            'loaiphim_ids.*' => 'exists:moviegenres,id', // Xác thực các thể loại phim tồn tại
        ]);

        // check ko chấp nhận kiểu ảnh webp : check sau
        

        // xu ly upload ảnh 
        if ($request->hasFile('anh_phim')) {
            $file = $request->file('anh_phim');
            $filename = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('uploads/anh_phim', $filename, 'public');
            $validated['anh_phim'] = '/storage/' . $filePath;
        }

        // tạo mới phim 
        $movie = Movie::create($validated);

        // liên kết phim với nhiều loại phim
        $movie->movie_genres()->sync($request->loaiphim_ids);  // luu nhieu loai phim

        // Trả về kết quả thành công
        return response()->json([
            'message' => 'Thêm mới phim và các thể loại thành công',
            'data' => $movie->load(relations: 'movie_genres'),  // trả về cả thông tin thể loại phim đã lưu
            'image_url' => asset($validated['anh_phim']), // trả về đường dẫn ảnh phim
        ], 201);
    }



    public function show(string $id)
    {
        // show movie theo id
        $dataID = Movie::with('movie_genres')->find($id); // dung with nlay thong tin o bang trung gian

        // check 
        if (!$dataID) {
            return response()->json([
                'message' => 'Không có dữ liệu Movie theo id'
            ], 404);
        }

        return response()->json([
            'message' => 'Dữ liệu show theo ID thành công',
            'data' => $dataID,
        ]);
    }


    public function showEditID(Request $request, string $id)
    {
        // khi ấn vào edit theo id sẽ đưa đến from đổ all dữ liệu phim theo id đó
        // đổ thêm all thể loại phim để chọn nếu có thay đổi

        // Lấy phim dựa trên id
        $movie = Movie::findOrFail($id);

        // lấy tất cả các thể loại phim để chọn phim khác khi update
        $allGenre = MovieGenre::all();

        // Trả về dữ liệu phim (có thể trả về view hoặc JSON tuỳ theo nhu cầu)
        return response()->json([
            'message' => 'show thông tin phim theo id , đổ all thể loại phim để chọn khi thay đổi ok',
            'movie' => $movie->load('movie_genres'),  // Trả về phim kèm theo thể loại
            'all_genre' => $allGenre, // đổ all thể loại phim ra để chọn chỉnh sửa để update
        ], 200);
    }


    public function update(Request $request, string $id)
    {

        $movieID = Movie::findOrFail($id); // lấy dữ liệu phim hiện tại

        $validated = $request->validate([
            'ten_phim' => 'required|string|max:255',
            'anh_phim' => 'nullable|file|mimes:jpeg,png,jpg,gif|max:2048',
            'dao_dien' => 'required|string|max:255',
            'dien_vien' => 'required|string|max:255',
            'noi_dung' => 'required|string|max:255',
            'trailer' => 'required|string|max:255',
            'gia_ve' => 'required|numeric',
            'hinh_thuc_phim' => 'required|string|max:255',
            'loaiphim_ids' => 'required|array', // Xác thực mảng thể loại phim
            'loaiphim_ids.*' => 'exists:moviegenres,id', // Xác thực các thể loại phim tồn tại
        ]);

        // kiểm tra xem có thay đổi ảnh không
        if ($request->hasFile('anh_phim')) {
            // xóa ảnh cũ
            if ($movieID->anh_phim) {
                $oldImagePath = public_path(str_replace('/storage', 'storage', $movieID->anh_phim));
                //$oldImagePath = public_path($movieID->anh_phim);

                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            // nếu có xóa , upload ảnh mới lên
            $file = $request->file('anh_phim');
            $filename = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('uploads/anh_phim', $filename, 'public');
            $validated['anh_phim'] = '/storage/' . $filePath;
        } else {

            // k xóa để lại ảnh cũ
            $validated['anh_phim'] = $movieID->anh_phim;
        }

        // cập nhật phim
        $movieID->update($validated);

        // cập nhật thể loại phim nếu thay đổi
        $movieID->movie_genres()->sync($request->loaiphim_ids);

        // trẻ về nếu ok
        return response()->json([
            'message' => 'Cập nhật dữ liệu mới cho Movie thành công ',
            'data' => $movieID->load('movie_genres'),
            //'image_url' => asset($movieID->anh_phim), // trả về đường dẫn ánh mới hoặc cũ 
            'image_url' => asset('storage/' . str_replace('storage/', '', $movieID->anh_phim)),

        ], 200);

        // {
        //     "ten_phim": "Phim Aaaa",
        //     "dao_dien": "Đạo diễn Baa",
        //     "dien_vien": "Diễn viên Caa",
        //     "noi_dung": "Nội dung phimaaa",
        //     "trailer": "https://example.com/trailer.mp4",
        //     "gia_ve": 120,
        //     "hinh_thuc_phim": "2D",
        //     "loaiphim_ids": [10 , 9]
        // }

    }



    public function delete(string $id)
    {
        // xoa theo id
        $dataID = Movie::with('movie_genres')->find($id);
        // $dataID = Movie::find($id); // Ánh :  loi error cdm loi
        if (!$dataID) {
            return response()->json([
                'message' => 'Không có dữ liệu Movie theo id này',
            ], 404);
        }

        $dataID->delete();

        return response()->json([
            'message' => 'Xóa phim theo id thành công'
        ], 200);
    }

    // lọc phim theo thể loại
    public function movieFilter(string $id)
    {
        $dataID =   Movie::with('movie_genres')
            ->whereHas('movie_genres', function ($query) use ($id) {
                $query->where('moviegenres.id', $id); // Chỉ định rõ ràng tên bảng
            })
            ->get();
        if ($dataID->isEmpty()) {
            return response()->json([
                'message' => 'Không có dữ liệu Movie theo id này',
            ], 404);
        }
        return response()->json([
            'data' => $dataID,
        ], 200);
    }

    // tìm kiếm phịm theo từ khóa
    public function movieFilterKeyword(Request $request)
    {
        $keyword = $request->input('keyword');

        $dataID =  Movie::with('movie_genres')->where('ten_phim', 'like', '%' . $keyword . '%')->get();
        if ($dataID->isEmpty()) {
            return response()->json([
                'message' => 'Không có dữ liệu Movie theo theo từ khóa này',
            ], 404);
        }
        return response()->json([
            'data' => $dataID,
        ], 200);
    }


    // hàm xem chi tiết phim và show all showtime của phim đó đã thêm để user lựa chọn booking
    public function movie_detail($movieID)
    {
        
        // truy vấn show các showtime khi ấn vào phim theo id phim đó
        // truy vấn ấn vào phim đổ all thông tin phim đó theo id và các showtime theo id phim và ghế của phòng đó
        $movieDetailID = Movie::with(['showtimes.room.seat'])->findOrFail($movieID);

        $getFoodAll = Food::all();
        // check xem có showtime hay ko
        $checkShowtimes = Showtime::where('phim_id', $movieID)->exists();

        if (!$checkShowtimes) {
            return response()->json([
                'message' => 'Chưa có thông tin chiếu cho phim này | thêm thông tin chiếu cho phim',
                'data' => $movieDetailID   // trả về phim với các thông tin chiếu của phim đó
            ], 200);
        } else {
            return response()->json([
                'message' => 'Lấy thông tin phim và showtime đó theo id phim ok ',
                'data' => $movieDetailID, // trả về phim với các thông tin chiếu của phim đó
                'foods' => $getFoodAll,
            ], 200);
        }
    }
}
