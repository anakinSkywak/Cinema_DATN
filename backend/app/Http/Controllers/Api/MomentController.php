<?php

namespace App\Http\Controllers\Api;

use App\Models\Moment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use Illuminate\Auth\Events\Validated;
use function PHPUnit\Framework\isEmpty;

class MomentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $data = Moment::all();

        if(isEmpty($data)){
            return response()->json([
                "message" => "không có dữ liệu"
            ], 404);
        }

        return response()->json([
            "message" => "lấy khoảnh khác thành công",
            "data" => $data
        ], 200);
    }

    public function store(Request $request)
    {
        // Lấy ID người dùng đã đăng nhập
        $idUser = Auth::user()->id;
    
        // Lấy ID phim từ request
        $idMovie = $request->input('phim_id'); // Giả sử bạn truyền 'phim_id' trong request
    
        // Xác thực dữ liệu đầu vào
        $validated = $request->validate([
            'user_id' => $idUser,
            'phim_id' => 'required|integer|exists:movies,id', // Đảm bảo phim tồn tại trong bảng movies
            'anh_khoang_khac' => 'required|file|mimes:jpeg,png,jpg,gif|max:2048',
            'noi_dung' => 'required|string|max:255'
        ]);
    
        // Tạo mới bản ghi Moment
        Moment::create([
            'user_id' => $validated['user_id'],
            'phim_id' => $validated['phim_id'],
            'anh_khoang_khac' => $request->file('anh_khoang_khac')->store('images'), // Lưu hình ảnh vào thư mục images
            'noi_dung' => $validated['noi_dung']
        ]);
    
        return response()->json([
            'message' => 'Tạo mới Moment thành công!'
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
