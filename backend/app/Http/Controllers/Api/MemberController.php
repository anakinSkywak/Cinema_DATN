<?php


namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\Request;


namespace App\Http\Controllers\Api;

use App\Models\Member;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $data = Member::query()->orderBy('id', 'DESC')->paginate(10);

        if ($data->isEmpty()) {
            return response()->json([
                'message' => 'Không có hội viên',
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

        // Lấy tất cả dữ liệu từ bảng Member
        $data = Member::all();
        
        if ($data->isEmpty()) {
            return response()->json([
                'message' => 'Không có dữ liệu Member nào'
            ], 200);
        }

        return response()->json([
            'message' => 'Hiển thị dữ liệu thành công',
            'data' => $data
        ]);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $data = Member::create($request->all());

        return response()->json([
            'data' => $data,
            'message' => 'Thêm hội viên thành công!'
        ], 201);

        // Validate dữ liệu khi tạo Member mới
        $validated = $request->validate([
            'loai_hoi_vien' => 'required|string|max:255',
            'uu_dai' => 'required|numeric',
            'thoi_gian' => 'required|numeric',
            'ghi_chu' => 'nullable|string|max:255',
            'gia' => 'required|numeric',
            'trang_thai' => 'required|integer',
        ]);
    
        // Tạo mới Member
        $member = Member::create($validated);
    
        return response()->json([
            'message' => 'Thêm mới Member thành công',
            'data' => $member
        ], 200); // Chỉnh sửa từ 201 thành 200

    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $data = Member::findOrFail($id);

        return response()->json([
            'data' => $data
        ], 200);

        // Hiển thị Member theo ID
        $dataID = Member::find($id);

        if (!$dataID) {
            return response()->json([
                'message' => 'Không có dữ liệu Member theo id'
            ], 404);
        }

        return response()->json([
            'message' => 'Dữ liệu show theo ID thành công',
            'data' => $dataID,
        ]);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {

        $data = Member::findOrFail($id);
        $data->update($request->all());

        return response()->json([
            'data' => $data,
            'message' => 'Cập nhật hội viên thành công!'

        // Cập nhật Member theo ID
        $dataID = Member::find($id);

        if (!$dataID) {
            return response()->json([
                'message' => 'Không tìm thấy Member theo ID'
            ], 404);
        }

        // Validate dữ liệu khi cập nhật Member
        $validated = $request->validate([
            'loai_hoi_vien' => 'required|string|in:thường,vip',
            'uu_dai' => 'required|numeric',
            'thoi_gian' => 'required|numeric',
            'ghi_chu' => 'nullable|string|max:255',
            'trang_thai' => 'required|integer',
        ]);

        // Xác định giá cho từng loại hội viên
        $gia = $this->getPriceByType($request->loai_hoi_vien);
        $validated['gia'] = $gia;

        // Cập nhật Member
        $dataID->update($validated);

        return response()->json([
            'message' => 'Cập nhật dữ liệu thành công',
            'data' => $dataID,

        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {

        $data = Member::findOrFail($id);
        $data->delete();

        return response()->json([
            'message' => 'Xóa hội viên thành công!'
        ], 200);
    }

        // Xóa Member theo ID
        $dataID = Member::find($id);

        if (!$dataID) {
            return response()->json([
                'message' => 'Không tìm thấy Member theo ID'
            ], 404);
        }

        $dataID->delete();

        return response()->json([
            'message' => 'Xóa Member thành công'
        ], 200);
    }

    /**
     * Lấy giá theo loại hội viên
     */
    private function getPriceByType($type)
    {
        switch ($type) {
            case 'thường':
                return 100; // Giá cho hội viên thường
            case 'vip':
                return 200; // Giá cho hội viên VIP
            default:
                return 0; // Giá mặc định nếu không khớp loại
        }
    }

}
