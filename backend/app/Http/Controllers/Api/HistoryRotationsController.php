<?php

namespace App\Http\Controllers\Api;

use App\Models\HistoryRotation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class HistoryRotationsController extends Controller
{
    // Lấy lịch sử quay thưởng của người dùng hiện tại
    public function index()
    {
        // Lấy tất cả lịch sử quay thưởng của user đang đăng nhập
        $history = HistoryRotation::where('user_id', Auth::id())->get();
        return response()->json($history);
    }

    // Lấy chi tiết một lần quay thưởng theo id lịch sử
    public function show($id)
    {
        $history = HistoryRotation::find($id);
        if ($history) {
            // Kiểm tra xem user có quyền truy cập không
            if ($history->user_id == Auth::id()) {
                return response()->json($history);
            } else {
                return response()->json(['message' => 'Bạn không có quyền xem lịch sử này'], 403);
            }
        } else {
            return response()->json(['message' => 'Không tìm thấy lịch sử quay thưởng'], 404);
        }
    }

    // Xóa lịch sử quay thưởng theo id
    public function destroy($id)
    {
        $history = HistoryRotation::find($id);
        if ($history) {
            // Kiểm tra xem user có quyền xóa lịch sử này không
            if ($history->user_id == Auth::id()) {
                $history->delete();
                return response()->json(['message' => 'Xóa lịch sử quay thưởng thành công']);
            } else {
                return response()->json(['message' => 'Bạn không có quyền xóa lịch sử này'], 403);
            }
        } else {
            return response()->json(['message' => 'Không tìm thấy lịch sử quay thưởng'], 404);
        }
    }
}
