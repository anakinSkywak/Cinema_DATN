<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BookingDetailController extends Controller
{


    // show các đơn đã mua theo userid đó
    public function bookingDetail(Request $request)
    {

        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'message' => 'Vui lòng đăng nhập.',
            ], 401);
        }


       $bookDetails = DB::table('booking_details')
        ->join('bookings', 'booking_details.booking_id', '=', 'bookings.id')
        ->join('payments', 'booking_details.thanhtoan_id', '=', 'payments.id')
        ->where('bookings.user_id', $user->id)
        ->select('booking_details.*', 'bookings.*', 'payments.*')
        ->get();

        return response()->json([
            'message' => 'booking detail',
            'data' => $bookDetails
        ], 200);
    }



    public function show(string $id)
    {
        //
    }



    public function destroy(string $id)
    {
        //
    }
}
