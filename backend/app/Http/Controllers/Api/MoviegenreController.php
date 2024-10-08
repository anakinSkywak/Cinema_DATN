<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MovieGenre;
use Illuminate\Http\Request;

class MoviegenreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // show all MovieGenre  
        $data = MovieGenre::all();

        if ($data->isEmpty()) {
            return response()->json([
                'message' => 'Không có dữ liệu MovieGenre !'
            ], 200);
        }

        return response()->json([
            'message' => 'Xuất dữ liệu MovieGenre thành công',
            'data' => $data,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //

        // check cac truong khi them
        $validated = $request->validate([
            'ten_loai_phim' => 'required|string|max:255',
        ]);

        // them moi khi check ko co loi nao
        $data = MovieGenre::create($validated);

        // tra về dữ liêụ 
        return response()->json([
            'message' => 'Thêm mới loai phim thành công',
            'data' => $data
        ], 201); // 201 thêm mới thành công

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // show MovieGenre theo id
        $dataID = MovieGenre::find($id);


        if (!$dataID) {
            return response()->json([
                'message' => 'Không có dữ liệu MovieGenre theo id này',
            ], 404); // 404 ko có dữ liệu 
        }

        return response()->json([
            'message' => 'Lấy thông tin MovieGenre theo ID thành công',
            'data' => $dataID,
        ], 200);  // 200 có dữ liệu trả về
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // cap nhat MovieGenre theo id 
        $dataID = MovieGenre::find($id);

        //check khi sửa de cap nhat 
        if (!$dataID) {
            return response()->json([
                'message' => 'Không có dữ liệu MovieGenre phim theo id này',
            ], 404);
        }
        // check cac truong 
        $validated = $request->validate([
            'ten_loai_phim' => 'required|string|max:255',
        ]);

        // cap nhat
        $dataID->update($validated);

        // trả về 
        return response()->json([
            'message' => 'Cập nhật dữ liệu MovieGenre theo id thành công',
            'data' => $dataID
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(string $id)
    {
        // xoa theo id có softdelete
        $dataID = MovieGenre::find($id);

        // check xem co du lieu hay ko
        if (!$dataID) {
            return response()->json([
                'message' => 'Không có dữ liệu MovieGenre theo id này',
            ], 404);
        }

        $dataID->delete();

        return response()->json([
            'message' => 'Xóa MovieGenre theo id thành công'
        ], 200);
    }
}
