<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Theater;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    // Get all rooms
    public function index()
    {
        $rooms = Room::all();

        if ($rooms->isEmpty()) {
            return response()->json(['message' => 'Không có dữ liệu rạp phim!'], 200);
        }

        return response()->json([
            'message' => 'Xuất dữ liệu Room thành công',
            'data' => $rooms,
        ], 200);
    }

    // Get all theaters for adding a new room
    public function addRoom()
    {
        $theaters = Theater::all();

        if ($theaters->isEmpty()) {
            return response()->json(['message' => 'Không có dữ liệu rạp phim!'], 200);
        }

        return response()->json($theaters);
    }

    // Store new room
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ten_phong_chieu' => 'required|string|max:250',
            'tong_ghe_phong' => 'required|integer',
            'rapphim_id' => 'required|exists:theaters,id',
        ]);

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

    // Edit room with theater list
    public function editRoom(string $id)
    {
        $room = Room::find($id);

        if (!$room) {
            return response()->json(['message' => 'Không có dữ liệu Room theo id này'], 404);
        }

        $theaters = Theater::all();

        return response()->json([
            'message' => 'Lấy thông tin Room theo ID thành công',
            'data' => [
                'room' => $room,
                'theaters' => $theaters,
            ],
        ], 200);
    }

    // Update room by id
    public function update(Request $request, string $id)
    {
        $room = Room::find($id);

        if (!$room) {
            return response()->json(['message' => 'Không có dữ liệu Room theo id này'], 404);
        }

        $validated = $request->validate([
            'ten_phong_chieu' => 'required|string|max:250',
            'tong_ghe_phong' => 'required|integer',
            'rapphim_id' => 'required|exists:theaters,id',
        ]);

        $room->update($validated);

        return response()->json([
            'message' => 'Cập nhật dữ liệu Room thành công',
            'data' => $room,
        ], 200);
    }

    // Delete room by id (Soft Delete)
    public function delete(string $id)
    {
        $room = Room::find($id);

        if (!$room) {
            return response()->json(['message' => 'Không có dữ liệu Room theo id này'], 404);
        }

        $room->delete();

        return response()->json(['message' => 'Xóa Room theo id thành công'], 200);
    }
}
