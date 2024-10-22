<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MovieGenre;
use Illuminate\Http\Request;

class MoviegenreController extends Controller
{


    public function index()
    {
        // show all MovieGenre  
        $moviegenreall = MovieGenre::all();

        if ($moviegenreall->isEmpty()) {
            return response()->json([
                'message' => 'Không có dữ liệu MovieGenre !'
            ], 200);
        }

        return response()->json([
            'message' => 'Xuất dữ liệu MovieGenre thành công',
            'data' => $moviegenreall,
        ], 200);
    }


    public function store(Request $request)
    {


        // check cac truong khi them
        $validated = $request->validate([
            'ten_loai_phim' => 'required|string|max:255',
        ]);

        // them moi khi check ko co loi nao
        $moviegenre = MovieGenre::create($validated);

        // tra về dữ liêụ 
        return response()->json([
            'message' => 'Thêm mới loai phim thành công',
            'data' => $moviegenre
        ], 201); // 201 thêm mới thành công

    }

    
    public function show(string $id)
    {
        // show MovieGenre theo id
        $moviegenreID = MovieGenre::find($id);

        if (!$moviegenreID) {
            return response()->json([
                'message' => 'Không có dữ liệu MovieGenre theo id này',
            ], 404); // 404 ko có dữ liệu 
        }

        return response()->json([
            'message' => 'Lấy thông tin MovieGenre theo ID thành công',
            'data' => $moviegenreID,
        ], 200);  // 200 có dữ liệu trả về
    }


    // đưa đến trang edit đỏ dữ liệu ra theo id
    public function edit(string $id)
    {
        // show MovieGenre theo id
        $moviegenreID = MovieGenre::find($id);

        if (!$moviegenreID) {
            return response()->json([
                'message' => 'Không có dữ liệu MovieGenre theo id này',
            ], 404); // 404 ko có dữ liệu 
        }

        return response()->json([
            'message' => 'Lấy thông tin MovieGenre theo ID để edit thành công',
            'data' => $moviegenreID,
        ], 200);  // 200 có dữ liệu trả về
    }

   
    public function update(Request $request, string $id)
    {
        // cap nhat MovieGenre theo id 
        $moviegenreID = MovieGenre::find($id);

        //check khi sửa de cap nhat 
        if (!$moviegenreID) {
            return response()->json([
                'message' => 'Không có dữ liệu MovieGenre phim theo id này',
            ], 404);
        }
        // check cac truong 
        $validated = $request->validate([
            'ten_loai_phim' => 'required|string|max:255',
        ]);

        // cap nhat
        $moviegenreID->update($validated);

        // trả về 
        return response()->json([
            'message' => 'Cập nhật dữ liệu MovieGenre theo id thành công',
            'data' => $moviegenreID
        ], 200);
    }

   
    public function delete(string $id)
    {
        // xoa theo id có softdelete
        $moviegenreID = MovieGenre::find($id);

        // check xem co du lieu hay ko
        if (!$moviegenreID) {
            return response()->json([
                'message' => 'Không có dữ liệu MovieGenre theo id này',
            ], 404);
        }

        $moviegenreID->delete();

        return response()->json([
            'message' => 'Xóa MovieGenre theo id thành công'
        ], 200);
    }
}
