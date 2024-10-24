<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Theater;

class TheaterController extends Controller
{

    // xuat all rap
    public function index()
    {
        // call api xuat all theatrs 
        $theaterall  = Theater::all();

        if ($theaterall->isEmpty()) {
            return response()->json([
                'message' => 'Không có dữ liệu nào của rạp phim ! .',
            ], 404);
        }

        // trả về dữ liệu
        return response()->json([
            'message' => 'Lấy All dữ liệu rạp phim thành công ',
            'data' => $theaterall,
        ], 200);
    }

    
    // thêm mới rạp phim
    public function store(Request $request)
    {

        // check cac truong khi them
        $validated = $request->validate([
            'ten_rap' => 'required|string|max:255',
            'dia_diem' => 'required|string|max:255',
            'tong_ghe' => 'required|integer'
        ]);

        // them moi khi check ko co loi nao
        $data = Theater::create($validated);

        // tra về dữ liêụ 
        return response()->json([
            'message' => 'Thêm mới rạp phim thành công',
            'data' => $data
        ], 201); // 201 thêm mới thành công

    }

    

    // show theo id
    public function show(string $id)
    {
        // lấy thông tin rạp theo id
        $theateraID = Theater::find($id);

        if (!$theateraID) {
            return response()->json([
                'message' => 'Không có dữ liệu rạp phim theo id này',
            ], 404); // 404 ko có dữ liệu 
        }

        return response()->json([
            'message' => 'Lấy thông tin rạp phim theo ID thành công',
            'data' => $theateraID,
        ], 200);  // 200 có dữ liệu trả về
    }


    // đưa đến trang edit với thông tin theo id
    public function editTheaterID(Request $request, string $id)
    {
        // đổ dữ liệu theo id ra khi ấn nút edit theo id
        $dataID = Theater::find($id);

        if (!$dataID) {
            return response()->json([
                'message' => 'Không có dữ liệu rạp phim theo id này' .$id,
            ], 404); // 404 ko có dữ liệu 
        }

        // trả về 
        return response()->json($dataID);
    }


    // cập nhật theo id
    public function update(Request $request, string $id)
    {
        // cap nhat rap phim theo id
        $dataID = Theater::find($id);

        // kiểm tra xem có dữ liêụ theo id đó ko
        if (!$dataID) {
            return response()->json([
                'message' => 'Không có dữ liệu rạp phim theo id này'.$id,
            ], 404);
        }
        //check khi sửa dữ liệu
        $validated = $request->validate([
            'ten_rap' => 'required|string|max:255',
            'dia_diem' => 'required|string|max:255',
            'tong_ghe' => 'required|integer'
        ]);

        // them moi khi check xong
        $dataID->update($validated);

        // trả về 
        return response()->json([
            'message' => 'Cập nhật dữ liệu rạp phim theo id thành công',
            'data' => $dataID
        ], 200);
    }


    // xóa theo id
    public function delete(string $id)
    {
        // xoa theo id
        $dataID = Theater::find($id);

        // check xem co du lieu hay ko
        if (!$dataID) {
            return response()->json([
                'message' => 'Không có dữ liệu rạp phim theo id này' .$id,
            ], 404);
        }

        $dataID->delete();

        return response()->json([
            'message' => 'Xóa rạp phim theo id thành công'
        ], 200);
    }
}
