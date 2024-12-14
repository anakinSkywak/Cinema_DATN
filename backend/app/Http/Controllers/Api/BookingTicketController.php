<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use App\Models\Seat;
use App\Models\Showtime;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingTicketController extends Controller
{



    // list phim có showtime khi thêm showtime vói phim
    public function listMovieBookTicket(Request $request)
    {

        $showtimeMoviesBook = DB::table('movies')
            ->join('showtimes', 'movies.id', '=', 'showtimes.phim_id')
            ->select('movies.*')
            ->distinct()
            ->get();

        if ($showtimeMoviesBook->isEmpty()) {
            return response()->json([
                'message' => 'Không có phim nào đang có xuất chiếu - thêm xuất chiếu.',
            ],  404);
        }

        return response()->json([
            'message' => 'Danh sách phim có xuất chiếu cho nhân viên book vé thành công .',
            'data' => $showtimeMoviesBook
        ], 200);
    }



}
