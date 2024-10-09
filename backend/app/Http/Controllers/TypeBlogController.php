<?php

namespace App\Http\Controllers;

use App\Models\TypeBlog;
use Illuminate\Http\Request;

class TypeBlogController extends Controller
{
    // Hiển thị danh sách loại bài viết
    public function index()
    {
        $typeBlogs = TypeBlog::all();
        return view('typeblogs.index', compact('typeBlogs'));
    }

    // Hiển thị form tạo loại bài viết mới
    public function create()
    {
        return view('typeblogs.create');
    }

    // Lưu loại bài viết mới
    public function store(Request $request)
    {
        $request->validate(['ten_loai_bai_viet' => 'required|max:255']);
        TypeBlog::create($request->all());
        return redirect()->route('typeblogs.index')->with('success', 'Loại bài viết đã được tạo thành công!');
    }

    // Hiển thị form chỉnh sửa loại bài viết
    public function edit($id)
    {
        $typeBlog = TypeBlog::find($id);
        return view('typeblogs.edit', compact('typeBlog'));
    }

    // Cập nhật loại bài viết
    public function update(Request $request, $id)
    {
        $request->validate(['ten_loai_bai_viet' => 'required|max:255']);
        $typeBlog = TypeBlog::find($id);
        $typeBlog->update($request->all());
        return redirect()->route('typeblogs.index')->with('success', 'Loại bài viết đã được cập nhật thành công!');
    }

    // Xóa loại bài viết
    public function destroy($id)
    {
        $typeBlog = TypeBlog::find($id);
        $typeBlog->delete();
        return redirect()->route('typeblogs.index')->with('success', 'Loại bài viết đã được xóa thành công!');
    }
}
