<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use Illuminate\Http\Request;

class VoucherController extends Controller
{

    public function index()
    {
        //
        // show all Voucher  

        $voucherall = Voucher::all();

        if ($voucherall->isEmpty()) {

            return response()->json([
                'message' => 'Không có dữ liệu Voucher !'
            ], 200);
        }

        return response()->json([
            'message' => 'Xuất dữ liệu Voucher thành công',
            'data' => $voucherall,

        ], 200);
    }


    public function store(Request $request)
    {

        // check cac truong khi them
        $validated = $request->validate([
            'ma_giam_gia' => 'required|string|max:255',
            'muc_giam_gia' => 'required|numeric',
            'mota' => 'required|string|max:255',
            'ngay_het_han' => 'required|date',
            'so_luong' => 'required|integer',
        ]);

        // them moi khi check ko co loi nao
        $vouhchers = Voucher::create($validated);

        // tra về dữ liêụ 
        return response()->json([
            'message' => 'Thêm mới voucher thành công',
            'data' => $vouhchers
        ], 201); // 201 thêm mới thành công
    }


    public function show(string $id)
    {
        // show theo id
        // show Voucher theo id
        $voucherID = Voucher::find($id);


        if (!$voucherID) {
            return response()->json([
                'message' => 'Không có dữ liệu Voucher theo id này',
            ], 404); // 404 ko có dữ liệu 
        }

        return response()->json([
            'message' => 'Lấy thông tin Voucher theo ID thành công',
            'data' => $voucherID,
        ], 200);  // 200 có dữ liệu trả về
    }

    // đưa đến trang edit đổ voucher theo id
    public function edit(string $id)
    {
        // show theo id
        // show Voucher theo id
        $voucherID = Voucher::findOrFail($id);


        if (!$voucherID) {
            return response()->json([
                'message' => 'Không có dữ liệu Voucher theo id này',
            ], 404); // 404 ko có dữ liệu 
        }

        return response()->json([
            'message' => 'Lấy thông tin Voucher theo ID để edit ok',
            'data' => $voucherID,
        ], 200);  // 200 có dữ liệu trả về
    }

    public function update(Request $request, string $id)
    {
        // cap nhat Voucher theo id 
        $dataID = Voucher::findOrFail($id);


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
        ]);

        // cap nhat
        $dataID->update($validated);

        // trả về 
        return response()->json([
            'message' => 'Cập nhật dữ liệu Voucher theo id thành công',
            'data' => $dataID
        ], 200);
    }


    public function delete(string $id)
    {
        // xoa theo id có softdelete

        $voucherID = Voucher::find($id);

        // check xem co du lieu hay ko
        if (!$voucherID) {

  
            return response()->json([
                'message' => 'Không có dữ liệu Voucher theo id này',
            ], 404);
        }

        $voucherID->delete();


        return response()->json([
            'message' => 'Xóa Voucher theo id thành công'
        ], 200);
    }
}
