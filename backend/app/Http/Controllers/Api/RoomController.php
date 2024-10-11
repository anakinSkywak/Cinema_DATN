<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Room;


use Illuminate\Http\Request;

class RoomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // show all room 
        $data = Room::all();

        if ($data->isEmpty()) {
            return response()->json([
                'message' => 'Không có dữ liệu rạp phim !'
            ], 200);
        }

        return response()->json([
            'message' => 'Xuất dữ liệu Room thành công',
            'data' => $data,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // thêm moi room
        // check cac truong 
        $validated = $request->validate([
            'ten_phong_chieu' => 'required|string|max:250',
            'tong_ghe_phong' => 'required|integer',
            'rapphim_id' => 'required|exists:theaters,id', // dam bao co rap theo id
        ]);

        // them moi phong chieu
        $room = Room::create($validated);

        // goi phuong thuc tao ghe ngoi o model Seat
        
        $room->addCreate(10);   // tạm test là 10

        // Lấy thông tin phòng chiếu cùng với rạp phim và ghế ngồi
        $roomWithDetails  = Room::with(['theater', 'seats'])->find($room->id);

        // tra ve khi them moi ok
        return response()->json([
            'message' => 'Thêm mới phòng chiếu phim va ghế ngồi thành công',
            'data' => $roomWithDetails 
        ], 201);    // tra về 201 them moi thanh cong
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // show room theo id
        $dataID = Room::find($id);


        if (!$dataID) {
            return response()->json([
                'message' => 'Không có dữ liệu Room theo id này',
            ], 404); // 404 ko có dữ liệu 
        }

        return response()->json([
            'message' => 'Lấy thông tin Room theo ID thành công',
            'data' => $dataID,
        ], 200);  // 200 có dữ liệu trả về
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // cap nhat room theo id 
        $dataID = Room::find($id);

        //check khi sửa de cap nhat 
        if (!$dataID) {
            return response()->json([
                'message' => 'Không có dữ liệu Room phim theo id này',
            ], 404);
        }
        // check cac truong 
        $validated = $request->validate([
            'ten_phong_chieu' => 'required|string|max:250',
            'tong_ghe_phong' => 'required|integer',
            'rapphim_id' => 'required|exists:theaters,id', // dam bao co rap theo id
        ]);

        // cap nhat
        $dataID->update($validated);

        // trả về 
        return response()->json([
            'message' => 'Cập nhật dữ liệu Room theo id thành công',
            'data' => $dataID
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(string $id)
    {
        // xoa theo id có softdelete
        $dataID = Room::find($id);

        // check xem co du lieu hay ko
        if (!$dataID) {
            return response()->json([
                'message' => 'Không có dữ liệu Room theo id này',
            ], 404);
        }

        $dataID->delete();

        return response()->json([
            'message' => 'Xóa Room theo id thành công'
        ], 200);
    }
}
