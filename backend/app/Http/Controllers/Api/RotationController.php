<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Rotation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RotationController extends Controller
{
    /**
     * Hiển thị danh sách tất cả các vòng quay.
     */
    public function index()
    {
        $rotations = Rotation::all();
        return response()->json([
            'status' => 'success',
            'message' => 'Lấy danh sách vòng quay thành công!',
            'data' => $rotations
        ], 200);
    }

    /**
     * Tạo mới một vòng quay.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Dữ liệu đầu vào không hợp lệ!',
                'errors' => $validator->errors()
            ], 400);
        }

        $rotation = Rotation::create([
            'name' => $request->name,
            'description' => $request->description,
            'status' => $request->status
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Tạo vòng quay mới thành công!',
            'data' => $rotation
        ], 201);
    }

    /**
     * Hiển thị chi tiết một vòng quay.
     */
    public function show($id)
    {
        $rotation = Rotation::find($id);

        if (!$rotation) {
            return response()->json([
                'status' => 'error',
                'message' => 'Không tìm thấy vòng quay với ID này!',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Lấy thông tin vòng quay thành công!',
            'data' => $rotation
        ], 200);
    }

    /**
     * Cập nhật thông tin một vòng quay.
     */
    public function update(Request $request, $id)
    {
        $rotation = Rotation::find($id);

        if (!$rotation) {
            return response()->json([
                'status' => 'error',
                'message' => 'Không tìm thấy vòng quay với ID này!',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Dữ liệu đầu vào không hợp lệ!',
                'errors' => $validator->errors()
            ], 400);
        }

        $rotation->update([
            'name' => $request->name,
            'description' => $request->description,
            'status' => $request->status
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Cập nhật thông tin vòng quay thành công!',
            'data' => $rotation
        ], 200);
    }

    /**
     * Xóa một vòng quay.
     */
    public function destroy($id)
    {
        $rotation = Rotation::find($id);

        if (!$rotation) {
            return response()->json([
                'status' => 'error',
                'message' => 'Không tìm thấy vòng quay với ID này!',
            ], 404);
        }

        $rotation->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Xóa vòng quay thành công!',
        ], 200);
    }
}
