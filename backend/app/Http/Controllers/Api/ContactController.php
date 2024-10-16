<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    // Lấy danh sách tất cả các contact
    public function index()
    {
        $contacts = Contact::all();
        return response()->json($contacts);
    }

    // Lấy thông tin một contact theo ID
    public function show($id)
    {
        $contact = Contact::find($id);
        if ($contact) {
            return response()->json($contact);
        } else {
            return response()->json(['message' => 'Không tìm thấy contact'], 404);
        }
    }

    // Tạo một contact mới
    public function store(Request $request)
    {
        $request->validate([
            'noidung' => 'required|string|max:255',
            'user_id' => 'required|exists:users,id',
        ]);

        $contact = Contact::create([
            'noidung' => $request->noidung,
            'user_id' => $request->user_id,
        ]);

        return response()->json($contact, 201);
    }

    // Cập nhật thông tin contact
    public function update(Request $request, $id)
    {
        $contact = Contact::find($id);

        if (!$contact) {
            return response()->json(['message' => 'Không tìm thấy contact'], 404);
        }

        $request->validate([
            'noidung' => 'string|max:255',
            'user_id' => 'exists:users,id',
        ]);

        $contact->update([
            'noidung' => $request->noidung ?? $contact->noidung,
            'user_id' => $request->user_id ?? $contact->user_id,
        ]);

        return response()->json($contact);
    }

    // Xóa contact
    public function destroy($id)
    {
        $contact = Contact::find($id);

        if (!$contact) {
            return response()->json(['message' => 'Không tìm thấy contact'], 404);
        }

        $contact->delete();
        return response()->json(['message' => 'Xóa contact thành công']);
    }
}
