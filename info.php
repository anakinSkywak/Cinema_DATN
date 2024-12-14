note code anh
Route::get('showtime-by-movie/{movieID}' , [ShowtimeController::class , 'showtimeByDateMovie']); // 2 
Route::post('showtime-by-movie/{movieID}/showtimes-by-date' , [ShowtimeController::class , 'getShowtimesTimeByDate']); // 3
//2
    // đổ all showtime ngày theo phim id đó
    public function showtimeByDateMovie(Request $request, $movieID)
    {

        // truy vấn lấy showtime theo khác nhau
        $showtimeByMovieByDate = Showtime::where('phim_id', $movieID)
            ->selectRaw('DATE(ngay_chieu) as ngay_chieu')
            ->distinct()
            ->orderBy('ngay_chieu', 'asc')
            ->get();

        if (!$showtimeByMovieByDate) {
            return response()->json([
                'message' => 'Không ngày chiếu theo id phim này !',
                'data' => $showtimeByMovieByDate,
            ], 400);
        }


        if ($showtimeByMovieByDate->isEmpty()) {
            return response()->json([
                'message' => 'Không ngày chiếu theo id phim này , thêm xuất chiếu với phim đó !',
                'data' => $showtimeByMovieByDate,
            ], 404);
        }

        return response()->json([
            'message' => 'Tất cả ngày chiếu của xuất chiếu theo phim id',
            'data' => $showtimeByMovieByDate,
        ], 200);
    }


    //3
    // đổ all giờ khi ấn vào ngày 
    public function getShowtimesTimeByDate(Request $request, $movieID)
    {
        $validated = $request->validate([
            'ngay_chieu' => 'required|date',
        ]);

        $date = $validated['ngay_chieu'];

      
        $allTimeByDate = Showtime::with('movie:id,ten_phim', 'room:id,ten_phong_chieu')
            ->where('phim_id', $movieID)
            ->whereDate('ngay_chieu', $date)
            ->orderBy('gio_chieu', 'asc')
            ->get();

        if ($allTimeByDate->isEmpty()) {
            return response()->json([
                'message' => 'Không có giờ chiếu nào của ngày đã chọn !',
                'data' => [],
            ], 404);
        }

        return response()->json([
            'message' => 'Lấy tất cả giờ chiếu theo ngày thành công',
            'data' => $allTimeByDate,
        ], 200);
    }