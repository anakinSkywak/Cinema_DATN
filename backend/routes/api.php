<?php

use App\Models\Movie;
use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth;
use App\Models\CouponCodeTaken;
use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\Group;
use App\Http\Controllers\Api\BillController;
use App\Http\Controllers\Api\BlogController;
use App\Http\Controllers\Api\FoodController;
use App\Http\Controllers\Api\RoomController;
use App\Http\Controllers\Api\SeatController;
use App\Http\Controllers\Api\MovieController;
use App\Http\Controllers\Api\MemberController;
use App\Http\Controllers\Api\MomentController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\CouponsController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\VoucherController;
use App\Http\Controllers\Api\ShowtimeController;
use App\Http\Controllers\Api\TypeBlogController;
use App\Http\Controllers\Api\RotationsController;
use App\Http\Controllers\Api\MembershipController;
use App\Http\Controllers\Api\MoviegenreController;
use App\Http\Controllers\Api\MemberShipsController;
use App\Http\Controllers\Api\BookingDetailController;
use App\Http\Controllers\Api\BookingTicketController;
use App\Http\Controllers\Api\RegisterMemberController;
use App\Http\Controllers\Api\CouponCodeTakenController;
use App\Http\Controllers\API\CountdownVoucherController;
use App\Http\Controllers\Api\HistoryRotationsController;
use App\Http\Controllers\Api\AuthController; //  auth api 
use App\Http\Controllers\Api\StatisticalController;


// xác thực email
Route::post('/email/verify-otp', [AuthController::class, 'verifyEmail'])
    // giới hạn số lần gửi mail
    ->middleware(['throttle:6,1'])
    ->name('verifyEmail');


//Route::middleware('auth:api', 'role:admin')->group(function () {
// show all user
Route::get('showAllUser', [AuthController::class, 'showAllUser']);

// update user bên admin
Route::put('updateUser/{id}', [AuthController::class, 'updateUser']);

// xóa user bên admin
Route::delete('deleteUser/{id}', [AuthController::class, 'deleteUser']);
//});


Route::group(['middleware' => 'api', 'prefix' => 'auth'], function ($router) {
    // Đăng ký người dùng mới
    Route::post('registers', [AuthController::class, 'register']);

    // Đăng nhập và trả về token cho frontend
    Route::post('login', [AuthController::class, 'login'])->name('login');

    // Các route yêu cầu xác thực token
    Route::middleware('auth:api')->group(function () {
        // Lấy thông tin chi tiết của người dùng
        Route::get('profile', [AuthController::class, 'userProfile']);
        Route::get('/user/voucher-codes', [CouponCodeTakenController::class, 'showVoucherCodes']);
        Route::post('/register-members/{hoivien_id}', [RegisterMemberController::class, 'store']);
        Route::put('/register-membera/{hoivien_id}', [RegisterMemberController::class, 'update']);
        Route::post('/register-members/{hoivien_id}/{method}', [PaymentController::class, 'createPayment1']);
        Route::middleware('auth:api')->get('/user/membership', [MembershipsController::class, 'show']);


        Route::post('/quay-thuong', [RotationsController::class, 'quayThuong']);
        // Đăng xuất - vô hiệu hóa token
        Route::post('logout', [AuthController::class, 'logout']);

        // Cập nhật thông tin tài khoản
        Route::put('updateProfile', [AuthController::class, 'updateProfile']);
    });

    // Route xử lý khi chưa xác thực
    Route::get('authenticationRoute', function () {
        return response()->json([
            'error' => 'hãy đăng nhập hoặc đăng ký để sử dụng dịch vụ này',
        ], 401);
    })->name('unauthenticated');
});

Route::post('forget_password', [AuthController::class, 'sendResetLinkEmail']);
Route::post('reset_password/{token}', [AuthController::class, 'resetPassword'])->name('password.reset');




// Client
// phim 2 home front 2 dạng Đang Chiếu and Sắp Công Chiếu
Route::get('movie-client', [MovieController::class, 'movieClient']);



// chi tiết theo id phim khi ấn vào phim ở home
// 1  user
//http://127.0.0.1:8000/api/movie-detail/31
Route::get('movie-detail/{id}', [MovieController::class, 'movieDetail']);

// 2 user
//http://127.0.0.1:8000/api/movie-detail/31/showtime-date/2024-11-19
Route::get('movie-detail/{movieID}/showtime-date/{date}', [MovieController::class, 'getShowtimesByDate']);

// 3 user
//http://127.0.0.1:8000/api/movie-detail/31/showtime-date/2024-11-19/09:30:00
Route::get('movie-detail/{movieID}/showtime-date/{date}/{time}', [MovieController::class, 'getRoomsByShowtime']);



Route::post('/select-seat', [BookingController::class, 'selectSeat']); //

Route::middleware('auth:api')->group(function () {

    // nhân viên book vé cho khách
    //5
    //http://127.0.0.1:8000/api/book-ticket
    Route::post('book-ticket', [BookingController::class, 'Bookticket']);

    // khóa ghế khi user chọn ghế và đến trang chọn đồ ăn
    Route::post('seat-lock', [BookingController::class, 'lockSeat']);

    // 4 user
    //http://127.0.0.1:8000/api/booking
    Route::post('booking', [BookingController::class, 'Booking']);

    //5 user
    //http://127.0.0.1:8000/api/payment/137/ncb
    // đưa đến trang thanh toán với theo boooking id
    Route::post('payment/{bookingId}/{method}', [PaymentController::class, 'createPayment']);

    // nhân viên
    //6
    //http://127.0.0.1:8000/api/payment/137/thanh_toan_tien_tai_quay
    Route::post('paymentBookTicket/{bookingId}/{method}', [PaymentController::class, 'createPaymentBookTicket']);

    // booking detail theo user id book thanh toán xong chuyến đến trang này đổ all booking detail đã bookng ra
    // dữ liệu ok
    Route::get('booking-detail', [BookingDetailController::class, 'bookingDetail']);
});



// Nhân viên
//1
//http://127.0.0.1:8000/api/movie-book-all
Route::get('movie-book-all', [BookingTicketController::class, 'listMovieBookTicket']);
//2
//http://127.0.0.1:8000/api/movie-book-id/42
Route::get('movie-book-id/{id}', [BookingTicketController::class, 'MovieByShowtimeID']);
//3
//http://127.0.0.1:8000/api/movie-book-id/42/showtime-date/2024-11-24
Route::get('movie-book-id/{movieID}/showtime-date/{date}', [BookingTicketController::class, 'getShowtimesByDate']);
//4
//http://127.0.0.1:8000/api/movie-book-id/42/showtime-date/2024-11-24/08:00:00
Route::get('movie-book-id/{movieID}/showtime-date/{date}/{time}', [BookingTicketController::class, 'getRoomsByShowtime']);



// return user 
Route::get('payment/ncb-return', [PaymentController::class, 'NCBReturn']);
Route::get('payment/mastercard-return', [PaymentController::class, 'mastercardReturn']);
Route::get('payment/visa-return', [PaymentController::class, 'visaReturn']);


// booking all bên admin
Route::get('booking-all', [BookingController::class, 'index']);
Route::get('payment-all', [PaymentController::class, 'index']);

// còn check tiếp tìm kiếm : nhân viên
// Ánh booking detail all , tìm đơn của khách , xác nhận khách đến
Route::get('booking-detail-all', [BookingDetailController::class, 'bookingDetailAll']);
Route::get('search-booking-detail/{search}', [BookingDetailController::class, 'searchBookingDetail']);
Route::put('confirm-booking-detail/{id}', [BookingDetailController::class, 'confirmArrival']);


//Ánh call api rooms
Route::get('rooms', [RoomController::class, 'index']);
Route::post('storeRoom', [RoomController::class, 'store']);
Route::get('showRoom/{id}', [RoomController::class, 'show']);
Route::get('editRoom/{id}', [RoomController::class, 'editRoom']);
Route::put('updatetRoom/{id}', [RoomController::class, 'update']);
Route::delete('deleteRoom/{id}', [RoomController::class, 'delete']);
Route::get('seatAllRoom/{id}', [RoomController::class, 'allSeatRoom']);
Route::put('baoTriSeat/{id}', [RoomController::class, 'baoTriSeat']);
Route::put('tatbaoTriSeat/{id}', [RoomController::class, 'tatbaoTriSeat']);
Route::delete('delete-all-seatbyroom/{id}' , [RoomController::class , 'deleteAllSeatByRoom']);


//Ánh call api xuat all ghe theo id room phòng , và all ghế 
Route::get('seats', [SeatController::class, 'index']);
Route::get('addSeat', [SeatController::class, 'addSeat']);
Route::post('storeOneSeat', [SeatController::class, 'storeOneSeat']);
Route::post('storeSeat', [SeatController::class, 'store']);
Route::get('showSeat/{id}', [SeatController::class, 'show']);
Route::get('editSeat/{id}', [SeatController::class, 'editSeat']);
Route::put('updateSeat/{id}', [SeatController::class, 'update']);
Route::delete('deleteSeat/{id}', [SeatController::class, 'delete']);


// Ánh : call api moviegenres
Route::get('moviegenres', [MoviegenreController::class, 'index']);
Route::post('storeMoviegenre', [MoviegenreController::class, 'store']);
Route::get('showMoviegenre/{id}', [MoviegenreController::class, 'show']);
Route::get('editMoviegenre/{id}', [MoviegenreController::class, 'edit']);
Route::put('updateMoviegenre/{id}', [MoviegenreController::class, 'update']);
Route::delete('moviegenre/{id}', [MoviegenreController::class, 'delete']);


//Ánh call api movie
Route::get('movies', [MovieController::class, 'index']);
Route::get('addMovie', [MovieController::class, 'getMovieGenre']);
Route::post('storeMovie', [MovieController::class, 'store']);
Route::get('showMovie/{id}', [MovieController::class, 'show']);
Route::get('editMovie/{id}', [MovieController::class, 'showEditID']);
Route::post('updateMovie/{id}', [MovieController::class, 'update']);
Route::delete('movies/{id}', [MovieController::class, 'delete']);
Route::get('movieFilter/{id}', [MovieController::class, 'movieFilter']);
Route::get('movieFilterKeyword', [MovieController::class, 'movieFilterKeyword']);


// Ánh : call api showtimes : thêm showtime theo phim id và rạp phim phòng
Route::get('showtimes', [ShowtimeController::class, 'index']);
Route::get('list-showtime' , [ShowtimeController::class , 'listshowtimeByMovie']);
Route::get('showtime-by-movie/{movieID}' , [ShowtimeController::class , 'showtimeByMovie']);
Route::get('addShowtime', [ShowtimeController::class, 'addShowtime']);
Route::post('storeShowtime', [ShowtimeController::class, 'store']);
Route::get('showShowtime/{id}', [ShowtimeController::class, 'show']);
Route::get('editShowtime/{id}', [ShowtimeController::class, 'editShowtime']);
Route::put('updateShowtime/{id}', [ShowtimeController::class, 'update']);
Route::delete('deleteShowtime/{id}', [ShowtimeController::class, 'delete']);
Route::get('search-showtime' , [ShowtimeController::class , 'searchShowtimes']);


// Ánh : call api Foods
Route::get('foods', [FoodController::class, 'index']);
Route::post('storeFood', [FoodController::class, 'store']);
Route::get('showFood/{id}', [FoodController::class, 'show']);
Route::get('editFood/{id}', [FoodController::class, 'edit']);
Route::post('updateFood/{id}', [FoodController::class, 'update']);
Route::delete('deleteFood/{id}', [FoodController::class, 'delete']);
Route::put('stopFood/{id}', [FoodController::class, 'stopFood']);
Route::put('openFood/{id}', [FoodController::class, 'openFood']);


// Ánh : call api vouchers 
Route::get('vouchers', [VoucherController::class, 'index']);
Route::post('storeVoucher', [VoucherController::class, 'store']);
Route::get('showVoucher/{id}', [VoucherController::class, 'show']);
Route::get('editVoucher/{id}', [VoucherController::class, 'edit']);
Route::put('updateVoucher/{id}', [VoucherController::class, 'update']);
Route::delete('vouchers/{id}', [VoucherController::class, 'delete']);





// call api MembershipController
Route::apiResource('memberships', MembershipsController::class);
Route::get('memberships', [MembershipsController::class, 'index']); // xuất all dữ liệu
Route::post('memberships', [MembershipsController::class, 'store']); // thêm bản ghi mới
// Route::get('memberships/{id}', [MembershipsController::class, 'show']); // hiển thị theo id
Route::middleware('auth:api')->get('/membership/{id}', [MembershipsController::class, 'show']);
Route::put('memberships/{id}', [MembershipsController::class, 'update']); // cập nhật theo id
Route::delete('memberships/{id}', [MembershipsController::class, 'destroy']); // xóa theo id
// call api MemberController
Route::apiResource('members', MemberController::class);
Route::middleware(['auth:api'])->get('members', [MemberController::class, 'index']); // xuất all dữ liệu
Route::middleware(['auth:api'])->post('members', [MemberController::class, 'store']); // thêm bản ghi mới
Route::middleware(['auth:api'])->get('members/{id}', [MemberController::class, 'show']); // hiển thị theo id
Route::put('members/{id}', [MemberController::class, 'update']); // cập nhật theo id
Route::delete('members/{id}', [MemberController::class, 'destroy']); // xóa theo id
Route::get('/membersa/types', [MemberController::class, 'getMemberTypes']); //lấy thẻ hội viên để đk
Route::middleware(['auth:api'])->put('/members/{id}/status', [MemberController::class, 'updateStatus']); // admin cập nhập ẩn member
// call api RegisterMemberController
Route::apiResource('registerMembers', RegisterMemberController::class);
Route::get('registerMembers', [RegisterMemberController::class, 'index']); // xuất all dữ liệu
Route::get('registerMembers/{id}', [RegisterMemberController::class, 'show']); // hiển thị theo id
Route::delete('registerMembers/{id}', [RegisterMemberController::class, 'destroy']); // xóa theo id

Route::middleware(['auth:api'])->get('/register-member', [RegisterMemberController::class, 'listRegisterMembersForUser']);

//vòng quoay
Route::get('rotations', [RotationsController::class, 'index']);
Route::post('rotations', [RotationsController::class, 'store']);
Route::get('rotations/{id}', [RotationsController::class, 'show']);
Route::put('rotations/{id}', [RotationsController::class, 'update']);
Route::delete('rotations/{id}', [RotationsController::class, 'destroy']);

//call api CouponCodeTaken T
Route::middleware(['auth:api'])->post('/spin-voucher', [CouponCodeTakenController::class, 'spinVoucher']);

//call api countdown_vouchers T
Route::get('countdown_vouchers/', [CountdownVoucherController::class, 'index']);
Route::post('countdown_vouchers', [CountdownVoucherController::class, 'store']);
Route::get('countdown_vouchers/{id}', [CountdownVoucherController::class, 'show']);
Route::put('countdown_vouchers/{id}', [CountdownVoucherController::class, 'update']);
Route::delete('countdown_vouchers/{id}', [CountdownVoucherController::class, 'destroy']);
// call api type_blogs T
Route::get('type_blogs', [TypeBlogController::class, 'index']);
Route::post('type_blogs', [TypeBlogController::class, 'store']);
Route::get('type_blogs/{id}', [TypeBlogController::class, 'show']);
Route::post('type_blogs/{id}', [TypeBlogController::class, 'update']);
Route::delete('type_blogs/{id}', [TypeBlogController::class, 'destroy']);
// call api BlogController T
Route::get('blogs', [BlogController::class, 'index']);
Route::post('blogs', [BlogController::class, 'store']);
Route::get('blogs/{id}', [BlogController::class, 'show']);
Route::post('blogs/{id}', [BlogController::class, 'update']);
Route::delete('blogs/{id}', [BlogController::class, 'delete']);
//cal api contacts T
Route::get('contacts/{id}', [ContactController::class, 'show']);
Route::get('/contact-details', [ContactController::class, 'getContactDetails'])
    ->name('contacts.details');

Route::middleware(['auth:api'])->post('contacts', [ContactController::class, 'store']);
Route::put('contacts/{id}', [ContactController::class, 'update']);
Route::delete('/contacts/{id}', [ContactController::class, 'destroy'])->name('contacts.destroy');
Route::post('/send-response/{contactId}', [ContactController::class, 'sendResponse']);
//call api rotations T
// Route::get('rotations', [RotationsController::class, 'index']); // Lấy danh sách
// Route::get('rotations/{id}', [RotationsController::class, 'show']); // Lấy chi tiết theo id
// Route::post('rotations', [RotationsController::class, 'store']); // Tạo mới
// Route::put('/rotations/{id}', [RotationsController::class, 'update']);
// Route::delete('/rotations/{id}', [RotationsController::class, 'destroy']);
// call api cho tạo ra mã giảm giá (*coupons) T
Route::get('coupons', [CouponsController::class, 'index']);
Route::post('coupons', [CouponsController::class, 'store']);
Route::get('coupons/{id}', [CouponsController::class, 'show']);
Route::put('coupons/{id}', [CouponsController::class, 'update']);
Route::delete('coupons/{id}', [CouponsController::class, 'destroy']);
//call api quay thuong
Route::middleware(['auth:api'])->post('/quay-thuong', [RotationsController::class, 'quayThuong']);
Route::middleware('auth:api')->get('/available-rotations', [HistoryRotationsController::class, 'getAvailableRotations']);



// Router cho lịch sử quay thưởng (History Rotations)
Route::get('history-rotations', [HistoryRotationsController::class, 'index']); // Xuất tất cả lịch sử quay thưởng
Route::get('addHistoryRotations', [HistoryRotationsController::class, 'addHistoryRotation']); // Đưa đến form thêm mới lịch sử quay thưởng (nếu cần)
Route::post('storeHistoryRotations', [HistoryRotationsController::class, 'store']); // Thêm bản ghi lịch sử quay thưởng mới
Route::get('history-rotations/{id}', [HistoryRotationsController::class, 'show']);  // Hiển thị lịch sử quay thưởng theo ID
Route::get('editHistoryRotation/{id}', [HistoryRotationsController::class, 'editHistoryRotation']);  // Đưa đến trang chỉnh sửa
Route::put('editHistoryRotation/{id}', [HistoryRotationsController::class, 'update']);  // Cập nhật lịch sử quay thưởng theo ID
Route::delete('history-rotations/{id}', [HistoryRotationsController::class, 'delete']);  // Xóa lịch sử quay thưởng theo ID





//call api moment
Route::get('moments', [MomentController::class, 'index']);
Route::post('moments', [MomentController::class, 'store']);
Route::get('moments/{id}', [MomentController::class, 'show']);
Route::put('moments/{id}', [MomentController::class, 'update']);
Route::delete('moments/{id}', [MomentController::class, 'destroy']);


Route::middleware('auth:api')->group(function () {
    Route::get('comments', [CommentController::class, 'index']);
    Route::post('comments', [CommentController::class, 'store']);
    Route::get('comments/{id}', [CommentController::class, 'show']);
    Route::put('comments/{id}', [CommentController::class, 'update']);
    Route::delete('comments/{id}', [CommentController::class, 'destroy']);
});

// việt làm thống kê
Route::get('getCountMovie', [StatisticalController::class, 'soLuongPhim']);
Route::get('getDoanhThuVe', [StatisticalController::class, 'doanhThuBanve']);
Route::get('getDoanhDoAn', [StatisticalController::class, 'doanhThuDoAn']);
Route::get('getSoLuongVoucher', [StatisticalController::class, 'thongKeSoLuongVoucher']);
Route::get('getDoanhThuPhim/{id}', [StatisticalController::class, 'thongKeDoanhThuPhim']);
Route::get('getDoanhPhongChieu/{id}', [StatisticalController::class, 'doanhThuPhongChieu']);
Route::get('getPhanLoaiUser', [StatisticalController::class, 'phanLoaiNguoiDung']);
Route::get('getPhanLoaiVe', [StatisticalController::class, 'tinhTrangVe']);
Route::get('getHinhThucThanhToan', [StatisticalController::class, 'hinhThucThanhToan']);
Route::get('getTopDatVe', [StatisticalController::class, 'topNguoiMuaVeNhieuNhat']);
Route::get('getTopVePhim', [StatisticalController::class, 'topPhimLuotveCao']);
Route::get('getDoanhThuThang', [StatisticalController::class, 'doanhThuThang']);

Route::get('payment/NCB-return1', [PaymentController::class, 'NCBReturn1']);
// Route::put('/register-member/{id}/{hoivien_id}', [RegisterMemberController::class, 'update']);