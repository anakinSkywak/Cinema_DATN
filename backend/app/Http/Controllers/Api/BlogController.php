<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    // Lấy danh sách tất cả các blogs
    public function index()
    {
        $blogs = Blog::with('typeBlog')->get();
        return response()->json($blogs);
    }

    // Tạo blog mới
    public function store(Request $request)
    {
        $request->validate([
            'loaibaiviet_id' => 'required|exists:type_blogs,id',
            'tieu_de' => 'required|string|max:255',
            'anh_bai_viet' => 'required|string|max:255',
            'noi_dung' => 'required|string',
            'ngay_viet' => 'required|date',
        ]);

        $blog = Blog::create($request->all());

        return response()->json($blog, 201);
    }

    // Hiển thị chi tiết một blog
    public function show($id)
    {
        $blog = Blog::with('typeBlog')->find($id);

        if (!$blog) {
            return response()->json(['message' => 'Blog không tồn tại'], 404);
        }

        return response()->json($blog);
    }

    // Cập nhật blog
    public function update(Request $request, $id)
    {
        $request->validate([
            'loaibaiviet_id' => 'required|exists:type_blogs,id',
            'tieu_de' => 'required|string|max:255',
            'anh_bai_viet' => 'required|string|max:255',
            'noi_dung' => 'required|string',
            'ngay_viet' => 'required|date',
        ]);

        $blog = Blog::find($id);

        if (!$blog) {
            return response()->json(['message' => 'Blog không tồn tại'], 404);
        }

        $blog->update($request->all());

        return response()->json($blog);
    }

    // Xóa blog
    public function destroy($id)
    {
        $blog = Blog::find($id);

        if (!$blog) {
            return response()->json(['message' => 'Blog không tồn tại'], 404);
        }

        $blog->delete();

        return response()->json(['message' => 'Blog đã được xóa']);
    }
}
