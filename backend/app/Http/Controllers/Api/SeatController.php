<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Seat;
use Illuminate\Http\Request;

class SeatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // call api xuat all seats 
        $data = Seat::all();
        
        // check rỗng nếu ko co dữ liệu trả về thông báo
        if ($data->isEmpty()) {
            return response()->json([
                'message' => 'Không có dữ liệu nào của ghế ! .',
            ], 200);
        }

        // trả về dữ liệu
        return response()->json([
            'message' => 'Lấy All dữ liệu rạp phim thành công ',
            'data' => $data,
        ], 200);
    }
    

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // show seat theo id
        $dataID = Seat::find($id);


        if (!$dataID) {
            return response()->json([
                'message' => 'Không có dữ liệu Seat theo id này',
            ], 404); // 404 ko có dữ liệu 
        }

        return response()->json([
            'message' => 'Lấy thông tin Seat theo ID thành công',
            'data' => $dataID,
        ], 200);  // 200 có dữ liệu trả về
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // cap nhat seat theo id 
        $dataID = Seat::find($id);

        //check khi sửa de cap nhat 
        if (!$dataID) {
            return response()->json([
                'message' => 'Không có dữ liệu Seat theo id này !',
            ], 404);
        }
        // check cac truong 
        $validated = $request->validate([
            'so_ghe_ngoi' => 'required|string|max:250',
            'loai_ghe_ngoi' => 'required|string|max:250',
            
        ]);

        // cap nhat
        $dataID->update($validated);

        // trả về 
        return response()->json([
            'message' => 'Cập nhật dữ liệu Seat theo id thành công',
            'data' => $dataID
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(string $id)
    {
         // xoa theo id
         $dataID = Seat::find($id);

         // check xem co du lieu hay ko
         if (!$dataID) {
             return response()->json([
                 'message' => 'Không có dữ liệu seat theo id này !',
             ], 404);
         }
 
         $dataID->delete();
 
         return response()->json([
             'message' => 'Xóa seat theo id thành công'
         ], 200);
    }
}
