<?php

namespace App\Http\Controllers\Api;

use App\Models\Contact;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ContactController extends Controller
{
    // Lấy danh sách tất cả các contact
    // public function index()
    // {
    //     $contacts = Contact::with('user:id,name,email') // Nếu muốn hiển thị thêm thông tin người gửi
    //         ->get();
    
    //     return response()->json([
    //         'message' => 'Danh sách contact.',
    //         'data' => $contacts
    //     ]);
    // }
    public function getContactDetails()
    {
        $contacts = Contact::with('user:id,ho_ten,email,so_dien_thoai')
            ->get()
            ->map(function ($contact) {
                return [
                    'id' => $contact->id,
                    'ho_ten' => $contact->user->ho_ten ?? null,
                    'email' => $contact->user->email ?? null,
                    'so_dien_thoai' => $contact->user->so_dien_thoai ?? null,
                    'noidung' => $contact->noidung,
                ];
            });
    
        return response()->json([
            'message' => 'Danh sách contact kèm thông tin người dùng.',
            'data' => $contacts
        ]);
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
    
        // Trả về phản hồi JSON với thông tin người dùng
        return response()->json([
            'message' => 'Thông tin đã được gửi thành công.',
            'data' => [
                'contact' => $contact,
                'user' => [
                    'ho_ten' => $user->ho_ten,
                    'email' => $user->email,
                    'so_dien_thoai' => $user->so_dien_thoai,
                ]
            ]
        ], 201); // 201: Created
    }

    // Xóa contact
    public function destroy($id)
    {
        // Tìm contact theo ID
        $contact = Contact::find($id);
    
        // Nếu không tìm thấy contact, trả về lỗi 404
        if (!$contact) {
            return response()->json(['message' => 'Không tìm thấy phản hồi'], 404);
        }
    
        // Xóa contact (hoặc soft delete nếu cần)
        $contact->delete();
    
        // Phản hồi thành công
        return response()->json(['message' => 'Xóa phản hồi thành công'], 200);
    }
}
