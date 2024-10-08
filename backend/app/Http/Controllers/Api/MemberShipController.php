<?php

namespace App\Http\Controllers\Api;

use App\Models\MemberShips;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MemberShipsController extends Controller
{
    public function index()
    {
        $data = MemberShips::with('registerMember')->orderBy('id', 'DESC')->paginate(10);

        if ($data->isEmpty()) {
            return response()->json([
                'message' => 'Không có thẻ hội viên',
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
        $data = MemberShips::create($request->all());

        return response()->json([
            'data' => $data,
            'message' => 'Thêm thẻ hội viên thành công!'
        ], 201);
    }

    public function show($id)
    {
        $data = MemberShips::with('registerMember')->findOrFail($id);

        return response()->json([
            'data' => $data
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $data = MemberShips::findOrFail($id);
        $data->update($request->all());

        return response()->json([
            'data' => $data,
            'message' => 'Cập nhật thẻ hội viên thành công!'
        ], 200);
    }

    public function destroy($id)
    {
        $data = MemberShips::findOrFail($id);
        $data->delete();

        return response()->json([
            'message' => 'Xóa thẻ hội viên thành công!'
        ], 200);
    }
}
