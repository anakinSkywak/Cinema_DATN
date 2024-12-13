<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Member;
use App\Models\Rotation;
use Illuminate\Http\Request;
use App\Models\HistoryRotation;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RotationsController extends Controller
{

    public function quayThuong()
    {
        $user = auth()->user();

        // Kiểm tra xem người dùng đã đăng nhập chưa
        if (!$user) {
            return response()->json(['message' => 'Bạn cần đăng nhập để tiếp tục'], 401);
        }

        // Kiểm tra xem người dùng có còn lượt quay không
        if ($user->so_luot_quay <= 0) {
            return response()->json(['message' => 'Bạn không còn lượt quay.'], 403);
        }

        // Lấy các vòng quay có thể quay (trạng thái = 1)
        $rotations = Rotation::where('trang_thai', 1)->get();

        // Kiểm tra có vòng quay nào khả dụng không
        if ($rotations->isEmpty()) {
            return response()->json(['message' => 'Hiện không có vòng quay khả dụng'], 404);
        }

        // Tính toán xác suất dựa trên xac_xuat và chọn ngẫu nhiên
        $totalXacXuat = $rotations->sum('xac_xuat');
        $random = rand(1, $totalXacXuat);

        $currentXacXuat = 0;
        $selectedRotation = null;

        foreach ($rotations as $rotation) {
            $currentXacXuat += $rotation->xac_xuat;
            if ($random <= $currentXacXuat) {
                $selectedRotation = $rotation;
                break;
            }
        }

        // Kiểm tra vòng quay được chọn và số lượng còn lại
        if ($selectedRotation && $selectedRotation->so_luong_con_lai > 0) {
            // Cập nhật số lượng còn lại
            $selectedRotation->so_luong_con_lai -= 1;
            $selectedRotation->save();

            // Giảm lượt quay của người dùng sau khi quay
            $user->so_luot_quay -= 1;
            $user->save();

            // Lưu lịch sử quay
            HistoryRotation::create([
                'user_id' => Auth::id(),
                'vongquay_id' => $selectedRotation->id,
                'ket_qua' => $selectedRotation->ten_phan_thuong,
                'ngay_quay' => Carbon::now(),
                'ngay_het_han' => Carbon::now()->addDays(7),
                'trang_thai' => 1
            ]);

            return response()->json([
                'ket_qua' => $selectedRotation->ten_phan_thuong,
                'message' => 'Quay thành công!',
                'phan_thuong' => $selectedRotation // Trả về chi tiết phần thưởng
            ]);
        } else {
            return response()->json(['message' => 'Không có phần thưởng nào hoặc số lượng phần thưởng đã hết.'], 404);
        }
    }


    // Lấy danh sách tất cả các rotations
    public function index()
    {
        $rotations = Rotation::all();
        return response()->json($rotations);
    }

    // Lấy chi tiết một rotation theo id
    public function show($id)
    {
        $rotation = Rotation::find($id);
        if ($rotation) {
            return response()->json($rotation);
        } else {
            return response()->json(['message' => 'Không tìm thấy phần thưởng'], 404);
        }
    }

    // Tạo mới một rotation
    public function store(Request $request)
    {
        // Xác thực dữ liệu đầu vào
        $validatedData = $request->validate([
            'ten_phan_thuong' => 'required|string|max:150',
            'muc_giam_gia' => 'nullable|numeric',
            'mo_ta' => 'required|string|max:255',
            'xac_xuat' => 'required|numeric|min:0|max:100',
            'so_luong' => 'required|integer|min:1',
            'so_luong_con_lai' => 'integer|min:0|max:' . $request->so_luong,
        ], [
            // Tùy chỉnh thông báo lỗi cho các trường hợp validate không hợp lệ
            'so_luong.min' => 'Số lượng không thể nhỏ hơn 1.',
            'so_luong_con_lai.max' => 'Số lượng còn lại không thể lớn hơn số lượng tổng.',
        ]);
    
        // Kiểm tra lỗi xác thực
        if ($request->fails()) {
            return response()->json([
                'message' => 'Dữ liệu không hợp lệ.',
                'errors' => $request->errors()
            ], 200);
        }
    
        // Kiểm tra tổng xác suất của tất cả các vòng quay đã có
        $totalXacXuat = Rotation::sum('xac_xuat');
    
        // Kiểm tra tổng xác suất có vượt quá 100 không
        $newXacXuat = $request->xac_xuat;
        if (($totalXacXuat + $newXacXuat) > 100) {
            return response()->json([
                'message' => 'Tổng xác suất của tất cả các vòng quay không thể vượt quá 100%.'
            ], 200); // Trả về thông báo lỗi với mã 200 OK
        }
    
        // Tạo vòng quay mới với trạng thái mặc định là 1
        $rotation = Rotation::create(array_merge($validatedData, ['trang_thai' => 1]));
    
        // Trả về vòng quay mới nếu không có lỗi
        return response()->json([
            'message' => 'Vòng quay đã được tạo thành công.',
            'data' => $rotation
        ], 201); // Mã 201 cho việc tạo thành công
    }
    





    public function update(Request $request, $id)
    {
        $rotation = Rotation::find($id);

        if (!$rotation) {
            return response()->json(['message' => 'Không tìm thấy phần thưởng'], 404);
        }

        $validatedData = $request->validate([
            'ten_phan_thuong' => 'string|max:150',
            'muc_giam_gia' => 'nullable|numeric',
            'mo_ta' => 'string|max:255',
            'xac_xuat' => 'numeric|min:0|max:100',
            'so_luong' => 'integer|min:1',
            'so_luong_con_lai' => 'integer|min:0|max:' . ($request->so_luong ?? $rotation->so_luong),
        ]);

        // Loại bỏ `trang_thai` để tránh ghi đè
        $validatedData = array_filter($validatedData, function ($key) {
            return $key !== 'trang_thai';
        }, ARRAY_FILTER_USE_KEY);

        $rotation->update($validatedData);

        return response()->json($rotation);
    }



    // Xóa rotation theo id
    public function destroy($id)
    {
        $rotation = Rotation::find($id);
        if (!$rotation) {
            return response()->json(['message' => 'Không tìm thấy phần thưởng'], 404);
        }

        $rotation->delete();
        return response()->json(['message' => 'Xóa phần thưởng thành công']);
    }
}
