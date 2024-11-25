<?php

namespace App\Http\Controllers\Api;

use App\Models\Contact;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ContactController extends Controller
{
    // Lấy danh sách tất cả các contact
    public function index()
    {
        $contacts = Contact::all();
        return response()->json($contacts);
    }

    // Tạo một contact mới
    public function store(Request $request)
    {
        // Kiểm tra xem người dùng đã đăng nhập chưa
        if (!Auth::check()) {
            return response()->json(['message' => 'Bạn cần đăng nhập để liên hệ'], 401);
        }
    
        $user = Auth::user(); // Lấy thông tin người dùng đang đăng nhập
    
        // Xác thực dữ liệu đầu vào
        $validated = $request->validate([
            'noidung' => 'required|string',
        ]);
    
        // Tạo contact mới
        $contact = Contact::create([
            'noidung' => $validated['noidung'],
            'user_id' => $user->id, // Dùng id của người dùng đã đăng nhập
        ]);
    
        // Trả về phản hồi JSON
        return response()->json([
            'message' => 'Thông tin đã được gửi thành công.',
            'data' => $contact
        ], 201); // 201: Created
    }

    // Xóa contact
    public function destroy($id)
    {
        $contact = Contact::find($id);

        if (!$contact) {
            return response()->json(['message' => 'Không tìm thấy phàn hồi '], 404);
        }

        $contact->delete();
        return response()->json(['message' => 'Xóa contact thành công']);
    }
}
