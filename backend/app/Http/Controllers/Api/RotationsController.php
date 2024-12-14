<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Member;
use App\Models\Rotation;
use Illuminate\Http\Request;
use App\Models\HistoryRotation;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

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
        $rotations = Rotation::where('trang_thai', 0)->get();

        // Kiểm tra có vòng quay nào khả dụng không
        if ($rotations->isEmpty()) {
            return response()->json(['message' => 'Hiện không có vòng quay khả dụng'], 404);
        }

        // Chọn ngẫu nhiên vòng quay từ danh sách
        $selectedRotation = $rotations->random();

        // Kiểm tra và xử lý số lượng phần thưởng
        if ($selectedRotation->so_luong > 0) {
            // Giảm số lượng phần thưởng
            $selectedRotation->so_luong -= 1;

            // Nếu số lượng phần thưởng giảm về 0, cập nhật trạng thái vòng quay
            if ($selectedRotation->so_luong == 0) {
                $selectedRotation->trang_thai = 1;
            }

            $selectedRotation->save();
        } else {
            return response()->json(['message' => 'Phần thưởng này đã hết, vui lòng thử lại.'], 403);
        }

        // Giảm lượt quay của người dùng
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
    }


    // Lấy danh sách tất cả các rotations
    public function index()
    {
        $rotations = Rotation::where('trang_thai', 0)->get();
        return response()->json($rotations);
    }
    public function indexa()
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
        $validatedData = $request->validate([
            'ten_phan_thuong' => 'required|string|max:150',
            'muc_giam_gia' => 'nullable|numeric|max:90',
            'mo_ta' => 'required|string|max:255',
            'so_luong' => 'required|integer|min:1',
        ]);

        // Kiểm tra tên phần thưởng có bị trùng không
        $exists = Rotation::where('ten_phan_thuong', $request->ten_phan_thuong)->exists();
        if ($exists) {
            return response()->json([
                'message' => 'Tên phần thưởng đã tồn tại! Vui lòng chọn tên khác.'
            ], 409);
        }

        // Tạo vòng quay mới với trạng thái mặc định là 1
        $rotation = Rotation::create(array_merge($validatedData, ['trang_thai' => 0]));

        return response()->json([
            'message' => 'Vòng quay đã được tạo thành công.',
            'data' => $rotation
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $rotation = Rotation::find($id);

        if (!$rotation) {
            return response()->json(['message' => 'Không tìm thấy phần thưởng'], 404);
        }

        $validatedData = $request->validate([
            'ten_phan_thuong' => 'string|max:150',
            'muc_giam_gia' => 'nullable|numeric|max:90',
            'mo_ta' => 'string|max:255',
            'so_luong' => 'integer|min:1',
        ]);

        // Kiểm tra tên phần thưởng có bị trùng không, ngoại trừ phần thưởng hiện tại
        if ($request->has('ten_phan_thuong') && $request->ten_phan_thuong !== $rotation->ten_phan_thuong) {
            $exists = Rotation::where('ten_phan_thuong', $request->ten_phan_thuong)->exists();
            if ($exists) {
                return response()->json([
                    'message' => 'Tên phần thưởng đã tồn tại! Vui lòng chọn tên khác.'
                ], 409);
            }
        }

        // Cập nhật phần thưởng
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
