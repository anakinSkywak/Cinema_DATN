<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            'noi_dung' => 'required',
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
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
