<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        // show all Voucher  
        $data = Voucher::all();

        if ($data->isEmpty()) {
            return response()->json([
                'message' => 'Không có dữ liệu Voucher !'
            ], 200);
        }

        return response()->json([
            'message' => 'Xuất dữ liệu Voucher thành công',
            'data' => $data,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        // check cac truong khi them
        $validated = $request->validate([
            'ma_giam_gia' => 'required|string|max:255',
            'muc_giam_gia' => 'required|numeric',
            'mota' => 'required|string|max:255',
            'ngay_het_han' => 'required|date',
            'so_luong' => 'required|integer',
            'so_luong_da_su_dung' => 'required|integer',
        ]);

        // them moi khi check ko co loi nao
        $data = Voucher::create($validated);

        // tra về dữ liêụ 
        return response()->json([
            'message' => 'Thêm mới voucher thành công',
            'data' => $data
        ], 201); // 201 thêm mới thành công
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // show theo id
        // show Voucher theo id
        $dataID = Voucher::find($id);


        if (!$dataID) {
            return response()->json([
                'message' => 'Không có dữ liệu Voucher theo id này',
            ], 404); // 404 ko có dữ liệu 
        }

        return response()->json([
            'message' => 'Lấy thông tin Voucher theo ID thành công',
            'data' => $dataID,
        ], 200);  // 200 có dữ liệu trả về
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // cap nhat Voucher theo id 
        $dataID = Voucher::find($id);

        //check khi sửa de cap nhat 
        if (!$dataID) {
            return response()->json([
                'message' => 'Không có dữ liệu Voucher phim theo id này',
            ], 404);
        }
        // check cac truong 
        $validated = $request->validate([
            'ma_giam_gia' => 'required|string|max:255',
            'muc_giam_gia' => 'required|numeric',
            'mota' => 'required|string|max:255',
            'ngay_het_han' => 'required|date',
            'so_luong' => 'required|integer',
            'so_luong_da_su_dung' => 'required|integer',
        ]);

        // cap nhat
        $dataID->update($validated);

        // trả về 
        return response()->json([
            'message' => 'Cập nhật dữ liệu Voucher theo id thành công',
            'data' => $dataID
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(string $id)
    {
        // xoa theo id có softdelete
        $dataID = Voucher::find($id);

        // check xem co du lieu hay ko
        if (!$dataID) {
            return response()->json([
                'message' => 'Không có dữ liệu Voucher theo id này',
            ], 404);
        }

        $dataID->delete();

        return response()->json([
            'message' => 'Xóa Voucher theo id thành công'
        ], 200);
    }
}
