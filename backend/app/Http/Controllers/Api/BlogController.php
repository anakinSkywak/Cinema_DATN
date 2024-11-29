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
            'noi_dung' => 'required|string',
            'ngay_viet' => 'required|date',
        ]);

        // Lưu file ảnh
        if ($request->hasFile('anh_bai_viet')) {
            $file = $request->file('anh_bai_viet');
            $filename = time() . '_' . $file->getClientOriginalName(); // Thêm timestamp vào tên file
            $filePath = $file->storeAs('uploads/blogs', $filename, 'public'); // Lưu file vào thư mục public/uploads/blogs
            $validated['anh_bai_viet'] = '/storage/' . $filePath; // Tạo đường dẫn lưu vào DB
        }

        // Tạo blog mới
        $blog = Blog::create($validated);

        return response()->json([
            'status' => 'thành công',
            'message' => 'Blog được tạo thành công!',
            'data' => $blog,
            'image_url' => asset($validated['anh_bai_viet']),
        ], 201); // Trả mã HTTP 201 (Created)
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
        // Xác thực dữ liệu
        $request->validate([
            'loaibaiviet_id' => 'sometimes|exists:type_blogs,id',
            'tieu_de' => 'sometimes|string|max:255',
            'anh_bai_viet' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
            'noi_dung' => 'sometimes|string',
            'ngay_viet' => 'sometimes|date',
        ]);

        // Tìm bài viết
        $blog = Blog::find($id);

        if (!$blog) {
            return response()->json([
                'message' => 'Không tìm thấy bài viết với id ' . $id
            ], 404);
        }

        $imagePath = $blog->anh_bai_viet;

        // Xử lý ảnh bài viết
        if ($request->hasFile('anh_bai_viet')) {
            // Xóa ảnh cũ nếu tồn tại
            if ($blog->anh_bai_viet && Storage::disk('public')->exists(str_replace('/storage/', '', $blog->anh_bai_viet))) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $blog->anh_bai_viet));
            }

            $file = $request->file('anh_bai_viet');
            $filename = uniqid() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('uploads/blogs', $filename, 'public');
            $imagePath = '/storage/' . $filePath;
        }

        // Cập nhật dữ liệu bài viết
        $blog->update([
            'loaibaiviet_id' => $request->loaibaiviet_id ?? $blog->loaibaiviet_id,
            'tieu_de' => $request->tieu_de ?? $blog->tieu_de,
            'anh_bai_viet' => $imagePath,
            'noi_dung' => $request->noi_dung ?? $blog->noi_dung,
            'ngay_viet' => $request->ngay_viet ?? $blog->ngay_viet,
        ]);

        return response()->json([
            'message' => 'Cập nhật bài viết thành công!',
            'data' => $blog,
            'image_url' => $imagePath ? asset($imagePath) : null,
        ], 200);
    }

    // Xóa blog
    public function delete($id)
    {
        $blog = Blog::find($id);

        if (!$blog) {
            return response()->json(['message' => 'Không tìm thấy blog'], 404);
        }

        // Xóa ảnh liên quan nếu có
        if ($blog->anh_bai_viet && Storage::disk('public')->exists(str_replace('/storage/', '', $blog->anh_bai_viet))) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $blog->anh_bai_viet));
        }

        // Xóa blog
        $blog->delete();

        return response()->json(['message' => 'Xóa blog thành công'], 200);
    }
}
