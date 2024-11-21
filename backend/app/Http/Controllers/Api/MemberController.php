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
        // Kiểm tra vai trò
        if (auth()->user()->vai_tro === 'admin') {
            // Admin nhìn thấy tất cả thẻ hội viên, bao gồm cả những thẻ có trạng thái 0
            $data = Member::all();
        } else {
            // Người dùng chỉ thấy các thẻ có trạng thái 1
            $data = Member::where('trang_thai', 1)->get();
        }

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

    public function getMemberTypes()
    {
        $members = Member::select('id', 'loai_hoi_vien', 'gia')
            ->where('trang_thai', 1)
            ->get();

        if ($members->isEmpty()) {
            return response()->json(['message' => 'Không có loại hội viên nào khả dụng'], 200);
        }

        return response()->json([
            'message' => 'Danh sách các loại hội viên khả dụng',
            'data' => $members
        ], 200);
    }
    public function updateStatus(Request $request, $id)
    {
        // Kiểm tra vai trò người dùng là admin
        // if (auth()->user()->vai_tro !== 'admin') {
        //     return response()->json(['message' => 'Bạn không có quyền thực hiện hành động này'], 403);
        // }
        if (auth()->check() && auth()->user()->id !== 14) {
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
