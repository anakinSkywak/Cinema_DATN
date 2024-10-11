<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Food;
use Illuminate\Http\Request;

class FoodController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // xuat all 
        $data = Food::all();

        if ($data->isEmpty()) {
            return response()->json([
                'message' => 'Không có dữ liệu Foods !'
            ], 200);
        }

        return response()->json([
            'message' => 'Xuất all dữ liệu Foods thành công',
            'data' => $data,
        ], 200);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 
        // check cac truong 
        $validated = $request->validate([
            'ten_do_an' => 'required|string|max:250',
            'gia' => 'required|numeric',
            'ghi_chu' => 'required|string|max:250',
        ]);

        // them moi food
        $room = Food::create($validated);

        // tra ve khi them moi ok
        return response()->json([
            'message' => 'Thêm mới Food thành công',
            'data' => $room
        ], 201);    // tra về 201 them moi thanh cong

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // show theo id
        // show food theo id
        $dataID = Food::find($id);


        if (!$dataID) {
            return response()->json([
                'message' => 'Không có dữ liệu Food theo id này',
            ], 404); // 404 ko có dữ liệu 
        }

        return response()->json([
            'message' => 'Lấy thông tin Food theo ID thành công',
            'data' => $dataID,
        ], 200);  // 200 có dữ liệu trả về
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //

        // cap nhat food theo id 
        $dataID = Food::find($id);

        //check khi sửa de cap nhat 
        if (!$dataID) {
            return response()->json([
                'message' => 'Không có dữ liệu Food theo id này',
            ], 404);
        }
        // check cac truong 
        $validated = $request->validate([
            'ten_do_an' => 'required|string|max:250',
            'gia' => 'required|numeric',
            'ghi_chu' => 'required|string|max:250',
        ]);

        // cap nhat
        $dataID->update($validated);

        // trả về 
        return response()->json([
            'message' => 'Cập nhật dữ liệu Food id thành công',
            'data' => $dataID
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(string $id)
    {
        // xoa theo id
        $dataID = Food::find($id);

        if (!$dataID) {
            return response()->json([
                'message' => 'Không có dữ liệu Food theo id này !',
            ], 404);
        }

        $dataID->delete();

        return response()->json([
            'message' => 'Xóa Booking theo id thành công'
        ], 200);
    }
}
