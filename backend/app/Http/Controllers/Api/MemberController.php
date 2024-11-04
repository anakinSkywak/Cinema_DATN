<?php

namespace App\Http\Controllers\Api;

use App\Models\Member;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Lấy tất cả dữ liệu từ bảng Member
        $data = Member::query()->orderBy('id', 'DESC')->paginate(10);

        if ($data->isEmpty()) {
            return response()->json([
                'message' => 'Không có hội viên',
            ], 404);
        }

        return response()->json([
            'data' => $data->items(),
            'pagination' => [
                'current_page' => $data->currentPage(),
                'total_pages' => $data->lastPage(),
                'total_items' => $data->total(),
                'per_page' => $data->perPage(),
                'next_page_url' => $data->nextPageUrl(),
                'prev_page_url' => $data->previousPageUrl(),
            ]
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate dữ liệu khi tạo Member mới
        $validated = $request->validate([
            'loai_hoi_vien' => 'required|string|max:255',
            'uu_dai' => 'required|numeric', // uu dai % bao nhieu phan tram
            'thoi_gian' => 'required|numeric',  // defaut 1 tháng user có the thay doi thang khi dk : chỉnh cột default sau 
            'ghi_chu' => 'nullable|string|max:255', 
            'gia' => 'required|numeric', // gia moi lan khi them hoi vien
            //'trang_thai' => 'required|integer', // de default k can them
        ]);

        // Tạo mới Member
        $member = Member::create($validated);

        return response()->json([
            'message' => 'Thêm hội viên thành công!',
            'data' => $member
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Hiển thị Member theo ID
        $dataID = Member::find($id);

        if (!$dataID) {
            return response()->json([
                'message' => 'Không có dữ liệu Member theo id'
            ], 404);
        }

        return response()->json([
            'message' => 'Hiển thị dữ liệu thành công',
            'data' => $dataID,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Cập nhật Member theo ID
        $dataID = Member::find($id);

        if (!$dataID) {
            return response()->json([
                'message' => 'Không tìm thấy Member theo ID'
            ], 404);
        }

        // Validate dữ liệu khi cập nhật Member
        $validated = $request->validate([
            'loai_hoi_vien' => 'required|string|max:255',
            'uu_dai' => 'required|numeric',
            'thoi_gian' => 'required|numeric',
            'ghi_chu' => 'nullable|string|max:255',
            'gia' => 'required|numeric',
            'trang_thai' => 'required|integer',

        ]);

        // Cập nhật Member
        $dataID->update($validated);

        return response()->json([
            'message' => 'Cập nhật thành công!',
            'data' => $dataID,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Xóa Member theo ID
        $dataID = Member::find($id);

        if (!$dataID) {
            return response()->json([
                'message' => 'Không tìm thấy Member theo ID'
            ], 404);
        }

        $dataID->delete();

        return response()->json([
            'message' => 'Xóa hội viên thành công!',
        ], 200);
    }
}
