<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Seat;
use App\Models\Theater;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoomController extends Controller
{


    // Get all rooms
    public function index()
    {
        $rooms = Room::all();

        if ($rooms->isEmpty()) {
            return response()->json(['message' => 'Không có dữ liệu room '], 404);
        }

        return response()->json([
            'message' => 'Xuất dữ liệu Room thành công',
            'data' => $rooms,
        ], 200);
    }


    // Store new room
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ten_phong_chieu' => 'required|string|max:250',
        ]);

        //check phòng chiếu trùng khi thêm
        $checkNameRoom = Room::where('ten_phong_chieu', $validated['ten_phong_chieu'])->exists();
        if ($checkNameRoom) {
            return response()->json([
                'message' => 'Tên phòng này đã tồn tại !',
            ], 422); //  422 là yêu cầu không hợp lệ
        }

        $room = Room::create($validated);

        return response()->json([
            'message' => 'Thêm mới phòng chiếu phim thành công',
            'data' => $room,
        ], 201);
    }

    // Show room by id
    public function show(string $id)
    {
        $room = Room::find($id);

        if (!$room) {
            return response()->json(['message' => 'Không có dữ liệu Room theo id này'], 404);
        }

        return response()->json([
            'message' => 'Lấy thông tin Room theo ID thành công',
            'data' => $room,
        ], 200);
    }


    // đưa đến trang edit với thông tin edit đó và Theater để thay đổi rạp nếu muốn
    public function editRoom(string $id)
    {

        $roomID = Room::find($id);

        if (!$roomID) {
            return response()->json(['message' => 'Không có dữ liệu Room theo id này'], 404);
        }

        return response()->json([
            'message' => 'Lấy thông tin Room theo ID thành công',
            'data' => [
                'room' => $roomID,
            ],
        ], 200);
    }


    // cập nhật phòng với thông tin mới
    public function update(Request $request, string $id)
    {
        $room = Room::find($id);

        if (!$room) {
            return response()->json(['message' => 'Không có dữ liệu Room theo id này'], 404);
        }


        $validated = $request->validate([
            'ten_phong_chieu' => 'required|string|max:250',
        ]);

        // check nếu thay đổi tên phòng chiếu khác không được trùng với bản ghi id khác
        // nhưng được phép cùng với id bản ghi hiện tại
        $checkNameRoom = Room::where('ten_phong_chieu', $validated['ten_phong_chieu'])->where('id', '!=', $id)->exists();
        if ($checkNameRoom) {
            return response()->json([
                'message' => 'Tên phòng này đã tồn tại !',
            ], 422); //  422 là yêu cầu không hợp lệ
        }

        $room->update($validated);

        return response()->json([
            'message' => 'Cập nhật dữ liệu Room thành công',
            'data' => $room,
        ], 200);
    }


    // xóa room theo id
    public function delete(string $id)
    {
        $room = Room::find($id);

        if (!$room) {
            return response()->json(['message' => 'Không có dữ liệu Room theo id này'], 404);
        }

        // truy vấn showtime nếu có room theo id này thì phải xóa showtime trước
        $checkRoomIDShowtime = DB::table('showtimes')->where('room_id', $id)->exists();
    
        if($checkRoomIDShowtime){
            return response()->json([
                'message' => 'Có showtime đã tạo với phòng này phải xóa showtime đã tạo với room này trước mới xóa được room này !'
            ],409);
        }
    
        DB::table('seats')->where('room_id' , $id)->delete(); 

        $room->delete();

        return response()->json(['message' => 'Xóa Room theo id thành công'], 200);
    }


    // show all ghế theo phòng đó để xem all ghế và 1 số chức năng phụ
    public function allSeatRoom(string $id)
    {

        $roomID = Room::find($id);

        if (!$roomID) {
            return response()->json([
                'message' => 'Phòng không tồn tại !',
            ], 404);
        }
        // show all ghế theo phòng đó theo id
        $allSeatRoom = DB::table('seats')->where('room_id', $roomID->id)->whereNull('deleted_at')->get();

        if ($allSeatRoom->isEmpty()) {
            return response()->json([
                'message' => 'Không có ghế nào của phòng này !',
                'data' =>  $allSeatRoom
            ], 404);
        } else {
            return response()->json([
                'message' => 'Đổ toàn bộ ghế theo id room thành công !',
                'data' =>  $allSeatRoom
            ], 200);
        }
    }


    // chức năng bảo trì tắt ghế ko cho thuê nếu gặp sự cố 
    public function baoTriSeat(string $id)
    {
        // 0 la co the thue
        // 1 la da bi thue het thoi gian chieu phim set thanh 0 
        // 2 la cap nhat dang lỗi hoặc đang bảo trì ko cho thuê 

        $seatID = Seat::find($id);
        if (!$seatID) {
            return response()->json([
                'message' => 'Ghế không tồn tại',
            ], 404);
        }

        // cập nhật trạng thái là 2 bảo trị lỗi
        $seatID->update(['trang_thai' => 2]);

        return response()->json([
            'message' => 'Tắt ghế để bảo trì ghế theo id này ok',
            'data' => $seatID
        ], 200);
    }


    // tắt bảo trì ghế update lại trạng thái thành 0 có thể thuê
    // chức năng bảo trì tắt ghế ko cho thuê nếu gặp sự cố 
    public function tatbaoTriSeat(string $id)
    {
        // 0 la co the thue
        // 1 la da bi thue het thoi gian chieu phim set thanh 0 
        // 2 la cap nhat dang lỗi hoặc đang bảo trì ko cho thuê 

        $seatID = Seat::find($id);
        if (!$seatID) {
            return response()->json([
                'message' => 'Ghế không tồn tại',
            ], 404);
        }

        // cập nhật trạng thái là 2 bảo trị lỗi
        $seatID->update(['trang_thai' => 0]);

        return response()->json([
            'message' => 'Bỏ bảo trì ghế ok có thể booking',
            'data' => $seatID
        ], 200);
    }


    // xóa toàn bố ghế của phòng đó theo id phòng
    public function deleteAllSeatByRoom(string $id)
    {

        $roomID = Room::find($id);
        if (!$roomID) {
            return response()->json([
                'message' => 'Không có dữ liệu room này !',
            ], 404);
        }

        // xóa toàn bộ ghế của phòng có id
        $deleteAllSeatByRoom = Seat::where('room_id', $id)->delete();

        $resetNumbeChair = Room::where('id', $id)->update(['tong_ghe_phong' => 0]);

        return response()->json([
            'message' => 'Xóa toàn bộ ghế theo id phòng này thành công',
            'delete_count' => $deleteAllSeatByRoom . ' ghế đã xóa của phòng này',
        ], 200);
    }

    
}
