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
    
        if (!$user) {
            return response()->json(['message' => 'Bạn cần đăng nhập để tiếp tục'], 401);
        }
    
        if ($user->so_luot_quay <= 0) {
            return response()->json(['message' => 'Bạn không còn lượt quay.'], 403);
        }
    
        $rotations = Rotation::where('trang_thai', 1)->get();
    
        if ($rotations->isEmpty()) {
            return response()->json(['message' => 'Hiện không có vòng quay khả dụng'], 404);
        }
    
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
    
        if ($selectedRotation) {
            $user->so_luot_quay -= 1;
            $user->save();
    
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
                'phan_thuong' => $selectedRotation
            ]);
        } else {
            return response()->json(['message' => 'Không có phần thưởng nào khả dụng.'], 404);
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
        $validatedData = $request->validate([
            'ten_phan_thuong' => 'required|string|max:150',
            'muc_giam_gia' => 'nullable|numeric|max:90',
            'mo_ta' => 'required|string|max:255',
            'xac_xuat' => 'required|numeric|min:0|max:100',
            'so_luong' => 'required|integer|min:1',
        ]);
    
        $exists = Rotation::where('ten_phan_thuong', $request->ten_phan_thuong)->exists();
        if ($exists) {
            return response()->json([
                'message' => 'Tên phần thưởng đã tồn tại! Vui lòng chọn tên khác.'
            ], 409);
        }
    
        $totalXacXuat = Rotation::sum('xac_xuat');
        if (($totalXacXuat + $request->xac_xuat) > 100) {
            return response()->json([
                'message' => 'Tổng xác suất của tất cả các vòng quay không thể vượt quá 100%.'
            ], 200);
        }
    
        $rotation = Rotation::create(array_merge($validatedData, ['trang_thai' => 1]));
    
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
            'xac_xuat' => 'numeric|min:0|max:100',
            'so_luong' => 'integer|min:1',
        ]);
    
        if ($request->has('ten_phan_thuong') && $request->ten_phan_thuong !== $rotation->ten_phan_thuong) {
            $exists = Rotation::where('ten_phan_thuong', $request->ten_phan_thuong)->exists();
            if ($exists) {
                return response()->json([
                    'message' => 'Tên phần thưởng đã tồn tại! Vui lòng chọn tên khác.'
                ], 409);
            }
        }
    
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
