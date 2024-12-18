<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Member;
use App\Models\Rotation;
use Illuminate\Http\Request;
use App\Models\HistoryRotation;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Milon\Barcode\BarcodeGenerator;
use Milon\Barcode\BarcodeGeneratorPNG;
use Illuminate\Support\Facades\Storage;

class RotationsController extends Controller
{

    public function quayThuong()
    {
        $user = auth()->user();

        // Kiểm tra xác thực người dùng
        if (!$user) {
            return response()->json(['message' => 'Người dùng chưa được xác thực'], 401);
        }

        if (!$user instanceof \App\Models\User) {
            return response()->json(['message' => 'Dữ liệu người dùng không hợp lệ'], 500);
        }

        // Kiểm tra lượt quay của người dùng
        if ($user->so_luot_quay <= 0) {
            return response()->json(['message' => 'Bạn không còn lượt quay.'], 403);
        }

        // Lấy các vòng quay khả dụng (trạng thái = 0)
        $rotations = Rotation::where('trang_thai', 0)->get();

        // Kiểm tra có vòng quay nào khả dụng không
        if ($rotations->isEmpty()) {
            return response()->json(['message' => 'Hiện không có vòng quay khả dụng'], 404);
        }

        // Chọn ngẫu nhiên vòng quay từ danh sách
        $selectedRotation = $rotations->random();

        // Kiểm tra số lượng phần thưởng
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
        if (!isset($user->so_luot_quay) || $user->so_luot_quay === null) {
            return response()->json(['message' => 'Dữ liệu lượt quay không hợp lệ'], 422);
        }

        $user->so_luot_quay -= 1; // Giảm lượt quay
        $user->save();

       
        $cc='Bạn chỉ có thể sử dụng phần thưởng khi có vé xem phim và phiếu này còn thời gian sử dụng';

        // Lưu thông tin vào bảng history_rotations
        HistoryRotation::create([
            'user_id' => Auth::id(),
            'vongquay_id' => $selectedRotation->id,
            'ket_qua' => $selectedRotation->ten_phan_thuong,
            'ngay_quay' => Carbon::now(),
            'ngay_het_han' => Carbon::now()->addDays(3),
            'dieu_kien'=>$cc,
            'trang_thai' => 0
        ]);

        // Trả về kết quả quay thưởng và đường dẫn mã vạch
        return response()->json([
            'ket_qua' => $selectedRotation->ten_phan_thuong,
            'message' => 'Quay thành công!',
            'phan_thuong' => $selectedRotation,  // Trả về chi tiết phần thưởng
            // 'barcode_url' => asset('storage/' . $barcodeFilePath) // URL mã vạch
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
            'mo_ta' => 'string|max:255',
            'so_luong' => 'integer|min:1',
            'hinh_anh' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Kiểm tra file ảnh
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
    
        // Xử lý file ảnh nếu có
        if ($request->hasFile('hinh_anh')) {
            // Xóa ảnh cũ nếu có
            if ($rotation->hinh_anh && Storage::exists($rotation->hinh_anh)) {
                Storage::delete($rotation->hinh_anh);
            }
    
            // Lưu ảnh mới
            $imagePath = $request->file('hinh_anh')->store('phan_thuong', 'public');
            $validatedData['hinh_anh'] = $imagePath;
        }
    
        // Cập nhật phần thưởng
        $rotation->update($validatedData);
    
        return response()->json([
            'message' => 'Cập nhật phần thưởng thành công.',
            'data' => $rotation,
        ]);
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

    public function updateStatusrotaion(Request $request, $id)
    {
        if (auth()->user()->vai_tro !== 'admin') {
            return response()->json(['message' => 'Bạn không có quyền thực hiện hành động này'], 403);
        }

        // Tìm Rotation theo ID
        $rotation = Rotation::find($id);

        if (!$rotation) {
            return response()->json(['message' => 'Không tìm thấy phần thưởng ID'], 404);
        }

        // Thay đổi trạng thái từ 0 -> 1 hoặc 1 -> 0
        $rotation->trang_thai = $rotation->trang_thai === 0 ? 1 : 0;
        $rotation->save();

        return response()->json(['message' => 'Cập nhật trạng thái vòng quay thành công', 'data' => $rotation], 200);
    }
}
