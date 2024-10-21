<?php

namespace App\Http\Controllers;

use App\Models\Moment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MomentController extends Controller
{
    // Lấy danh sách tất cả moments
    public function index()
    {
        return response()->json(Moment::with(['user', 'movie'])->get());
    }

    // Tạo moment mới
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'phim_id' => 'required|exists:movies,id',
            'anh_khoang_khac' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'noi_dung' => 'required|string|max:255',
            'like' => 'required|numeric',
            'dislike' => 'required|numeric',
        ]);

        // Lưu ảnh
        $validatedData = $request->only(['user_id', 'phim_id', 'noi_dung', 'like', 'dislike']); // Lấy dữ liệu đã xác thực

        if ($request->hasFile('anh_khoang_khac')) {
            $file = $request->file('anh_khoang_khac');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('uploads/moments', $fileName, 'public'); 
            $validatedData['anh_khoang_khac'] = '/storage/' . $filePath; // Lưu đường dẫn ảnh vào cơ sở dữ liệu
        }

        $moment = Moment::create($validatedData);

        return response()->json([
            'message' => 'Thêm khoảnh khắc thành công',
            'data' => $moment
        ], 201);
    }

    // Lấy thông tin 1 moment
    public function show($id)
    {
        $moment = Moment::with(['user', 'movie'])->findOrFail($id);
        return response()->json($moment);
    }

    // Cập nhật moment
    public function update(Request $request, $id)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'phim_id' => 'required|exists:movies,id',
            'anh_khoang_khac' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'noi_dung' => 'required|string|max:255',
            'like' => 'required|numeric',
            'dislike' => 'required|numeric',
        ]);

        $moment = Moment::findOrFail($id); // Tìm khoảnh khắc hoặc trả về lỗi 404

        // Nếu có tệp hình ảnh, xử lý upload hình
        if ($request->hasFile('anh_khoang_khac')) {
            $imagePath = $request->file('anh_khoang_khac')->store('uploads/moments', 'public');
            $moment->anh_khoang_khac = '/storage/' . $imagePath; // Lưu đường dẫn ảnh vào cơ sở dữ liệu
        }

        // Cập nhật các trường khác
        $moment->user_id = $request->user_id;
        $moment->phim_id = $request->phim_id;
        $moment->noi_dung = $request->noi_dung;
        $moment->like = $request->like;
        $moment->dislike = $request->dislike;

        $moment->save();

        return response()->json(['message' => 'Cập nhật khoảnh khắc thành công!', 'data' => $moment], 200);
    }

    // Xóa moment
    public function destroy($id)
    {
        $moment = Moment::findOrFail($id);

        // Xóa ảnh khi xóa moment
        if ($moment->anh_khoang_khac) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $moment->anh_khoang_khac));
        }

        $moment->delete();

        return response()->json(['message' => 'Xóa khoảnh khắc thành công']);
    }
}
