<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingDetail;
use Illuminate\Http\Request;

class BookingDetailController extends Controller
{
   


    // // chon ghe khi chon phim va rạp phòng xong
    // // sau do den trang thanh toan
    // public function selectSeat(Request $request, $bookingId)
    // {
    //     // check ghế
    //     // Validate thông tin ghế
    //     $request->validate([
    //         'ghengoi_id' => 'required|exists:seats,id', // Chỉ chọn một ghế
    //     ]);

    //     // lấy thông tin booking để tính toán giá booking cùng với gía ghế vip thường
    //     $booking = Booking::find($bookingId);
    //     if (!$booking) {
    //         return response()->json([
    //             'message' => 'Booking này không tồn tại'
    //         ], 404);
    //     }

    //     // check xem booking nay da chon ghe ngoi hay chua neu chua co cho chọn k co ko cho chon
    //     // dieu kien phai thanh toan thanh cong
    //     if (BookingDetail::where('booking_id', $bookingId)->exists()) {
    //         return response()->json(
    //             [
    //                 'message' => 'Booking này đã có ghế ngồi, không thể chọn lại'
    //             ],
    //             400
    //         );
    //     }

    //     // Lưu ghế ngồi vào booking_detail
    //     BookingDetail::create([
    //         'booking_id' => $booking->id,
    //         'trang_thai' => 0, // 0   là chưa thanh toán đưa đến trang thanh toán và thanh toán 0 mặc định
    //         'ghengoi_id' => $request->ghengoi_id,
    //     ]);

    //     return response()->json([
    //         'message' => 'Chọn ghế thành công, đến trang tiến hành thanh toán!',
    //         'booking_id' => $booking->id,
    //         'ghengoi_id' => $request->ghengoi_id,
    //     ], 201);
    // }

    public function index()
    {
        //
    }

    
    public function store(Request $request)
    {
        //
    }

   
    public function show(string $id)
    {
        //
    }

    
    public function update(Request $request, string $id)
    {
        //
    }

    
    public function destroy(string $id)
    {
        //
    }
}
