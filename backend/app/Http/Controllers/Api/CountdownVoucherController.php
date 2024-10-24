<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CountdownVoucher;
use Illuminate\Http\Request;

class CountdownVoucherController extends Controller
{
    public function index()
    {
        $countdownVouchers = CountdownVoucher::with('voucher')->get();
        return response()->json($countdownVouchers);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
    'magiamgia_id' => 'required|exists:vouchers,id',
    'ngay' => 'required|date',
    'thoi_gian_bat_dau' => 'required|date_format:H:i:s',
    'thoi_gian_ket_thuc' => 'required|date_format:H:i:s|after:thoi_gian_bat_dau',
    'so_luong' => 'required|integer|min:1',
    'trang_thai' => 'nullable|integer|in:0,1',
]);


        $countdownVoucher = CountdownVoucher::create($validated);

        return response()->json([
            'message' => 'Tạo mã giảm giá thành công.',
            'data' => $countdownVoucher
        ], 201);
    }

    public function show($id)
    {
        $countdownVoucher = CountdownVoucher::with('voucher')->findOrFail($id);
        return response()->json($countdownVoucher);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'magiamgia_id' => 'required|exists:vouchers,id',
            'ngay' => 'required|date',
            'thoi_gian_bat_dau' => 'required|date_format:H:i:s',
            'thoi_gian_ket_thuc' => 'required|date_format:H:i:s|after:thoi_gian_bat_dau',
            'so_luong' => 'required|integer|min:1',
            'trang_thai' => 'nullable|integer|in:0,1',
        ]);
        

        $countdownVoucher = CountdownVoucher::findOrFail($id);
        $countdownVoucher->update($validated);

        return response()->json([
            'message' => 'Cập nhật mã giảm giá thành công.',
            'data' => $countdownVoucher
        ]);
    }

    public function destroy($id)
    {
        $countdownVoucher = CountdownVoucher::findOrFail($id);
        $countdownVoucher->delete();

        return response()->json(['message' => 'Xóa mã giảm giá thành công.']);
    }
}
