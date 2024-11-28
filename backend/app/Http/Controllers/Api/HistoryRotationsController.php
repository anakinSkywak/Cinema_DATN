<?php

namespace App\Http\Controllers\Api;

use App\Models\HistoryRotation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class HistoryRotationsController extends Controller
{
    /**
     * Lấy lịch sử quay thưởng của người dùng hiện tại
     */
    public function index()
    {
        // Lấy tất cả lịch sử quay thưởng của user đang đăng nhập
        $history = HistoryRotation::where('user_id', Auth::id())->get();
        return response()->json($history);
    }

    /**
     * Lấy chi tiết một lần quay thưởng theo ID lịch sử
     */
    public function show($id)
    {
        $history = HistoryRotation::find($id);

        if (!$history) {
            return response()->json(['message' => 'Không tìm thấy lịch sử quay thưởng'], 404);
        }

        // Kiểm tra quyền truy cập
        if ($history->user_id !== Auth::id()) {
            return response()->json(['message' => 'Bạn không có quyền xem lịch sử này'], 403);
        }

        return response()->json($history);
    }

    /**
     * Xóa lịch sử quay thưởng theo ID
     */
    public function destroy($id)
    {
        $history = HistoryRotation::find($id);

        if (!$history) {
            return response()->json(['message' => 'Không tìm thấy lịch sử quay thưởng'], 404);
        }

        // Kiểm tra quyền xóa
        if ($history->user_id !== Auth::id()) {
            return response()->json(['message' => 'Bạn không có quyền xóa lịch sử này'], 403);
        }

        $history->delete();
        return response()->json(['message' => 'Xóa lịch sử quay thưởng thành công']);
    }

    /**
     * Lấy phần thưởng cho người dùng chọn 
     */
    public function getAvailableRotations()
    {
        $rotations = HistoryRotation::where('user_id', Auth::id())
            ->get();
        if ($rotations->isEmpty()) {
            return response()->json(['message' => 'Không phần thưởng quay nào khả dụng'], 404);
        }
        return response()->json($rotations);
    }
    
}
