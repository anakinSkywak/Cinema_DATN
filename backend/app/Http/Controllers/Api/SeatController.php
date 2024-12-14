<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Seat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SeatController extends Controller
{


    // ghế all ( có thể dùng hoặc không )
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
                'message' => 'Không có phòng, hãy thêm phòng !'
            ], 404);
        }

        return response()->json([
            'message' => 'Xuất tất cả phòng có thành công',
            'data' => $roomall
        ], 200);
    }


    // thêm 1 ghế đơn lẻ 
    public function storeOneSeat(Request $request)
    {
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'so_ghe_ngoi' => 'required|string|max:255',
            'loai_ghe_ngoi' => 'required|string|max:255',
            'gia_ghe' => 'required|numeric|min:1',
        ]);

        // tổng số ghế sẽ cộng thêm 1
        $totalSeatAddNew = 1;

        //  truy vấn xem số ghế ngồi thêm đã có trong phòng chọn chưa
        $checkNameSeat = Seat::where('room_id', $validated['room_id'])->where('so_ghe_ngoi', $validated['so_ghe_ngoi'])->exists();
        if ($checkNameSeat) {
            return response()->json([
                'message' => 'Số ghế này đã có trong phòng chọn rồi !',
                'invali_nameSeat' => $validated['so_ghe_ngoi'],
            ], 422);
        }

        // Cập nhật số ghế trong bảng rooms
        $room = Room::find($validated['room_id']);
        $room->tong_ghe_phong += $totalSeatAddNew;
        $room->save();

        $oneSeat = Seat::create([
            'so_ghe_ngoi' => $validated['so_ghe_ngoi'],
            'loai_ghe_ngoi' => $validated['loai_ghe_ngoi'],
            'room_id' => $validated['room_id'],
            'gia_ghe' => $validated['gia_ghe'],
        ]);

        return response()->json([
            'message' => 'Thêm 1 ghế theo room đã chọn thành công',
            'seat' => $oneSeat,
        ], 200);
    }


    // thêm ghế mới với 1 mảng A1-A5
    public function store(Request $request)
    {

        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'seats' => 'required|array', // ghế ngồi được thêm thành mảng, ví dụ: A1-A15
            'seats.*.range' => 'required|string', // xác định phạm vi khi thêm ghế
            'seats.*.loai_ghe_ngoi' => 'required|string|max:255', // loại ghế 
            'seats.*.gia_ghe' => 'nullable|numeric|min:1', // giá ghế 
        ]);


        // tạo mảng ghế rỗng
        $seatCreate = [];

        $existingSeatsList = [];

        // tổng số ghế = 0 
        $totalSeatAddNew = 0;

        // đặt cờ kiểm tra ghế trùng hay không
        $isAnySeatExist = false;

        // lặp qua từng ghế để thêm ghế ngồi
        foreach ($validated['seats'] as $seatConfig) {
            // phân tích phạm vi ghế ngồi và tạo ghế
            $range = explode('-', $seatConfig['range']);
            $starSeat = $range[0];
            $endSeat = $range[1];

            // tạo ghế dựa trên phạm vi đã phân tích
            $seats = $this->generateSeats(
                $starSeat,
                $endSeat,
                $seatConfig['loai_ghe_ngoi'],
                $seatConfig['gia_ghe'],
                $validated['room_id'],
                $existingSeatsList,
            );


            // nếu phát hiện ghế trùng dừng lại và không thêm bất kỳ ghế nào
            if (!empty($existingSeatsList)) {
                $isAnySeatExist = true;

                // dùng việc thêm ghế
                break;
            }

            // lưu tất cả ghế ngồi vào mảng kết quả
            $seatCreate = array_merge($seatCreate, $seats);

            // đếm tổng số ghế và thêm vào cột tong_ghe_phong của bảng rooms
            $totalSeatAddNew += count($seats);
        }

        // nếu ghế trùng trả về số ghế trùng của phòng đó
        if ($isAnySeatExist) {
            return response()->json([
                'message' => 'Có ghế trùng trong phòng này hoặc có lỗi phạm vi ghế , không thể thêm ghế mới',
                'existing_seats' => $existingSeatsList,
            ], 400);
        }

        // Cập nhật số ghế trong bảng rooms
        $room = Room::find($validated['room_id']);
        $room->tong_ghe_phong += $totalSeatAddNew;
        $room->save();

        return response()->json([
            'message' => 'Thêm mới ghế ngồi thành công',
            'data' => $seatCreate,
            'existing_seats' => $existingSeatsList,
        ], 201);
    }


    // Hàm để tạo phạm vi ghế ngồi
    public function generateSeats($starSeat, $endSeat, $loai_ghe_ngoi, $gia_ghe, $room_id, &$existingSeatsList)
    {
        $seats = [];

        // truy vấn số ghế kiểm tra ghế khi thêm ghế trùng của phòng khi thêm
        $existingSeats = Seat::where('room_id', $room_id)->pluck('so_ghe_ngoi')->toArray();

        // lấy phần chữ cái và phần số từ tên ghế bắt đầu và kết thúc
        preg_match('/([A-Z]+)([0-9]+)/', $starSeat, $startParts);
        preg_match('/([A-Z]+)([0-9]+)/', $endSeat, $endParts);

        $startPrefix = $startParts[1]; // Tiền tố bắt đầu (A)
        $endPrefix = $endParts[1];     // Tiền tố kết thúc (B)
        $startNum = (int)$startParts[2]; // Số bắt đầu
        $endNum = (int)$endParts[2];     // Số kết thúc

        if ($startPrefix !== $endPrefix) {
            $existingSeatsList[] = "Không hỗ trợ phạm vi khác ký tự prefix: $starSeat - $endSeat";
            return [];
        }

        // Tạo ghế từ startNum đến endNum
        for ($i = $startNum; $i <= $endNum; $i++) {
            $seatName = $startPrefix . $i; // Ghép lại tên ghế, ví dụ: A1, A2...
    
            // Kiểm tra nếu ghế đã tồn tại
            if (in_array($seatName, $existingSeats)) {
                $existingSeatsList[] = $seatName;
                continue; // Bỏ qua ghế đã tồn tại
            }

            $seats[] = Seat::create([
                'so_ghe_ngoi' => $seatName,
                'loai_ghe_ngoi' => $loai_ghe_ngoi,
                'room_id' => $room_id,
                'gia_ghe' => $gia_ghe,
            ]);
        }

        return $seats;
    }


    // có thể dùng hoặc không : shơw ghế theo id
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


    // đưa đến trang edit ghế đổ thông tin ghế theo id
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


    // cập nhật ghế với thông tin mới
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
            'gia_ghe' => 'required|numeric|min:1',
        ]);

        // check khi thay đổi tên ghế khi update có bị trùng với tên ghế đã có ở trong bảng theo room id ko
        $checkNameSeatUpdateByRoom = Seat::where('room_id', $seatID->room_id)
            ->where('so_ghe_ngoi', $validated['so_ghe_ngoi'])->where('id', '!=', $seatID->id)->exists();

        if ($checkNameSeatUpdateByRoom) {
            return response()->json([
                'message' => 'Tên ghế đã tồn tại trong phòng với id phòng là : ' . $seatID->room_id,
                'invalid_seat' => $validated['so_ghe_ngoi'],
            ], 422);
        }

        $seatID->update($validated);

        return response()->json([
            'message' => 'Cập nhật dữ liệu Seat theo id thành công',
            'data' => $seatID
        ], 200);
    }


    // xóa ghế theo id
    public function delete(string $id)
    {

        $seatID = Seat::find($id);
        if (!$seatID) {
            return response()->json([
                'message' => 'Không có dữ liệu seat theo id này!',
            ], 404);
        }

        // lấy thông tin phòng liên kết với ghế
        $room = Room::find($seatID->room_id);

        $seatID->delete();

        // khi xóa 1 ghế của phòng nào giảm đi 1 tong_ghe_phong của phòng đó
        if ($room) {
            $room->decrement('tong_ghe_phong');
        }


        return response()->json([
            'message' => 'Xóa seat theo id thành công trừ đi 1 ghế trong Tổng Ghế Phòng của phòng theo ghế này',
        ], 200);
    }
}
