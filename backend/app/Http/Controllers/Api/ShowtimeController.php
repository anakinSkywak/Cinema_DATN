<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Showtime;
use Illuminate\Http\Request;

class ShowtimeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // xuat all
        $data = Showtime::with(['movie', 'theater', 'room'])->get();

        if (!$data) {
            return response()->json([
                'message' => 'Không tìm thấy suất chiếu'
            ], 404);
        }

        return response()->json([
            'message' => 'Lấy thông tin suất chiếu thành công',
            'data' => $data,
        ], 200);  // 200 có dữ liệu trả về
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // them moi show tham , nhieu show tham cho phim de user booking
        // check khi them
        $validated = $request->validate([
            'ngay_chieu' => 'required|date',
            'thoi_luong_chieu' => 'required|string|max:250',
            'phim_id' => 'required|exists:movies,id',
            'rapphim_id' => 'required|exists:theaters,id',
            'room_id' => 'required|exists:rooms,id',
            'gio_chieu' => 'required|date_format:H:i'
        ]);

        // check chieu trung lap khi them moi
        // $checkTimes = Showtime::where('ngay_chieu', $request->ngay_chieu)
        //     ->where('gio_chieu', $request->gio_chieu)
        //     ->where('room_id', $request->room_id)
        //     ->exists();

        // if ($checkTimes) {
        //     return response()->json([
        //         'error' => 'Giờ chiếu này đã được thêm mới trong phòng này.',
        //     ], 400);
        // }

        // truy van them xuat chieu moi 
        $showtiems = Showtime::create($validated);

        // tra ve neu them ok
        return response()->json([
            'message' => 'Thêm mới showtime thành công',
            'data' => $showtiems
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // show du lieu theo id

        // Lấy suất chiếu theo id cùng với thông tin phim, rạp và phòng chiếu
        $showtimeID = Showtime::with(['movie', 'theater', 'room'])->find($id);

        if (!$showtimeID) {
            return response()->json([
                'message' => 'Không tìm thấy suất chiếu theo id này'
            ], 404);
        }

        return response()->json([
            'message' => 'Lấy thông tin suất chiếu theo id thành công',
            'data' => $showtimeID,
        ], 200);  // 200 có dữ liệu trả về
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // cap nhat theo id

        // Tìm và cập nhật suất chiếu
        $showtimeID = Showtime::find($id);

        if (!$showtimeID) {
            return response()->json([
                'message' => 'Không có dữ liệu Showtime phim theo id này',
            ], 404);
        }

        // Xác thực dữ liệu đầu vào
        $validated = $request->validate([
            'ngay_chieu' => 'required|date',
            'thoi_luong_chieu' => 'required|integer',
            'phim_id' => 'required|exists:movies,id',
            'rapphim_id' => 'required|exists:theaters,id',
            'room_id' => 'required|exists:rooms,id',
        ]);

        // cap nhat
        $showtimeID->update($validated);

        // trả về 
        return response()->json([
            'message' => 'Cập nhật dữ liệu Showtime theo id thành công',
            'data' => $showtimeID
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(string $id)
    {
        // xoa theo id
        $showtimeID = Showtime::find($id);

        // check xem co du lieu hay ko
        if (!$showtimeID) {
            return response()->json([
                'message' => 'Không có dữ liệu Showtime theo id này',
            ], 404);
        }

        $showtimeID->delete();

        return response()->json([
            'message' => 'Xóa Showtime theo id thành công'
        ], 200);
    }
}
