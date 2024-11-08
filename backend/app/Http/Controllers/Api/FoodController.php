<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Food;
use Illuminate\Http\Request;

class FoodController extends Controller
{


    // xuất all đồ ăn
    public function index()
    {
        // xuat all 
        $foodall = Food::all();

        if ($foodall->isEmpty()) {
            return response()->json([
                'message' => 'Không có dữ liệu Foods !'
            ], 404);
        }

        return response()->json([
            'message' => 'Xuất all dữ liệu Foods thành công',
            'data' => $foodall,
        ], 200);
    }


    // thêm mới đồ ăn
    public function store(Request $request)
    {

        // check cac truong 
        $validated = $request->validate([
            'ten_do_an' => 'required|string|max:250',
            'gia' => 'required|numeric',
            'ghi_chu' => 'required|string|max:250',
        ]);

        // them moi food
        $food = Food::create($validated);

        // tra ve khi them moi ok
        return response()->json([
            'message' => 'Thêm mới Food thành công',
            'data' => $food
        ], 201);    // tra về 201 them moi thanh cong

    }


    // show đồ ăn theo id
    public function show(string $id)
    {
        // show theo id
        // show food theo id
        $foodID = Food::find($id);


        if (!$foodID) {
            return response()->json([
                'message' => 'Không có dữ liệu Food theo id này',
            ], 404); // 404 ko có dữ liệu 
        }

        return response()->json([
            'message' => 'Lấy thông tin Food theo ID thành công',
            'data' => $foodID,
        ], 200);  // 200 có dữ liệu trả về
    }


    // đưa đến trang edit đổ all dữ liệu theo id
    public function edit(string $id)
    {
        // show food theo id
        $foodID = Food::find($id);

        if (!$foodID) {
            return response()->json([
                'message' => 'Không có dữ liệu Food theo id này',
            ], 404); // 404 ko có dữ liệu 
        }

        return response()->json([
            'message' => 'Lấy thông tin Food theo ID để edit ok ',
            'data' => $foodID,
        ], 200);  // 200 có dữ liệu trả về
    }


    // cập nhật
    public function update(Request $request, string $id)
    {
        // cap nhat food theo id 
        $foodID = Food::find($id);

        //check khi sửa de cap nhat 
        if (!$foodID) {
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
        $foodID->update($validated);

        // trả về 
        return response()->json([
            'message' => 'Cập nhật dữ liệu Food id thành công',
            'data' => $foodID
        ], 200);
    }


    // xóa theo id
    public function delete(string $id)
    {
        // xoa theo id
        $foodID = Food::find($id);

        if (!$foodID) {
            return response()->json([
                'message' => 'Không có dữ liệu Food theo id này !',
            ], 404);
        }

        $foodID->delete();

        return response()->json([
            'message' => 'Xóa Booking theo id thành công'
        ], 200);
    }
}
