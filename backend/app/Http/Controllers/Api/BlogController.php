<?php

namespace App\Http\Controllers\Api;

use App\Models\Blog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class BlogController extends Controller
{
    // Lấy danh sách blogs
    public function index()
    {
        $blogs = Blog::with('typeBlog')->get();
        return response()->json(['status' => 'thành công', 'data' => $blogs]);
    }

    // Tạo blog mới
    public function store(Request $request)
    {
        // Xác thực dữ liệu
        $validated = $request->validate([
            'loaibaiviet_id' => 'required|exists:type_blogs,id',
            'tieu_de' => 'required|string|max:255',
            'anh_bai_viet' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'noi_dung' => 'required|string|max:255',
            'ngay_viet' => 'required|date',
        ]);

        // Lưu file ảnh
        if ($request->hasFile('anh_bai_viet')) {
            $path = $request->file('anh_bai_viet')->store('blogs', 'public');
            $validated['anh_bai_viet'] = 'storage/' . $path; // Lưu đường dẫn ảnh
        }

        // Tạo blog mới
        $blog = Blog::create($validated);

        return response()->json(['status' => 'thành công', 'data' => $blog]);
    }

    // Lấy chi tiết blog
    public function show($id)
    {
        $blog = Blog::with('typeBlog')->find($id);

        if (!$blog) {
            return response()->json(['status' => 'lỗi', 'message' => 'Không tìm thấy bài viết'], 404);
        }

        return response()->json(['status' => 'thành công', 'data' => $blog]);
    }

    // Cập nhật blog
    public function update(Request $request, $id)
    {
        $blog = Blog::find($id);

        if (!$blog) {
            return response()->json([
                'status' => 'lỗi',
                'message' => 'Không tìm thấy bài viết'
            ], 404);
        }

        // Xác thực dữ liệu
        $validated = $request->validate([
            'loaibaiviet_id' => 'sometimes|exists:type_blogs,id',
            'tieu_de' => 'sometimes|string|max:255',
            'anh_bai_viet' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
            'noi_dung' => 'sometimes|string|max:255',
            'ngay_viet' => 'sometimes|date',
        ]);

        // Kiểm tra nếu có file ảnh mới
        if ($request->hasFile('anh_bai_viet')) {
            // Xóa ảnh cũ nếu có
            if ($blog->anh_bai_viet) {
                Storage::disk('public')->delete(str_replace('storage/', '', $blog->anh_bai_viet));
            }

            // Lưu ảnh mới
            $path = $request->file('anh_bai_viet')->store('blogs', 'public');
            $validated['anh_bai_viet'] = 'storage/' . $path;
        } else {
            // Giữ lại ảnh cũ nếu không có ảnh mới
            $validated['anh_bai_viet'] = $blog->anh_bai_viet;
        }

        // Cập nhật blog
        $blog->update($validated);

        return response()->json(['status' => 'thành công', 'data' => $blog]);
    }

    // Xóa blog
    public function destroy($id)
    {
        $blog = Blog::find($id);

        if (!$blog) {
            return response()->json(['status' => 'lỗi', 'message' => 'Không tìm thấy bài viết'], 404);
        }

        // Xóa ảnh liên quan
        if ($blog->anh_bai_viet && Storage::disk('public')->exists($blog->anh_bai_viet)) {
            Storage::disk('public')->delete(str_replace('storage/', '', $blog->anh_bai_viet));
        }

        $blog->delete();

        return response()->json(['status' => 'thành công', 'message' => 'Bài viết đã được xóa thành công']);
    }
}
