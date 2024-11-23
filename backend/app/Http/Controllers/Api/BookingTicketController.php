<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use App\Models\Showtime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingTicketController extends Controller
{



    public function listMovieBookTicket(Request $request)
{
    $moviesBook = DB::table('movies')
        ->join('showtimes', 'movies.id', '=', 'showtimes.phim_id') // Kết nối với bảng showtimes
        ->join('movie_movie_genre', 'movies.id', '=', 'movie_movie_genre.movie_id') // Kết nối với bảng trung gian
        ->join('moviegenres', 'movie_movie_genre.movie_genre_id', '=', 'moviegenres.id') // Kết nối với bảng thể loại
        ->select(
            'movies.id as movie_id',
            'movies.ten_phim',
            DB::raw('GROUP_CONCAT(DISTINCT moviegenres.ten_the_loai) as genres') // Gộp thể loại thành chuỗi
        )
        ->groupBy('movies.id', 'movies.ten_phim') // Nhóm dữ liệu theo phim để tránh lặp
        ->distinct()
        ->get();

    return response()->json([
        'message' => 'Danh sách phim có xuất chiếu và thể loại cho nhân viên book vé thành công.',
        'data' => $moviesBook
    ], 200);
}




    // 

}
