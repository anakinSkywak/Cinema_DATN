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





    // đến trang thanh toán modal voucher đổ ra chọn voucher và dùng voucher có
    // su dung voucher update lại giá với giá mới và thanh toán

    public function useCouponUpdatePrice(Request $request, $bookingID, $couponID)
    {

        $user = auth()->user();
        if (!$user) {
            return response()->json(['message' => 'Chưa đăng nhập, vui lòng đăng nhập'], 401);
        }

        // truy vấn mã giảm giá
        // và update gía booking theo id
        $booking = Booking::find($bookingID);

        // check booking
        if (!$booking) {
            return response()->json([
                'message' => 'Không có booking theo id này !'
            ], 404);
        }

        // truy vấn coupon sử dụng coupon giảm giá tiền booking theo id bookking
        $now = Carbon::now();

        // truy vấn mã giảm giá user login có điều kiện = 0 , ngày hết hạn lớn hớn ngày hiện tại
        $couponUser = DB::table('coupon_code_takens')
            ->join('countdown_vouchers', 'coupon_code_takens.countdownvoucher_id', '=', 'countdown_vouchers.id')
            ->join('coupons', 'countdown_vouchers.magiamgia_id', '=', 'coupons.id')
            ->where('coupon_code_takens.ngay_het_han', '>', $now)
            ->where('coupon_code_takens.id', $couponID)
            ->where('coupon_code_takens.trang_thai', 0)

            ->select(
                'coupon_code_takens.id as coupon_takens_id',
                'coupons.ma_giam_gia',
                'coupons.muc_giam_gia',
                'coupons.gia_don_toi_thieu',
                'coupons.Giam_max',
                'coupons.mota',
                'coupon_code_takens.ngay_het_han'
            )
            ->first();

        if (!$couponUser) {
            return response()->json([
                'message' => 'Mã giảm giá không hợp lệ hoặc đã hết hạn !'
            ], 400);
        }

        // kiểm tra giá trị đơn hàng có đủ điều kiện để áp dụng không theo đơn tối thiểu
        if ($booking->tong_tien_thanh_toan < $couponUser->gia_don_toi_thieu) {
            return response()->json([
                'message' => "Đơn hàng không đủ giá trị tối thiểu ({$couponUser->gia_don_toi_thieu} VND) để áp dụng mã giảm giá !"
            ], 400);
        }

        // tính toán update giá tiền theo điều kiện mức giảm giá , đơn tối thiểu , Giam_max bao nhiều
        // tính tiền giảm giá
        $muc_giam_gia = ($couponUser->muc_giam_gia / 100) * $booking->tong_tien_thanh_toan;

        // áp dụng giá trị giảm tối đa Giam_max
        $so_tien_giam = min($muc_giam_gia, $couponUser->Giam_max);

        // tính tổng tiền mới để khi sử dụng coupon đủ all điều kiện
        $tong_tien_moi = $booking->tong_tien_thanh_toan - $so_tien_giam;

        // cập nhật số tiền ở booking 
        $booking->update([
            //'tong_tien' => $tong_tien_moi,
            'ma_giam_gia' => $couponUser->ma_giam_gia,
            'magiamgia_id' => $couponUser->coupon_takens_id,
            'tong_tien_thanh_toan' => $tong_tien_moi
        ]);

        return response()->json([
            'message' => 'Áp dụng mã giảm giá thành công!',
            'ma_giam_gia' => $couponUser->ma_giam_gia,
            'so_tien_giam' => $so_tien_giam,
            'tong_tien_moi' => $tong_tien_moi
        ], 200);
    }


    // hàm bỏ sử dụng coupon update giá booking theo id lại giá cũ trước khi dùng
    public function cancelCouponUpdatePrice(Request $request, $bookingID, $couponID)
    {

        $user = auth()->user();
        if (!$user) {
            return response()->json([
                'message' => 'Chưa đăng nhập, vui lòng đăng nhập'
            ], 401);
        }


        $booking = Booking::find($bookingID);
        if (!$booking || !$booking->tong_tien) {
            return response()->json([
                'message' => 'Không thể khôi phục giá gốc hoặc booking không hợp lệ'
            ], 404);
        }

        // bỏ chọn coupon update lại giá cũ khi sử dụng voucher
        $booking->update([
            'tong_tien_thanh_toan' => $booking->tong_tien,
            'ma_giam_gia' => null,
            'magiamgia_id' => null
        ]);

        return response()->json([
            'message' => 'Đã hủy mã giảm giá và khôi phục giá gốc thành công!',
            'tong_tien_thanh_toan' => $booking->tong_tien
        ], 200);
    }

    
    // sử dụng coupon khi ấn và modal khi chọn phương thức thanh toán
    Route::post('use-coupon/{bookingID}/{couponID}', [BookingController::class, 'useCouponUpdatePrice']);

    // bỏ sử dụng voucher update lại giá cũ
    Route::post('cancel-coupon/{bookingID}/{couponID}', [BookingController::class, 'cancelCouponUpdatePrice']);




    //5. Kiểm tra loại ghế có tồn tại trong bảng Seats
        // $checkTypeSeat = Seat::where('loai_ghe_ngoi', $request->loai_ghe)->doesntExist();
        // if ($checkTypeSeat) {
        //     return response()->json([
        //         'message' => 'Thể loại ghế không đúng trong Seats!',
        //     ], 409);
        // }

        //6. Kiểm tra khoảng thời gian có bị trùng lặp không
        // $exists = SeatPrice::where('loai_ghe', $request->loai_ghe)
        //     ->where('thu_trong_tuan', $request->thu_trong_tuan)
        //     ->where(function ($query) use ($request) {
        //         // Kiểm tra xem giờ bắt đầu và kết thúc có bị trùng với khoảng thời gian hiện tại
        //         $query->whereBetween('gio_bat_dau', [$request->gio_bat_dau, $request->gio_ket_thuc])
        //             ->orWhereBetween('gio_ket_thuc', [$request->gio_bat_dau, $request->gio_ket_thuc])
        //             ->orWhere(function ($query) use ($request) {
        //                 // Kiểm tra xem có bản ghi nào có giờ bắt đầu <= giờ bắt đầu của yêu cầu và giờ kết thúc >= giờ kết thúc của yêu cầu không
        //                 $query->where('gio_bat_dau', '<=', $request->gio_bat_dau)
        //                     ->where('gio_ket_thuc', '>=', $request->gio_ket_thuc);
        //             });
        //     })
        //     ->where(function ($query) use ($request) {
        //         // Cho phép trường hợp nếu giờ bắt đầu mới = giờ kết thúc cũ (ví dụ: 12:00 và 12:00)
        //         $query->where('gio_ket_thuc', '!=', $request->gio_bat_dau);
        //     })
        //     ->exists();

        // // Nếu đã có khoảng thời gian trùng với loại ghế và thứ trong tuần, thông báo lỗi
        // if ($exists) {
        //     return response()->json([
        //         'message' => 'Loại ghế, thứ, và khoảng thời gian đã tồn tại.',
        //     ], 422);
        // }
