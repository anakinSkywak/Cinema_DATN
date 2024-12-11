<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Food;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FoodController extends Controller
{


    // xuất all đồ ăn
    public function index()
    {

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

        $validated = $request->validate([
            'ten_do_an' => 'required|string|max:250',
            'gia' => 'required|numeric',
            'ghi_chu' => 'required|string|max:250',
            'anh_do_an' => 'required|file|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // check trùng với tên đồ ăn khác trong db khi thêm mới
        $checkNameFood = DB::table('foods')->where('ten_do_an', $request->ten_do_an)->exists();
        if ($checkNameFood) {
            return response()->json([
                'message' => 'Tên đồ ăn này đã tồn tại !',
            
            ], 422);
        }

        if ($request->hasFile('anh_do_an')) {
            $file = $request->file('anh_do_an');
            $filename = $file->getClientOriginalName();
            $filePath = $file->storeAs('uploads/anh_do_an', $filename, 'public');
            $validated['anh_do_an'] = '/storage/' . $filePath;
        }

        $food = Food::create($validated);

        // tra ve khi them moi ok
        return response()->json([
            'message' => 'Thêm mới Food thành công',
            'data' => $food,
            'image_url' => asset($validated['anh_do_an']),
        ], 201);
    }


    // show đồ ăn theo id
    public function show(string $id)
    {

        $foodID = Food::find($id);
        if (!$foodID) {
            return response()->json([
                'message' => 'Không có dữ liệu Food theo id này',
            ], 404);
        }

        return response()->json([
            'message' => 'Lấy thông tin Food theo ID thành công',
            'data' => $foodID,
        ], 200);
    }


    // đưa đến trang edit đổ all dữ liệu theo id
    public function edit(string $id)
    {

        $foodID = Food::find($id);
        if (!$foodID) {
            return response()->json([
                'message' => 'Không có dữ liệu Food theo id này',
            ], 404);
        }

        return response()->json([
            'message' => 'Lấy thông tin Food theo ID để edit ok ',
            'data' => $foodID,
        ], 200);
    }


    // cập nhật
    public function update(Request $request, string $id)
    {

        $request->validate([
            'ten_do_an' => 'required|string|max:250',
            'gia' => 'required|numeric',
            'ghi_chu' => 'required|string|max:250',
            'anh_do_an' => 'nullable|file|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $foodID = Food::find($id);

        if (!$foodID) {
            return response()->json([
                'message' => 'Không có dữ liệu Food theo id này',
            ], 404);
        }

         // check trùng với tên đồ ăn khác trong db nhưng ko phải của đồ ăn này
         $checkNameFood = DB::table('foods')->where('ten_do_an', $request->ten_do_an)->where('id' , '!=' , $id)->exists();
         if ($checkNameFood) {
             return response()->json([
                 'message' => 'Tên đồ ăn thay đổi này đã tồn tại với đồ ăn khác!',
             
             ], 422);
         }

        $imagePath = $foodID->anh_do_an;

        if ($request->hasFile('anh_do_an')) {
            if ($foodID->anh_do_an && file_exists(public_path($imagePath))) {
                unlink(public_path($imagePath));
            }

            $file = $request->file('anh_do_an');
            $filename = $file->getClientOriginalName();
            $filePath = $file->storeAs('uploads/anh_do_an', $filename, 'public');
            $imagePath = '/storage/' . $filePath;
        }

        $foodID->update([
            'ten_do_an' => $request->ten_do_an,
            'gia' => $request->gia,
            'ghi_chu' => $request->ghi_chu,
            'anh_do_an' => $imagePath,
        ]);

        return response()->json([
            'message' => 'Cập nhật dữ liệu Food id thành công',
            'data' => $foodID,
            'image_url' => asset($foodID->anh_do_an),
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

    // hàm dừng bán đồ ăn theo id
    public function stopFood(string $id)
    {

        $foodID = Food::find($id);
        if (!$foodID) {
            return response()->json([
                'message' => 'Không có đồ ăn theo id' . $foodID,
            ], 404);
        }

        // cập nhật trạng thái là 2 bảo trị lỗi
        $foodID->update(['trang_thai' => 1]);

        return response()->json([
            'message' => 'Dừng bán đồ ăn này theo id ' . $foodID,
            'data' => $foodID
        ], 200);
    }

    public function openFood(string $id)
    {

        $foodID = Food::find($id);
        if (!$foodID) {
            return response()->json([
                'message' => 'Không có đồ ăn theo id' . $foodID,
            ], 404);
        }

        // cập nhật trạng thái là 0 mở bán
        $foodID->update(['trang_thai' => 0]);
        return response()->json([
            'message' => 'Mở bán đồ ăn này theo id ' . $foodID,
            'data' => $foodID
        ], 200);
    }
}
