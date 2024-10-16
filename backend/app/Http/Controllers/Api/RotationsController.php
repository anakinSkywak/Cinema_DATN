<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Rotation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RotationsController extends Controller
{
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
        $validator = Validator::make($request->all(), [
            'ten_phan_thuong' => 'required|string|max:255',
            'muc_giam_gia' => 'nullable|numeric',
            'mo_ta' => 'required|string|max:255',
            'xac_xuat' => 'required|integer',
            'so_luong' => 'required|integer',
            'so_luong_con_lai' => 'nullable|integer',
            'trang_thai' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $rotation = Rotation::create($request->all());
        return response()->json(['message' => 'Tạo mới phần thưởng thành công', 'data' => $rotation], 201);
    }

    // Cập nhật rotation theo id
    public function update(Request $request, $id)
    {
        $rotation = Rotation::find($id);
        if (!$rotation) {
            return response()->json(['message' => 'Không tìm thấy phần thưởng'], 404);
        }

        $validator = Validator::make($request->all(), [
            'ten_phan_thuong' => 'nullable|string|max:255',
            'muc_giam_gia' => 'nullable|numeric',
            'mo_ta' => 'nullable|string|max:255',
            'xac_xuat' => 'nullable|integer',
            'so_luong' => 'nullable|integer',
            'so_luong_con_lai' => 'nullable|integer',
            'trang_thai' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $rotation->update($request->all());
        return response()->json(['message' => 'Cập nhật phần thưởng thành công', 'data' => $rotation]);
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
