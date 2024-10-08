<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RegisterMember;
use Illuminate\Http\Request;

class RegisterMemberController extends Controller
{
    public function index()
    {
        $data = RegisterMember::with('member', 'user')->orderBy('id', 'DESC')->paginate(10);

        if ($data->isEmpty()) {
            return response()->json([
                'message' => 'Không có đăng ký hội viên',
            ], 204);
        }

        return response()->json([
            'data' => $data->items(),
            'pagination' => [
                'current_page' => $data->currentPage(),
                'total_pages' => $data->lastPage(),
                'total_items' => $data->total(),
                'per_page' => $data->perPage(),
                'next_page_url' => $data->nextPageUrl(),
                'prev_page_url' => $data->previousPageUrl(),
            ]
        ], 200);
    }

    public function store(Request $request)
    {
        $data = RegisterMember::create($request->all());

        return response()->json([
            'data' => $data,
            'message' => 'Đăng ký hội viên thành công!'
        ], 201);
    }

    public function show($id)
    {
        $data = RegisterMember::with('member', 'user')->findOrFail($id);

        return response()->json([
            'data' => $data
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $data = RegisterMember::findOrFail($id);
        $data->update($request->all());

        return response()->json([
            'data' => $data,
            'message' => 'Cập nhật đăng ký hội viên thành công!'
        ], 200);
    }

    public function destroy($id)
    {
        $data = RegisterMember::findOrFail($id);
        $data->delete();

        return response()->json([
            'message' => 'Xóa đăng ký hội viên thành công!'
        ], 200);
    }
}
