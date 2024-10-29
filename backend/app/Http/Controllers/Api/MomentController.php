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
        // Lấy tất cả dữ liệu từ model Moment
        $data = Moment::all(); // Sử dụng all() để lấy tất cả bản ghi

        // Kiểm tra xem dữ liệu có rỗng hay không
        if ($data->isEmpty()) { // Sử dụng isEmpty() của Collection
            return response()->json([
                "message" => "Không có dữ liệu."
            ], 404);
        }

        return response()->json([
            "message" => "Lấy khoảnh khắc thành công.",
            "data" => $data
        ], 200);
    }


    public function store(Request $request)
    {
        // Lấy ID người dùng đang đăng nhập
        // $idUser = Auth::user()->id;

        // Xác thực dữ liệu từ yêu cầu
        $validated = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'phim_id' => 'required|integer|exists:movies,id',
            'anh_khoang_khac' => 'required|mimes:jpg,png,jpeg|max:2048',
            'noi_dung' => 'required|string|max:255',
            'like' => 'sometimes|integer',  // Tùy chọn
            'dislike' => 'sometimes|integer' // Tùy chọn
        ]);

        if ($request->hasFile('anh_khoang_khac')) {
            $file = $request->file('anh_khoang_khac');
            $filename = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('uploads/anh_khoang_khac', $filename, 'public');
            // Lưu ảnh vào thư mục 'images' và lấy tên file
            $validated['anh_khoang_khac'] = '/storage/' . $filePath;
        }



        // Tạo mới Moment
        $moment = Moment::create([
            'user_id' => $validated['user_id'],
            'phim_id' => $validated['phim_id'],
            'anh_khoang_khac' => $filePath,
            'noi_dung' => $validated['noi_dung'],
            'like' => $validated['like'] ?? 0,    // Mặc định là 0 nếu không có
            'dislike' => $validated['dislike'] ?? 0 // Mặc định là 0 nếu không có
        ]);

        return response()->json([
            'message' => 'Thêm mới Moment thành công',
            'data' => $moment
        ], 201);
    }



    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //

        $data = Moment::find($id);

        if(isEmpty($data)){
            return response()->json([
                'message' => 'không có moment này',
            ], 404);
        }
        return response()->json([
            'message' => 'lấy Moment thành công',
            'data' => $data
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        $data = Moment::find($id);
          // Lấy ID người dùng đang đăng nhập
        // $idUser = Auth::user()->id;

        // Xác thực dữ liệu từ yêu cầu
        $validated = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'phim_id' => 'required|integer|exists:movies,id',
            'anh_khoang_khac' => 'required|string|max:255',
            'noi_dung' => 'required|string|max:255',
            'like' => 'sometimes|integer',  // Tùy chọn
            'dislike' => 'sometimes|integer' // Tùy chọn
        ]);

        // Lưu ảnh vào thư mục 'images' và lấy tên file
        // $filePath = $request->file('anh_khoang_khac')->store('images');

        // Tạo mới Moment
        $data->update([
            'user_id' => $validated['user_id'],
            'phim_id' => $validated['phim_id'],
            'anh_khoang_khac' => $validated['anh_khoang_khac'],
            'noi_dung' => $validated['noi_dung'],
            'like' => $validated['like'] ?? 0,    // Mặc định là 0 nếu không có
            'dislike' => $validated['dislike'] ?? 0 // Mặc định là 0 nếu không có
        ]);

        return response()->json([
            'message' => 'đã update Moment thành công',
            'data' => $data
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $data = Moment::find($id);
        $data->delete();

        return response()->json([
            'message' => 'đã xóa Moment thành công',
        ], 200);
    }
}
