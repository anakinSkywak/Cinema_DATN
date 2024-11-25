<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Seat;
use Illuminate\Http\Request;

class SeatController extends Controller
{


    public function index()
    {
        $seatall = Seat::all();
        
        if ($seatall->isEmpty()) {
            return response()->json([
                'message' => 'Không có dữ liệu nào của ghế!',
            ], 404);
        }
        
        return response()->json([
            'message' => 'Lấy All dữ liệu rạp phim thành công',
            'data' => $seatall,
        ], 200);
    }

    // Đưa đến form thêm ghế và đổ all phòng ra để thêm ghế theo phòng
    public function addSeat()
    {
        // Đổ all phòng ra khi thêm
        $roomall = Room::all();
        if ($roomall->isEmpty()) {
            return response()->json([
                'message' => 'Không có phòng, hãy thêm phòng'
            ], 404);
        }

        return response()->json([
            'message' => 'Xuất all phòng ok',
            'data' => $roomall
        ], 200);
    }

    public function store(Request $request)
    {
        // Thêm mới ghế ngồi 
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'seats' => 'required|array', // ghế ngồi được thêm thành mảng, ví dụ: A1-A15
            'seats.*.range' => 'required|string', // xác định phạm vi khi thêm ghế
            'seats.*.loai_ghe_ngoi' => 'required|string|max:255',
            'seats.*.gia_ghe' => 'required|numeric',
        ]);


        $seatCreate = [];
        $totalSeatAddNew = 0;

        // Lặp qua từng ghế để thêm ghế ngồi
        foreach ($validated['seats'] as $seatConfig) {
            // Phân tích phạm vi ghế ngồi và tạo ghế
            $range = explode('-', $seatConfig['range']);
            $starSeat = $range[0];
            $endSeat = $range[1];

            // Tạo ghế dựa trên phạm vi đã phân tích
            $seats = $this->generateSeats($starSeat, $endSeat, $seatConfig['loai_ghe_ngoi'], $seatConfig['gia_ghe'], $validated['room_id']);

            // Lưu tất cả ghế ngồi vào mảng kết quả
            $seatCreate = array_merge($seatCreate, $seats);

            // Đếm tổng số ghế và thêm vào cột tong_ghe_phong của bảng rooms
            $totalSeatAddNew += count($seats);
        }

        // Cập nhật số ghế trong bảng rooms
        $room = Room::find($validated['room_id']);
        $room->tong_ghe_phong += $totalSeatAddNew;
        $room->save();

        return response()->json([
            'message' => 'Thêm mới ghế ngồi thành công',
            'data' => $seatCreate,
        ], 201);
    }


    // Hàm để tạo phạm vi ghế ngồi
    public function generateSeats($starSeat, $endSeat, $loai_ghe_ngoi, $gia_ghe, $room_id)
    {
        $seats = [];
        // Lấy phần chữ cái và phần số từ tên ghế bắt đầu và kết thúc
        preg_match('/([A-Z]+)([0-9]+)/', $starSeat, $startParts);
        preg_match('/([A-Z]+)([0-9]+)/', $endSeat, $endParts);

        $prefix = $startParts[1]; // Phần chữ A B C tùy thích
        $startNum = (int)$startParts[2]; // Phần số ghế bắt đầu
        $endNum = (int)$endParts[2]; // Phần số của ghế kết thúc (ví dụ: 15)

        // Tạo ghế từ startNum đến endNum
        for ($i = $startNum; $i <= $endNum; $i++) {
            $seatName = $prefix . $i; // Nhập số ghế A1, A2, ..., A15
            $seats[] = Seat::create([
                'so_ghe_ngoi' => $seatName,
                'loai_ghe_ngoi' => $loai_ghe_ngoi,
                'room_id' => $room_id,
                'gia_ghe' => $gia_ghe,
            ]);
        }

        return $seats;
    }

    
    public function show(string $id)
    {
    
        $seatID = Seat::find($id);
        if (!$seatID) {
            return response()->json([
                'message' => 'Không có dữ liệu Seat theo id này',
            ], 404); 
        }

        return response()->json([
            'message' => 'Lấy thông tin Seat theo ID thành công',
            'data' => $seatID,
        ], 200);
    }


    public function editSeat(string $id)
    {

        $seatID = Seat::find($id);
        if (!$seatID) {
            return response()->json([
                'message' => 'Không có dữ liệu Seat theo id này',
            ], 404); 
        }

        return response()->json([
            'message' => 'Lấy thông tin Seat theo ID thành công',
            'data' => $seatID,
        ], 200); 
    }

    public function update(Request $request, string $id)
    {
        // Cập nhật seat theo id 
        $seatID = Seat::find($id);

        if (!$seatID) {
            return response()->json([
                'message' => 'Không có dữ liệu Seat theo id này!',
            ], 404);
        }

        $validated = $request->validate([
            'so_ghe_ngoi' => 'required|string|max:250',
            'loai_ghe_ngoi' => 'required|string|max:250',
            'gia_ghe' => 'required|numeric',
        ]);

        $seatID->update($validated);

        return response()->json([
            'message' => 'Cập nhật dữ liệu Seat theo id thành công',
            'data' => $seatID
        ], 200);
    }

    public function delete(string $id)
    {

        $seatID = Seat::find($id);
        if (!$seatID) {
            return response()->json([
                'message' => 'Không có dữ liệu seat theo id này!',
            ], 404);
        }

        $seatID->delete();

        return response()->json([
            'message' => 'Xóa seat theo id thành công'
        ], 200);
    }


}
