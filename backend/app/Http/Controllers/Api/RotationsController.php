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
    $request->validate([
        'ten_phan_thuong' => 'required|string|max:150',
        'muc_giam_gia' => 'nullable|numeric',
        'mo_ta' => 'required|string|max:255',
        'xac_xuat' => 'required|numeric|min:0|max:100',
        'so_luong' => 'required|integer|min:1',
        'so_luong_con_lai' => 'integer|min:0|max:' . $request->so_luong,
        'trang_thai' => 'nullable|integer',
    ]);

    $rotation = Rotation::create($request->all());

    return response()->json($rotation, 201);
}

public function update(Request $request, $id)
{
    $rotation = Rotation::find($id);

    if (!$rotation) {
        return response()->json(['message' => 'Không tìm thấy phần thưởng'], 404);
    }

    $request->validate([
        'ten_phan_thuong' => 'string|max:150',
        'muc_giam_gia' => 'nullable|numeric',
        'mo_ta' => 'string|max:255',
        'xac_xuat' => 'numeric|min:0|max:100',
        'so_luong' => 'integer|min:1',
        'so_luong_con_lai' => 'integer|min:0|max:' . ($request->so_luong ?? $rotation->so_luong),
        'trang_thai' => 'nullable|integer',
    ]);

    $rotation->update($request->all());

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
