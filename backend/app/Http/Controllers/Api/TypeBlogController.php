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
        try {
            $typeBlogs = TypeBlog::all();

            return response()->json([
                'success' => true,
                'message' => 'Lấy danh sách các loại bài viết thành công!',
                'data' => $typeBlogs,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi lấy danh sách các loại bài viết.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Tạo loại bài viết mới
    public function store(Request $request)
    {
        try {
            // Xác thực dữ liệu đầu vào
            $validated = $request->validate([
                'ten_loai_bai_viet' => 'required|string|max:255', // Trường 'ten_loai_bai_viet' là bắt buộc, phải là chuỗi, tối đa 255 ký tự
                'anh' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Trường 'anh' là bắt buộc, phải là ảnh (jpeg, png, jpg, gif), dung lượng tối đa 2MB
            ]);

            // Kiểm tra xem có file ảnh nào được tải lên hay không
            if ($request->hasFile('anh')) {
                $file = $request->file('anh'); // Lấy file từ request
                $fileName = time() . '_' . $file->getClientOriginalName(); // Đặt tên file (kết hợp thời gian hiện tại với tên file gốc)
                $filePath = $file->storeAs('uploads/type_blogs', $fileName, 'public'); // Lưu file vào thư mục 'uploads/type_blogs' trong 'storage/app/public'
                $validated['anh'] = '/storage/' . $filePath; // Tạo đường dẫn URL công khai để truy cập file
            }

            // Thêm ngày hiện tại vào dữ liệu
            $validated['ngay'] = now()->toDateString(); // Lấy ngày hiện tại theo định dạng YYYY-MM-DD

            // Tạo một bản ghi mới trong bảng 'type_blogs' với dữ liệu đã xác thực
            $typeBlog = TypeBlog::create($validated);

            // Trả về phản hồi thành công (HTTP 201) kèm theo dữ liệu của loại bài viết mới
            return response()->json([
                'success' => true, // Biến thể hiện trạng thái thành công
                'message' => 'Loại bài viết mới đã được tạo thành công!', // Thông báo
                'data' => $typeBlog, // Dữ liệu loại bài viết vừa tạo
                'image_url' => asset($validated['anh']),
            ], 201);
        } catch (\Exception $e) {
            // Bắt các lỗi khác không phải lỗi xác thực và trả về phản hồi lỗi (HTTP 500)
            return response()->json([
                'success' => false, // Biến thể hiện trạng thái thất bại
                'message' => 'Đã xảy ra lỗi khi tạo loại bài viết.', // Thông báo lỗi
                'error' => $e->getMessage(), // Chi tiết lỗi (dành cho debug)
            ], 500);
        }
    }

    // Hiển thị chi tiết loại bài viết
    public function show($id)
    {
        try {
            $typeBlog = TypeBlog::findOrFail($id);

            return response()->json([
                'status' => true,
                'data' => $typeBlog,
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Loại bài viết không tồn tại',
            ], 404);
        }
    }
// Cập nhật loại bài viết
public function update(Request $request, $id)
{
    try {
        // Xác thực dữ liệu đầu vào
        $validated = $request->validate([
            'ten_loai_bai_viet' => 'required|string|max:255', // Trường 'ten_loai_bai_viet' là bắt buộc, phải là chuỗi, tối đa 255 ký tự
            'anh' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Trường 'anh' là tùy chọn, nếu có phải là ảnh (jpeg, png, jpg, gif), dung lượng tối đa 2MB
        ]);

        // Tìm loại bài viết theo id
        $typeBlog = TypeBlog::find($id);

        if (!$typeBlog) {
            return response()->json([
                'success' => false,
                'message' => 'Loại bài viết không tồn tại',
            ], 404);
        }

        // Kiểm tra nếu có ảnh mới
        if ($request->hasFile('anh')) {
            // Xóa ảnh cũ nếu có
            if ($typeBlog->anh) {
                $oldImagePath = public_path($typeBlog->anh);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath); // Xóa ảnh cũ
                }
            }

            // Lưu ảnh mới
            $file = $request->file('anh');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('uploads/type_blogs', $fileName, 'public');
            $validated['anh'] = '/storage/' . $filePath;
        }

        // Cập nhật dữ liệu
        $typeBlog->update($validated);

        // Trả về phản hồi thành công
        return response()->json([
            'success' => true,
            'message' => 'Loại bài viết đã được cập nhật thành công!',
            'data' => $typeBlog,
            'image_url' => asset($validated['anh'] ?? $typeBlog->anh),
        ], 200);

    } catch (\Exception $e) {
        // Xử lý lỗi
        return response()->json([
            'success' => false,
            'message' => 'Đã xảy ra lỗi khi cập nhật loại bài viết.',
            'error' => $e->getMessage(),
        ], 500);
    }
}

// Xóa loại bài viết
public function destroy($id)
{
    try {
        // Tìm loại bài viết theo id
        $typeBlog = TypeBlog::find($id);

        if (!$typeBlog) {
            return response()->json([
                'success' => false,
                'message' => 'Loại bài viết không tồn tại',
            ], 404);
        }

        // Kiểm tra và xóa ảnh nếu có
        if ($typeBlog->anh) {
            $oldImagePath = public_path($typeBlog->anh);
            if (file_exists($oldImagePath)) {
                unlink($oldImagePath); // Xóa ảnh
            }
        }

        // Xóa loại bài viết
        $typeBlog->delete();

        // Trả về phản hồi thành công
        return response()->json([
            'success' => true,
            'message' => 'Loại bài viết đã được xóa thành công!',
        ], 200);
    } catch (\Exception $e) {
        // Xử lý lỗi
        return response()->json([
            'success' => false,
            'message' => 'Đã xảy ra lỗi khi xóa loại bài viết.',
            'error' => $e->getMessage(),
        ], 500);
    }
}
}
