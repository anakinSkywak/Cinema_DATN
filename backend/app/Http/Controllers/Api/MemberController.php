<?php


namespace App\Http\Controllers\Api;

namespace App\Http\Controllers\Api;

use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        // Lấy tất cả dữ liệu từ bảng Member
        if (auth()->user()->vai_tro !== 'admin') {
            return response()->json(['message' => 'Bạn không có quyền thực hiện hành động này'], 403);
        }

        $data = Member::all();

        // Kiểm tra nếu không có dữ liệu
        if ($data->isEmpty()) {
            return response()->json(['message' => 'Không có dữ liệu Member nào'], 200);
        }

        // Trả về dữ liệu thành công
        return response()->json([
            'message' => 'Hiển thị dữ liệu thành công',
            'data' => $data
        ], 200);
    }

    public function store(Request $request)
    {
        if (auth()->user()->vai_tro !== 'admin') {
            return response()->json(['message' => 'Bạn không có quyền thực hiện hành động này'], 403);
        }
        // Validate dữ liệu khi tạo Member mới
        $validated = $request->validate([
            'loai_hoi_vien' => 'required|string|max:255',
            'uu_dai' => 'required|numeric',
            'thoi_gian' => 'required|numeric',
            'ghi_chu' => 'nullable|string|max:255',
            'gia' => 'required|numeric'
            
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
        if (auth()->user()->vai_tro !== 'admin') {
            return response()->json(['message' => 'Bạn không có quyền thực hiện hành động này'], 403);
        }

        $dataID = Member::find($id);

        if (!$dataID) {
            Log::error("Không tìm thấy Member với ID: $id");
            return response()->json(['message' => 'Không tìm thấy Member theo ID'], 404);
        }

        $validated = $request->validate([
            'loai_hoi_vien' => 'required|string|max:255',
            'uu_dai' => 'required|numeric',
            'thoi_gian' => 'required|numeric',
            'ghi_chu' => 'nullable|string|max:255',
            'gia' => 'required|numeric',
        ]);

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

    public function getMemberTypes(Request $request)
    {
        // Lấy tham số thời gian từ request (nếu có)
        $thoi_gian = $request->input('thoi_gian', null); // Nếu không có, sẽ là null

        // Lấy tất cả các loại hội viên đang hoạt động
        $members = Member::select('id', 'loai_hoi_vien', 'gia', 'thoi_gian')
            ->where('trang_thai', 1)
            ->get();

        // Kiểm tra nếu không có loại hội viên nào
        if ($members->isEmpty()) {
            return response()->json(['message' => 'Không có loại hội viên nào khả dụng'], 200);
        }

        // Nếu người dùng thay đổi thời gian, tính lại tổng giá cho từng hội viên
        if ($thoi_gian && is_numeric($thoi_gian) && $thoi_gian > 0) {
            foreach ($members as $member) {
                // Tính lại tổng tiền dựa trên thời gian người dùng chọn
                $member->tong_tien = $member->gia * $thoi_gian;
                $member->thoi_gian = $thoi_gian; // Cập nhật thời gian mới vào kết quả
            }
        }

        // Trả về danh sách các loại hội viên với thông tin đã cập nhật
        return response()->json([
            'message' => 'Danh sách các loại hội viên khả dụng',
            'data' => $members
        ], 200);
    }


    public function updateStatus(Request $request, $id)
    {
        // Kiểm tra vai trò người dùng là admin

        if (auth()->user()->vai_tro !== 'admin') {
            return response()->json(['message' => 'Bạn không có quyền thực hiện hành động này'], 403);
        }

        // Tìm Member theo ID
        $member = Member::find($id);

        if (!$member) {
            return response()->json(['message' => 'Không tìm thấy hội viên theo ID'], 404);
        }

        // Thay đổi trạng thái từ 0 -> 1 hoặc 1 -> 0
        $member->trang_thai = $member->trang_thai === 0 ? 1 : 0;
        $member->save();

        return response()->json(['message' => 'Cập nhật trạng thái hội viên thành công', 'data' => $member], 200);
    }
}
