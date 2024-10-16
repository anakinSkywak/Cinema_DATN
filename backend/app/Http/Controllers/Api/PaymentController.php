<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingDetail;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
   

    public function processPayment(Request $request, $bookingId)
    {
        // validate check thanh toan
        $request->validate([
            'phuong_thuc_thanh_toan' => 'required|in:credit_card,paypal,cash,bank_transfer',
        ]);

        // Lấy thông tin booking theo id booking khi call go dung id cua bang booking
        $booking = Booking::find($bookingId);
        if (!$booking) {
            return response()->json([
                'message' => 'Booking id này không tồn tại !'
            ], 404);
        }

        // Lấy thông tin ghế ngồi đã chọn từ booking_detail
        $bookingDetail = BookingDetail::where('booking_id', $bookingId)->first();

        if (!$bookingDetail) {
            return response()->json([
                'message' => 'Chưa chọn ghế ngồi !'
            ], 400);
        }

        // lấy giá ghế từ thông tin ghế ngồi
        $seat = $bookingDetail->seat;
        //lay gia ghe ngoi de tinh tien ghe voi tien booking de thanh toan
        $tong_tien_ghe = $seat->gia_ghe;

        // cập nhật tổng tiền của booking bao gồm giá ghế ngồi de thanh toan
        $tong_tien_thanh_toan = $booking->tong_tien + $tong_tien_ghe;

        // tao ban ghi thanh toan
        Payment::create([
            'booking_id' => $booking->id,
            'tong_tien' => $tong_tien_thanh_toan,
            'phuong_thuc_thanh_toan' => $request->phuong_thuc_thanh_toan,
            'ma_thanh_toan' => strtoupper(uniqid('PAY_')), // giả định thanh toán ok để test tích hơp sau 
            'ngay_thanh_toan' => Carbon::now(),
            'trang_thai' => 1, //  1 là đã thanh toán
        ]);

        // cập nhật trạng thái cho booking và booking_detail thanh toán thanhd công full 1 
        $booking->update(['trang_thai' => 1]); // thanh toán ok
        $bookingDetail->update(['trang_thai' => 1]); // ghế ngồi bị chặn k thể đặt

        

        return response()->json([
            'message' => 'Thanh toán thành công !',
            'booking_id' => $booking->id,
            'tong_tien_thanh_toan' => $tong_tien_thanh_toan, // so tien thanh toan ca booking vs ghe ngoi
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {}

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
