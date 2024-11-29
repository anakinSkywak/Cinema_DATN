<?php

namespace App\Http\Controllers\Api;

use App\Models\Contact;
use App\Mail\ContactsMail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
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
    public function sendResponse($contactId)
    {
        // Lấy thông tin contact
        $contact = Contact::with('user:id,ho_ten,email,so_dien_thoai')
            ->find($contactId);

        if (!$contact) {
            return response()->json(['message' => 'Không tìm thấy phản hồi'], 404);
        }

        // Phản hồi từ admin
        $admin_reply = "Cảm ơn bạn đã gửi phản hồi. Chúng tôi sẽ xem xét và xử lý vấn đề của bạn trong thời gian sớm nhất.";

        // Gửi email cho người dùng
        Mail::to($contact->user->email)->send(new ContactsMail([
            'ho_ten' => $contact->user->ho_ten,
            'noidung' => $contact->noidung,
        ], $admin_reply));

        return response()->json(['message' => 'Đã gửi phản hồi qua email'], 200);
    }
    // Hiển thị thông tin contact theo ID
    public function show($id)
    {
        // Tìm contact theo ID, đồng thời lấy thông tin người dùng
        $contact = Contact::with('user:id,ho_ten,email,so_dien_thoai')->find($id);

        // Kiểm tra nếu không tìm thấy contact
        if (!$contact) {
            return response()->json(['message' => 'Không tìm thấy phản hồi'], 404);
        }

        // Trả về thông tin contact chi tiết
        return response()->json([
            'message' => 'Thông tin contact theo ID.',
            'data' => [
                'id' => $contact->id,
                'ho_ten' => $contact->user->ho_ten ?? null,
                'email' => $contact->user->email ?? null,
                'so_dien_thoai' => $contact->user->so_dien_thoai ?? null,
                'noidung' => $contact->noidung,
            ]
        ]);
    }
}
