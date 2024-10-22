<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingDetailController extends Controller
{


    // show các đơn đã mua theo userid đó
    public function bookingDetail(Request $request )
    {

        $user = Auth::user();
        // if (!$user) {
        //     return response()->json([
        //         'message' => 'dn vào di ',
        //     ], 401);
        // }
        $bookDetails = BookingDetail::whereHas('booking', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->get();

        return response()->json([
            'message' => 'booking detail',
            'data' => $bookDetails
        ], 200);
    }

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
