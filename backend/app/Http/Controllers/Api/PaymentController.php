<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // thanh toan khi da len đơn store booking
        $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'tong_tien' => 'required|numeric',
            'phuong_thuc_thanh_toan' => 'required|in:credit_card,paypal,cash,bank_transfer', // các loaị thanh toán
            // 'ma_thanh_toan' => 'required|string|max:255', // ko can
            //'ngay_thanh_toan' => 'required|date', // ko can de tu dong theo ngay hien tai
            'trang_thai' => 'required|in:0,1' // 0: Chưa thanh toán, 1: Đã thanh toán
        ]);

        // toa ma thanh toan ngau nhien de admin tra cuu 
        $ma_thanh_toan = Str::upper(Str::random(8)) . mt_rand(1000, 9999);

        // lay ngay thanh toan hien tai cho ngay hien tai
        $ngay_thanh_toan = Carbon::now(); 
        
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
