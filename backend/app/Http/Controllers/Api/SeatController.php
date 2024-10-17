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


    // đưa đến from thêm ghế và đổ all phòng ra thêm ghế theo phòng
    public function addSeat()
    {

        // đổ all phòng ra khi thêm
        $roomall = Room::all();

        if ($roomall->isEmpty()) {
            return response()->json([
                'message' => 'Không có phòng hãy thêm phòng'
            ], 200);
        }

        return response()->json([
            'message' => 'Xuất all phòng ok',
            'data' => $roomall
        ], 200);
    }


    public function store(Request $request)
    {
        // them moi ghe ngoi 
        // xac thuc du lieu dau vao cua ghe
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id', // khi them 
            'seats' => 'required|array', // ghế ngồi thêm thành mảng khi thêm ví dụ A1->A15
            'seats.*.range' => 'required|string', // xác định phạm vi khi thêm ghế  A1-A15, VIP1-VIP15
            'seats.*.loai_ghe_ngoi' => 'required|string|max:255',
            'seats.*.gia_ghe' => 'required|numeric',
        ]);

        // mang ghe ngoi rong
        $seatCreate = [];

        // lap de them ghe ngoi voi mang 
        foreach ($validated['seats'] as $seatConfig) {
            // phân tích phạm vi ghế ngồi và range tạo ngẫu nhiên - 
            // lap va tach mang seat ra voi explode
            $range = explode('-', $seatConfig['range']);
            // ghe bat dau voi ghe ket thuc
            $starSeat = $range[0];
            $endSeat = $range[1];

            // tạo ghế dựa trên phạm vi đã phân tich
            $seats = $this->generateSeats($starSeat, $endSeat, $seatConfig['loai_ghe_ngoi'], $seatConfig['gia_ghe'], $validated['room_id']);

            // lưu tất cả ghe ngoi vao bang ket qua
            $seatCreate = array_merge($seatCreate, $seats);
        }

        return response()->json([
            'message' => 'Thêm mới ghế ngồi thành công',
            'data' => $seatCreate,
        ], 201);
    }



    // ham de tao pham vi ghe ngoi
    public function generateSeats($starSeat, $endSeat, $loai_ghe_ngoi, $gia_ghe, $room_id)
    {
        $seats = [];
        // lấy phần chữ cái và phần số từ tên ghế bắt đầu và kết thúc
        preg_match('/([A-Z]+)([0-9]+)/', $starSeat, $startParts);
        preg_match('/([A-Z]+)([0-9]+)/', $endSeat, $endParts);

        $prefix = $startParts[1]; // phần chữ A B C tùy thích
        $startNum = (int)$startParts[2]; // phần số ghế bắt đầu
        $endNum = (int)$endParts[2]; // Phần số của ghế kết thúc (ví dụ: 15)

        // tạo ghế từ startNum đến endNum
        for ($i = $startNum; $i <= $endNum; $i++) {
            $seatName = $prefix . $i; // nhập số ghế A1, A2, ..., A15 
            $seats[] = Seat::create([
                'so_ghe_ngoi' => $seatName,
                'loai_ghe_ngoi' => $loai_ghe_ngoi, // loại ghế cho all mảng đó
                'room_id' => $room_id,
                'gia_ghe' => $gia_ghe, // gia ghe theo mang đó ví dụ thường 10k
            ]);
        }

        return $seats;
    }



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
