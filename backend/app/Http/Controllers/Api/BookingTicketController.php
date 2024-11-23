<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use App\Models\Showtime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingTicketController extends Controller
{

   

    public function listMovieBookTicket(Request $request){

        $moviesBook = Showtime::with('');

        

        return response()->json([
            'message' => 'Phim cho nhân viên book vé hộ ok',
            'data' => $moviesBook
        ], 200);
    }


    // 
    
}
