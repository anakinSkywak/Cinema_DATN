<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TypeBlog;
use Illuminate\Http\Request;

class TypeBlogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Lấy tất cả loại bài viết
        $typeBlogs = TypeBlog::all();

        return response()->json($typeBlogs, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate dữ liệu
        $validatedData = $request->validate([
            'ten_loai_bai_viet' => 'required|string|max:255',
        ]);

        // Tạo mới loại bài viết
        $typeBlog = TypeBlog::create($validatedData);

        return response()->json($typeBlog, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Tìm loại bài viết theo ID
        $typeBlog = TypeBlog::findOrFail($id);

        return response()->json($typeBlog, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Validate dữ liệu
        $validatedData = $request->validate([
            'ten_loai_bai_viet' => 'required|string|max:255',
        ]);

        // Cập nhật loại bài viết
        $typeBlog = TypeBlog::findOrFail($id);
        $typeBlog->update($validatedData);

        return response()->json($typeBlog, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Tìm và xóa loại bài viết
        $typeBlog = TypeBlog::findOrFail($id);
        $typeBlog->delete();

        return response()->json(null, 204);
    }
}
