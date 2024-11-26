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
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\VoucherController;
use App\Http\Controllers\Api\ShowtimeController;
use App\Http\Controllers\Api\TypeBlogController;
use App\Http\Controllers\Api\RotationsController;
use App\Http\Controllers\Api\MembershipController;
use App\Http\Controllers\Api\MoviegenreController;
use App\Http\Controllers\Api\MemberShipsController;
use App\Http\Controllers\Api\CouponCodeTakenController;
use App\Http\Controllers\Api\BookingDetailController;
use App\Http\Controllers\Api\RegisterMemberController;
use App\Http\Controllers\API\CountdownVoucherController;
use App\Http\Controllers\Api\AuthController; //  auth api 


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
        //call api CouponCodeTaken T
        Route::post('/spin-voucher', [CouponCodeTakenController::class, 'spinVoucher']);
        Route::get('/user/voucher-codes', [CouponCodeTakenController::class, 'showVoucherCodes']);
        Route::post('/contacts', [ContactController::class, 'store'])->name('contacts.store');

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


//  phim ở home 1 trạng thái

//Route::get('movie-client', [MovieController::class, 'movieClient']);

// chi tiết theo id phim khi ấn vào phim ở home
// 1
//http://127.0.0.1:8000/api/movie-detail/31
Route::get('movie-detail/{id}', [MovieController::class, 'movieDetail']);

// 2
//http://127.0.0.1:8000/api/movie-detail/31/showtime-date/2024-11-19
Route::get('movie-detail/{movieID}/showtime-date/{date}', [MovieController::class, 'getShowtimesByDate']);

// 3
//http://127.0.0.1:8000/api/movie-detail/31/showtime-date/2024-11-19/09:30:00
Route::get('movie-detail/{movieID}/showtime-date/{date}/{time}', [MovieController::class, 'getRoomsByShowtime']);


Route::middleware('auth:api')->group(function () {

    // 4
    //http://127.0.0.1:8000/api/booking
    Route::post('booking', [BookingController::class, 'Booking']);

    //5
    //http://127.0.0.1:8000/api/payment/137/ncb
    // đưa đến trang thanh toán với theo boooking id
    Route::post('payment/{bookingId}/{method}', [PaymentController::class, 'createPayment']);

    // booking detail theo user id book thanh toán xong chuyến đến trang này đổ all booking detail đã bookng ra
    Route::get('booking-detail', [BookingDetailController::class, 'bookingDetail']);

    // in bill  
    //Route::get('/bill/{id}', [BillController::class, 'exportBill']);
});



//Route::get('movie-book-all', [BookingTicketController::class, 'listMovieBookTicket']);



Route::get('payment/NCB-return', [PaymentController::class, 'NCBReturn']);
Route::get('payment/MasterCard-return', [PaymentController::class, 'mastercardReturn']);
Route::get('payment/Visa-return', [PaymentController::class, 'visaReturn']);


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


//Ánh call api xuat all ghe theo id room phòng , và all ghế 
Route::get('seats', [SeatController::class, 'index']);
Route::get('addSeat', [SeatController::class, 'addSeat']);
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
Route::get('addShowtime', [ShowtimeController::class, 'addShowtime']);
Route::post('storeShowtime', [ShowtimeController::class, 'store']);
Route::get('showShowtime/{id}', [ShowtimeController::class, 'show']);
Route::get('editShowtime/{id}', [ShowtimeController::class, 'editShowtime']);
Route::put('updateShowtime/{id}', [ShowtimeController::class, 'update']);
Route::delete('deleteShowtime/{id}', [ShowtimeController::class, 'delete']);


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





// call api type_blogs T
// Route::apiResource('type_blogs', TypeBlogController::class);
Route::get('type_blogs', [TypeBlogController::class, 'index']); // xuat all
Route::post('type_blogs', [TypeBlogController::class, 'store']); // them ban ghi moi
Route::get('type_blogs/{id}', [TypeBlogController::class, 'show']);  // show theo id
Route::put('type_blogs/{id}', [TypeBlogController::class, 'update']);  // cap nhat theo id
Route::delete('type_blogs/{id}', [TypeBlogController::class, 'destroy']);  // xoa theo id
// call api BlogController T
Route::get('blogs', [BlogController::class, 'index']); // xuat all
Route::post('blogs', [BlogController::class, 'store']); // them ban ghi moi
Route::get('blogs/{id}', [BlogController::class, 'show']);  // show theo id
Route::put('blogs/{id}', [BlogController::class, 'update']);  // cap nhat theo id
Route::delete('blogs/{id}', [BlogController::class, 'delete']);  // xoa theo id




Route::apiResource('members', MemberController::class);
Route::apiResource('registermembers', RegisterMemberController::class);
Route::apiResource('memberships', MemberShipsController::class);

Route::apiResource('members', MemberController::class);
Route::get('members', [MemberController::class, 'index']); // xuất all dữ liệu
Route::post('members', [MemberController::class, 'store']); // thêm bản ghi mới
Route::get('members/{id}', [MemberController::class, 'show']); // hiển thị theo id
Route::put('members/{id}', [MemberController::class, 'update']); // cập nhật theo id
Route::delete('members/{id}', [MemberController::class, 'destroy']); // xóa theo id

// call api RegisterMemberController
Route::apiResource('registerMembers', RegisterMemberController::class);
Route::get('registerMembers', [RegisterMemberController::class, 'index']); // xuất all dữ liệu

Route::post('registerMembers', [RegisterMemberController::class, 'store']); // thêm bản ghi mới

Route::get('registerMembers/{id}', [RegisterMemberController::class, 'show']); // hiển thị theo id
Route::put('registerMembers/{id}', [RegisterMemberController::class, 'update']); // cập nhật theo id
Route::delete('registerMembers/{id}', [RegisterMemberController::class, 'destroy']); // xóa theo id


// call api MembershipController
Route::apiResource('memberships', MembershipController::class);
Route::get('memberships', [MembershipController::class, 'index']); // xuất all dữ liệu
Route::post('memberships', [MembershipController::class, 'store']); // thêm bản ghi mới
Route::get('memberships/{id}', [MembershipController::class, 'show']); // hiển thị theo id
Route::put('memberships/{id}', [MembershipController::class, 'update']); // cập nhật theo id
Route::delete('memberships/{id}', [MembershipController::class, 'destroy']); // xóa theo id


// call api MemberController
Route::apiResource('members', MemberController::class);
Route::get('members', [MemberController::class, 'index']); // xuất all dữ liệu
Route::post('members', [MemberController::class, 'store']); // thêm bản ghi mới
Route::get('members/{id}', [MemberController::class, 'show']); // hiển thị theo id
Route::put('members/{id}', [MemberController::class, 'update']); // cập nhật theo id
Route::delete('members/{id}', [MemberController::class, 'destroy']); // xóa theo id

// call api RegisterMemberController
Route::apiResource('registerMembers', RegisterMemberController::class);
Route::get('registerMembers', [RegisterMemberController::class, 'index']); // xuất all dữ liệu
Route::post('registerMembers', [RegisterMemberController::class, 'store']); // thêm bản ghi mới
Route::get('registerMembers/{id}', [RegisterMemberController::class, 'show']); // hiển thị theo id
Route::put('registerMembers/{id}', [RegisterMemberController::class, 'update']); // cập nhật theo id
Route::delete('registerMembers/{id}', [RegisterMemberController::class, 'destroy']); // xóa theo id


// call api MembershipController
Route::apiResource('memberships', MembershipController::class);
Route::get('memberships', [MembershipController::class, 'index']); // xuất all dữ liệu
Route::post('memberships', [MembershipController::class, 'store']); // thêm bản ghi mới
Route::get('memberships/{id}', [MembershipController::class, 'show']); // hiển thị theo id
Route::put('memberships/{id}', [MembershipController::class, 'update']); // cập nhật theo id
Route::delete('memberships/{id}', [MembershipController::class, 'destroy']); // xóa theo id



//cal api contacts T
Route::get('contacts', [ContactController::class, 'index']);
Route::get('contacts/{id}', [ContactController::class, 'show']);
Route::get('/contact-details', [ContactController::class, 'getContactDetails'])
    ->name('contacts.details');
Route::post('contacts', [ContactController::class, 'store']);
Route::put('contacts/{id}', [ContactController::class, 'update']);
Route::delete('/contacts/{id}', [ContactController::class, 'destroy'])->name('contacts.destroy');
Route::post('/send-response/{contactId}', [ContactController::class, 'sendResponse']);

//call api rotations T
Route::get('rotations', [RotationsController::class, 'index']); // Lấy danh sách
Route::get('rotations/{id}', [RotationsController::class, 'show']); // Lấy chi tiết theo id
Route::post('rotations', [RotationsController::class, 'store']); // Tạo mới
Route::put('/rotations/{id}', [RotationsController::class, 'update']);
Route::delete('/rotations/{id}', [RotationsController::class, 'destroy']);


//call api countdown_vouchers T
Route::get('countdown_vouchers/', [CountdownVoucherController::class, 'index']);
Route::post('countdown_vouchers', [CountdownVoucherController::class, 'store']);
Route::get('countdown_vouchers/{id}', [CountdownVoucherController::class, 'show']);
Route::put('countdown_vouchers/{id}', [CountdownVoucherController::class, 'update']);
Route::delete('countdown_vouchers/{id}', [CountdownVoucherController::class, 'destroy']);


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