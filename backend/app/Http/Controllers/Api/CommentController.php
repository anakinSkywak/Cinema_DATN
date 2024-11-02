<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;

use function PHPUnit\Framework\isEmpty;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        // Lấy tất cả dữ liệu từ model comment
        $data = Comment::all(); // Sử dụng all() để lấy tất cả bản ghi

        // Kiểm tra xem dữ liệu có rỗng hay không
        if ($data->isEmpty()) { // Sử dụng isEmpty() của Collection
            return response()->json([
                "message" => "Không có dữ liệu."
            ], 404);
        }

        return response()->json([
            "message" => "Lấy comment thành công.",
            "data" => $data
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'noi_dung' => 'required|string|max:255',
            'phim_id' => 'required|integer|exists:movies,id',
            'khoangkhac_id' => 'required|integer|exists:moments,id',
        ]);

        $user_id = auth()->id();

        $data = Comment::create([
            "user_id" => $user_id,
            "noi_dung" => $validated['noi_dung'],
            "phim_id" => $validated['phim_id'],
            "khoangkhac_id" => $validated['khoangkhac_id'],
        ]);

        return response()->json([
            "message" => "đã tạo bình luận thành công",
            "data" => $data,
        ], 201);
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $data = Comment::where('id', $id)->first();

        // Kiểm tra xem dữ liệu có tồn tại hay không
        if (!$data) {
            return response()->json([
                "message" => "Không có dữ liệu theo id này."
            ], 404);
        }

        return response()->json([
            "message" => "Lấy comment thành công.",
            "data" => $data
        ], 200);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Tìm comment theo ID
        $comment = Comment::find($id);

        // Kiểm tra comment có tồn tại không
        if (!$comment) {
            return response()->json([
                "message" => "Không tìm thấy comment này!"
            ], 404);
        }

        // Kiểm tra quyền sở hữu comment
        if ($comment->user_id !== auth()->id()) {
            return response()->json([
                "message" => "Bạn không có quyền thay đổi comment này."
            ], 403);
        }

        // Xác thực nội dung bình luận
        $validated = $request->validate([
            'noi_dung' => 'required|string|max:255',
        ]);

        // Cập nhật comment
        $comment->update($validated);

        // Trả về dữ liệu comment đã cập nhật
        return response()->json([
            "message" => "Đã update bình luận thành công.",
            "data" => $comment->refresh(),
        ], 200);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //

        
    }
}
