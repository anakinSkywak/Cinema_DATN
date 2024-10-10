<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TypeBlog;
use Illuminate\Http\Request;

class TypeBlogController extends Controller
{
    // Lấy danh sách các loại bài viết
    public function index()
    {
        $typeBlogs = TypeBlog::all();
        return response()->json($typeBlogs);
        
    }

    // Tạo loại bài viết mới
    public function store(Request $request)
    {
        $request->validate([
            'ten_loai_bai_viet' => 'required|string|max:255',
        ]);

        $typeBlog = TypeBlog::create($request->all());

        return response()->json($typeBlog, 201);
    }

    // Hiển thị chi tiết loại bài viết
    public function show($id)
    {
        $typeBlog = TypeBlog::find($id);

        if (!$typeBlog) {
            return response()->json(['message' => 'Loại bài viết không tồn tại'], 404);
        }

        return response()->json($typeBlog);
    }

    // Cập nhật loại bài viết
    public function update(Request $request, $id)
    {
        $request->validate([
            'ten_loai_bai_viet' => 'required|string|max:255',
        ]);

        $typeBlog = TypeBlog::find($id);

        if (!$typeBlog) {
            return response()->json(['message' => 'Loại bài viết không tồn tại'], 404);
        }

        $typeBlog->update($request->all());

        return response()->json($typeBlog);
    }

    // Xóa loại bài viết
    public function destroy($id)
    {
        $typeBlog = TypeBlog::find($id);

        if (!$typeBlog) {
            return response()->json(['message' => 'Loại bài viết không tồn tại'], 404);
        }

        $typeBlog->delete();

        return response()->json(['message' => 'Loại bài viết đã được xóa']);
    }
}
