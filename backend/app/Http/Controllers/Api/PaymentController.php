<?php

namespace App\Http\Controllers\Api;

use Auth;
use Carbon\Carbon;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\BookingDetail;
use App\Models\RegisterMember;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class PaymentController extends Controller
{


    // đưa đến from chọn phương thức thanh toán
    public function PaymentBooking($bookingId)
    {

        $bookingId = Booking::findOrFail($bookingId);

        // Các phương thức thanh toán có sẵn
        $paymentMethods = ['credit_card', 'paypal', 'cash', 'bank_transfer'];

        // Trả về danh sách các phương thức thanh toán và thông tin booking
        return response()->json([
            'message' => 'Thông tin booking và danh sách phương thức thanh toán',
            'booking' => $bookingId,
            'paymentMethods' => $paymentMethods
        ], 200);
    }

    public function processPaymentBooking(Request $request, $bookingId)
    {

        

        // Lấy thông tin booking theo id booking khi call go dung id cua bang booking
        $bookingId = Booking::findOrFail($bookingId);
        if (!$bookingId) {
            return response()->json([
                'message' => 'Booking id này không tồn tại !'
            ], 404);
        }

        // validate check thanh toan
        $request->validate([
            'phuong_thuc_thanh_toan' => 'required|in:credit_card,paypal,cash,bank_transfer',
        ]);


        $tong_tien = $bookingId->tong_tien_thanh_toan;

        // tao ban ghi thanh toan
        $payment =  Payment::create([
            'booking_id' => $bookingId->id,
            'tong_tien' => $tong_tien,
            'phuong_thuc_thanh_toan' => $request->phuong_thuc_thanh_toan,
            'ma_thanh_toan' => strtoupper(uniqid('PAY_')), // giả định thanh toán ok để test tích hơp sau 
            'ngay_thanh_toan' => Carbon::now(),
            'trang_thai' => 1, //  1 là đã thanh toán
        ]);

        // cập nhật trạng thái cho booking và booking_detail thanh toán thanhd công full 1 
        $bookingId->update(['trang_thai' => 1]); // thanh toán ok



        // Thêm thông tin vào booking_details
        BookingDetail::create([
            'booking_id' => $bookingId->id,
            'trang_thai' => 1, // trạng thái đã thanh toán (1)
            'thanhtoan_id' => $payment->id, // ID thanh toán vừa tạo
        ]);


        // Trả về phản hồi sau khi thanh toán thành công
        return response()->json([
            'message' => 'Thanh toán thành công',
            'payment' => $payment
        ], 201);
    }



    public function processPaymentForRegister(Request $request, RegisterMember $registerMember)
    {
        // Validate phương thức thanh toán
        $request->validate([
            'phuong_thuc_thanh_toan' => 'required|in:credit_card,paypal,cash,bank_transfer',
        ]);

        // Lấy tổng tiền từ RegisterMember
        $tong_tien = $registerMember->tong_tien;

        // Tạo bản ghi thanh toán mới
        try {
            // Tạo bản ghi thanh toán thành công
            $payment = Payment::create([
                'registermember_id' => $registerMember->id,
                'tong_tien' => $tong_tien,
                'phuong_thuc_thanh_toan' => $request->phuong_thuc_thanh_toan,
                'ma_thanh_toan' => strtoupper(uniqid('PAY_')),
                'ngay_thanh_toan' => Carbon::now(),
                'trang_thai' => 1, // Đã thanh toán
            ]);

            Log::info('Payment created successfully', ['payment' => $payment]);

            // Cập nhật trạng thái cho RegisterMember
            $registerMember->update(['trang_thai' => 1]); // Thanh toán thành công

            return response()->json([
                'message' => 'Thanh toán thành công cho RegisterMember!',
                'register_id' => $registerMember->id,
                'tong_tien_thanh_toan' => $tong_tien,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Payment creation failed', [
                'error' => $e->getMessage(),
                'registermember_id' => $registerMember->id
            ]);

            // Nếu có lỗi xảy ra, tạo bản ghi thanh toán thất bại
            Payment::create([
                'registermember_id' => $registerMember->id,
                'tong_tien' => $tong_tien,
                'phuong_thuc_thanh_toan' => $request->phuong_thuc_thanh_toan,
                'ma_thanh_toan' => strtoupper(uniqid('PAY_FAIL_')), // Đánh dấu là thất bại
                'ngay_thanh_toan' => Carbon::now(),
                'trang_thai' => 0,
            ]);

            return response()->json([
                'message' => 'Thanh toán không thành công!',
                'register_id' => $registerMember->id,
                'tong_tien_thanh_toan' => $tong_tien,
            ], 500); // Trả về mã lỗi 500 cho trường hợp lỗi hệ thống
        }
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
