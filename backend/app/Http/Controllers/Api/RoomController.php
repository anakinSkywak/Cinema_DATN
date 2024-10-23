<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Theater;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoomController extends Controller
{


    public function index()
    {
        // show all room 
        $roomall = Room::all();

        if ($roomall->isEmpty()) {
            return response()->json([
                'message' => 'Không có dữ liệu rạp phim !'
            ], 200);
        }

        return response()->json([
            'message' => 'Xuất dữ liệu Room thành công',
            'data' => $roomall,
        ], 200);
    }

    // hàm đến from add thêm mới đổ rạp phim để thêm khi thêm mới
    public function addRoom()
    {
        // đổ all rạp phim để chọn khi thêm
        $thearteall = Theater::all();

        if ($thearteall->isEmpty()) {
            return response()->json([
                'message' => 'Không có dữ liệu nào của rạp phim ! .',
            ], 200);
        }

        return response()->json($thearteall);
    }


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

        // tra ve khi them moi ok
        return response()->json([
            'message' => 'Thêm mới phòng chiếu phim  thành công',
            'data' => $room
        ], 201);    // tra về 201 them moi thanh cong
    }


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


    // đưa đến trang edit với thông tin edit đó và Theater để thay đổi rạp nếu muốn
    public function editRoom(string $id)
    {
        // show room theo id
        $roomID = Room::findOrFail($id);

        if (!$roomID) {
            return response()->json([
                'message' => 'Không có dữ liệu Room theo id này',
            ], 404); // 404 ko có dữ liệu 
        }

        // đổ all theaters ra để chọn nếu thay đổi rạp phim của phòng đó
        $theaters = Theater::all();

        return response()->json([
            'message' => 'Lấy thông tin Room theo ID thành công',
            'data' => [
                'room' => $roomID, // phong theo id
                'theaters' => $theaters,  // all rap phim
            ],
        ], 200);  // 200 có dữ liệu trả về
    }

    public function update(Request $request, string $id)
    {
        // cap nhat room theo id 
        $roomID = Room::findOrFail($id);

        //check khi sửa de cap nhat 
        if (!$roomID) {
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
        $roomID->update($validated);

        // trả về 
        return response()->json([
            'message' => 'Cập nhật dữ liệu Room theo id thành công',
            'data' => $roomID
        ], 200);
    }

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

    // show all ghế theo phòng đó để xem all ghế và 1 số chức năng phụ
    public function allSeatRoom(string $id)
    {

        
        $roomID = Room::find($id);
       
        if (!$roomID) {
            return response()->json([
                'message' => 'Phòng không tồn tại',
            ], 404);
        }
        // show all ghế theo phòng đó theo id
        $allSeatRoom = DB::table('seats')->where('room_id', $roomID->id)->get();


        return response()->json([
            'message' => 'đổ toàn bộ ghế theo id room ok',
            'data' =>  $allSeatRoom
        ], 200);
    }
}
