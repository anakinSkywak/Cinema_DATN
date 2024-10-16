<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    /**
     * Display a listing of the resource.
     */
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

    /**
     * Store a newly created resource in storage.
     */
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
            'noi_dung' => 'required|string',
            'trailer' => 'required|string|url|max:255',
            'gia_ve' => 'required|numeric',
            // 'danh_gia' => 'required|numeric|min:0|max:10',
            'loaiphim_ids' => 'required|array', // Xác thực mảng thể loại phim
            'loaiphim_ids.*' => 'exists:moviegenres,id', // Xác thực các thể loại phim tồn tại
        ]);

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

        // {
        //     "ten_phim": "Phim Ahffgggggg",
        //     "anh_phim": "movie-image.jpg",
        //     "dao_dien": "Đạo diễn X",
        //     "dien_vien": "anh , anh , yeu",
        //     "noi_dung": "Nội dung phim ABC",
        //     "trailer": "https://example.com/trailer.mp4",
        //     "gia_ve": 120000,
        //     "loaiphim_ids": [11 , 9 , 10]
        // }


    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // show movie theo id
        $dataID = Movie::with('movie_genres')->find($id); // dung with nap lay thong tin o bang trung gian

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

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // cap nhat moi du lieu cho movie
        // tìm phim theo id
        $dataID = Movie::find($id);

        // chekc khi sua du lieu
        $validated = $request->validate([
            'ten_phim' => 'required|string|max:255',
            'anh_phim' => 'required|string|max:255',
            'dao_dien' => 'required|string|max:255',
            'dien_vien' => 'required|string|max:255',
            'noi_dung' => 'required|string',
            'trailer' => 'required|string|max:255',
            'gia_ve' => 'required|numeric',
            // 'danh_gia' => 'required|numeric|min:0|max:10',
            'loaiphim_ids' => 'required|array', // Xác thực mảng thể loại phim
            'loaiphim_ids.*' => 'exists:moviegenres,id', // Xác thực các thể loại phim tồn tại
        ]);

        // cap nhat du lieu moi cho phim 
        $dataID->update($validated);

        // cap nhat the loai phim moi khi thay doi
        $dataID->movie_genres()->sync($request->loaiphim_ids);

        // tra ve neu cap nhat thanh cong
        return response()->json([
            'message' => 'Cập nhật dữ liệu mới cho Movie thành công ',
            'data' => $dataID->load('movie_genres'),
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
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
    public function movieFilter(string $id){
        $dataID =   Movie::with('movie_genres')
        ->whereHas('movie_genres', function($query) use ($id) {
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
    public function movieFilterKeyword(Request $request){
        $keyword = $request->input('keyword'); 

        $dataID =  Movie::with('movie_genres')->where('ten_phim', 'like', '%'.$keyword.'%')->get();
        if ($dataID->isEmpty()) {
            return response()->json([   
                'message' => 'Không có dữ liệu Movie theo theo từ khóa này',
            ], 404);
        }
        return response()->json([
            'data' => $dataID,
        ], 200);
    }
}
