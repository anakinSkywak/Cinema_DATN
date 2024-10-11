<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    /**
     * Hiển thị danh sách tất cả các contact.
     */
    public function index()
    {
        // Lấy tất cả các contact
        $contacts = Contact::all();
        return response()->json([
            'message' => 'Danh sách các contact',
            'data' => $contacts
        ], 200);
    }

    /**
     * Lưu một contact mới.
     */
    public function store(Request $request)
    {
        // Validation dữ liệu
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string',
        ]);

        // Tạo contact mới
        $contact = Contact::create($request->all());

        return response()->json([
            'message' => 'Tạo contact mới thành công',
            'data' => $contact
        ], 201);
    }

    /**
     * Hiển thị chi tiết một contact.
     */
    public function show($id)
    {
        // Tìm contact theo ID
        $contact = Contact::find($id);

        if ($contact) {
            return response()->json([
                'message' => 'Chi tiết contact',
                'data' => $contact
            ], 200);
        } else {
            return response()->json([
                'message' => 'Contact không tồn tại'
            ], 404);
        }
    }

    /**
     * Cập nhật thông tin contact.
     */
    public function update(Request $request, $id)
    {
        // Validation dữ liệu
        $request->validate([
            'name' => 'string|max:255',
            'email' => 'email|max:255',
            'message' => 'string',
        ]);

        // Tìm contact theo ID và cập nhật
        $contact = Contact::find($id);

        if ($contact) {
            $contact->update($request->all());
            return response()->json([
                'message' => 'Cập nhật contact thành công',
                'data' => $contact
            ], 200);
        } else {
            return response()->json([
                'message' => 'Contact không tồn tại để cập nhật'
            ], 404);
        }
    }

    /**
     * Xóa một contact.
     */
    public function destroy($id)
    {
        // Tìm contact theo ID và xóa
        $contact = Contact::find($id);

        if ($contact) {
            $contact->delete();
            return response()->json([
                'message' => 'Xóa contact thành công'
            ], 200);
        } else {
            return response()->json([
                'message' => 'Contact không tồn tại để xóa'
            ], 404);
        }
    }
}
