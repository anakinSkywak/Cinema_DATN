<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingTicketController extends Controller
{

   

    public function listMovieBookTicket(Request $request){

        $moviesBook = DB::table('showtimes')->join('movies' , 'showtimes.phim_id' , '=' , 'movies.id')->get();

        return response()->json([
            'message' => 'Kết quả tìm kiếm:',
            'data' => $moviesBook
        ], 200);
    }


    // 
    
}
