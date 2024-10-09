<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Lấy tất cả blog với thông tin loại bài viết
        $blogs = Blog::with('typeBlog')->get();

        return response()->json($blogs, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate dữ liệu
        $validatedData = $request->validate([
            'loaibaiviet_id' => 'required|exists:type_blogs,id',
            'tieu_de' => 'required|string|max:255',
            'anh_bai_viet' => 'required|string|max:255',
            'noi_dung' => 'required|string|max:255',
            'ngay_viet' => 'required|date',
        ]);

        // Tạo mới blog
        $blog = Blog::create($validatedData);

        return response()->json($blog, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Tìm blog theo ID
        $blog = Blog::with('typeBlog')->findOrFail($id);

        return response()->json($blog, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Validate dữ liệu
        $validatedData = $request->validate([
            'loaibaiviet_id' => 'required|exists:type_blogs,id',
            'tieu_de' => 'required|string|max:255',
            'anh_bai_viet' => 'required|string|max:255',
            'noi_dung' => 'required|string|max:255',
            'ngay_viet' => 'required|date',
        ]);

        // Cập nhật blog
        $blog = Blog::findOrFail($id);
        $blog->update($validatedData);

        return response()->json($blog, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Tìm và xóa blog
        $blog = Blog::findOrFail($id);
        $blog->delete();

        return response()->json(null, 204);
    }
}
