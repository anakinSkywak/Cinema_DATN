<?php


namespace App\Http\Controllers\Api;
namespace App\Http\Controllers\Api;

use App\Models\Member;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MemberController extends Controller
{
    public function index()
    {
        $data = Member::all();
        if ($data->isEmpty()) {
            return response()->json(['message' => 'Không có dữ liệu Member nào'], 200);
        }
        return response()->json([
            'message' => 'Hiển thị dữ liệu thành công',
            'data' => $data
        ]);
    }

    public function store(Request $request)
    {
        // Validate dữ liệu khi tạo Member mới
        $validated = $request->validate([
            'loai_hoi_vien' => 'required|string|max:255',
            'uu_dai' => 'required|numeric',
            'thoi_gian' => 'required|numeric',
            'ghi_chu' => 'nullable|string|max:255',
            'gia' => 'required|numeric',
            'trang_thai' => 'required|integer',
        ]);

        // Tạo mới Member
        $member = Member::create($validated);

        return response()->json(['message' => 'Thêm mới Member thành công', 'data' => $member], 200); 
    }

    public function show($id)
    {
        $dataID = Member::find($id);

        if (!$dataID) {
            return response()->json(['message' => 'Không có dữ liệu Member theo id'], 404);
        }

        return response()->json(['message' => 'Dữ liệu show theo ID thành công', 'data' => $dataID], 200);
    }

    public function update(Request $request, $id)
    {
        // Cập nhật Member theo ID
        $dataID = Member::find($id);

        if (!$dataID) {
            return response()->json(['message' => 'Không tìm thấy Member theo ID'], 404);
        }

        // Validate dữ liệu khi cập nhật Member
        $validated = $request->validate([
            'loai_hoi_vien' => 'required|string|in:thường,vip',
            'uu_dai' => 'required|numeric',
            'thoi_gian' => 'required|numeric',
            'ghi_chu' => 'nullable|string|max:255',
            'gia' => 'required|numeric',
            'trang_thai' => 'required|integer',
        ]);

        // Cập nhật Member
        $dataID->update($validated);

        return response()->json(['message' => 'Cập nhật dữ liệu thành công', 'data' => $dataID], 200);
    }

    public function destroy($id)
    {
        // Xóa Member theo ID
        $dataID = Member::find($id);

        if (!$dataID) {
            return response()->json(['message' => 'Không tìm thấy Member theo ID'], 404);
        }

        $dataID->delete();

        return response()->json(['message' => 'Xóa Member thành công'], 200);
    }

    public function getMemberTypes()
    {
        // Lấy các loại hội viên từ bảng Member
        $members = Member::select('id', 'loai_hoi_vien', 'gia')->get();

        if ($members->isEmpty()) {
            return response()->json(['message' => 'Không có loại hội viên nào'], 200);
        }

        // Trả về danh sách các loại hội viên
        return response()->json(['message' => 'Danh sách các loại hội viên', 'data' => $members], 200);
    }
}
