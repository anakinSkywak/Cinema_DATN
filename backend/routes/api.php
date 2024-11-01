<?php

// để yên
// để yên
use App\Http\Controllers\Api\MemberShipsController;
use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BlogController;
use App\Http\Controllers\Api\FoodController;
use App\Http\Controllers\Api\RoomController;
use App\Http\Controllers\Api\SeatController;
use App\Http\Controllers\Api\MovieController;
use App\Http\Controllers\Api\MemberController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\TheaterController;
use App\Http\Controllers\Api\VoucherController;
use App\Http\Controllers\Api\ShowtimeController;
use App\Http\Controllers\Api\TypeBlogController;
use App\Http\Controllers\Api\MembershipController;
use App\Http\Controllers\Api\RotationsController;
use App\Http\Controllers\Api\MoviegenreController;
use App\Http\Controllers\Api\BookingDetailController;
use App\Http\Controllers\Api\RegisterMemberController;
use App\Http\Controllers\API\CountdownVoucherController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\Api\AuthController; //  auth api 
use Illuminate\Routing\Controllers\HasMiddleware;
use PHPUnit\Framework\Attributes\Group;

// để yên


// route xu li , nhan xac thuc email ve email
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill(); // xác minh email thành công

    return response()->json([
        'message' => 'Email đã được xác minh thành công.'
    ], 200);
})->middleware(['auth:api', 'signed'])->name('verification.verify');
// xac minh an vao neu hien web foud loigin la ok se den de login



Route::group(['middleware' => 'api', 'prefix' => 'auth'], function ($router) {
    // Đăng ký người dùng mới
    Route::post('registers', [AuthController::class, 'register']);

    // Đăng nhập và trả về token cho frontend
    Route::post('login', [AuthController::class, 'login'])->name('login');
    // Route::get('login', [AuthController::class, 'login'])->name('login');

    // Lấy thông tin chi tiết của người dùng (yêu cầu phải có token hợp lệ)
    Route::get('profile', [AuthController::class, 'userProfile']);

    // Đăng xuất (invalidate token để người dùng không thể tiếp tục sử dụng token cũ)
    Route::post('logout', [AuthController::class, 'logout']);
    // update tài khoản phía user
    Route::post('updateProfile', [AuthController::class, 'updateProfile']);
});

Route::post('forget_password', [AuthController::class, 'sendResetLinkEmail']);
Route::post('reset_password/{token}', [AuthController::class, 'resetPassword'])->name('password.reset');


// xuất all thông tin phim và các showtime của phim đó khi user ấn vào phim để chọn showtime để đặt
Route::get('movie-detail/{id}', [MovieController::class, 'movieDetail']);  // chi tiết theo id phim khi ấn vào phim ở home

Route::middleware('auth:api')->group(function () {

    //Route::post('booking', [BookingController::class, 'storeBooking']);
    //Route::post('booking/{booking}/selectService', [BookingController::class, 'selectService']);

    Route::post('booking', [BookingController::class, 'Booking']);
    // đưa đến trang thanh toán với theo boooking id
    Route::get('booking/{booking}/payment', [PaymentController::class, 'PaymentBooking']); 
    Route::post('booking/{booking}/payment', [PaymentController::class, 'processPaymentBooking']);

    // show all booking đã book cho user
    Route::get('booking-detail', [BookingDetailController::class, 'bookingDetail']);
});


//Ánh call api theaters
Route::get('theaters', [TheaterController::class, 'index']); // xuất all
Route::post('storeTheater', [TheaterController::class, 'store']); // them ban ghi moi
Route::get('showTheater/{id}', [TheaterController::class, 'show']);  // show theo id
Route::get('editTheater/{id}', [TheaterController::class, 'editTheaterID']); // đưa đến trang edit ổ thông tin edit ra
Route::put('updateTheater/{id}', [TheaterController::class, 'update']);  // cap nhat theo id
Route::delete('deleteTheater/{id}', [TheaterController::class, 'delete']);  // xoa theo id


//Ánh call api rooms
Route::get('rooms', [RoomController::class, 'index']); // xuat all
Route::get('addRoom', [RoomController::class, 'addroom']); // đưa đến trang from add đổ all rạp phim để khi thêm room chọn rạp phim
Route::post('storeRoom', [RoomController::class, 'store']); // them ban ghi moi
Route::get('showRoom/{id}', [RoomController::class, 'show']);  // show theo id
Route::get('editRoom/{id}', [RoomController::class, 'editRoom']);  // đưa đến from edit room theo id , đổ all rạp phim để chọn nếu thay đổi
Route::put('updatetRoom/{id}', [RoomController::class, 'update']);  // cap nhat room theo id
Route::delete('deleteRoom/{id}', [RoomController::class, 'delete']);  // xoa theo id
Route::get('seatAllRoom/{id}', [RoomController::class, 'allSeatRoom']);
Route::put('baoTriSeat/{id}', [RoomController::class, 'baoTriSeat']);
Route::put('tatbaoTriSeat/{id}', [RoomController::class, 'tatbaoTriSeat']);


//Ánh call api xuat all ghe theo id room phòng , và all ghế 
Route::get('seats', [SeatController::class, 'index']); // xuat all
Route::get('addSeat', [SeatController::class, 'addSeat']); // xuat ghế theo phòng
Route::post('storeSeat', [SeatController::class, 'store']); // thêm ghế theo phòng
Route::get('showSeat/{id}', [SeatController::class, 'show']);  // show theo id
Route::get('editSeat/{id}', [SeatController::class, 'editSeat']);  // show theo id
Route::put('updateSeat/{id}', [SeatController::class, 'update']);  // cap nhat theo id
Route::delete('deleteSeat/{id}', [SeatController::class, 'delete']);  // xoa theo id


// Ánh : call api moviegenres
Route::get('moviegenres', [MoviegenreController::class, 'index']);
Route::post('storeMoviegenre', [MoviegenreController::class, 'store']);
Route::get('showMoviegenre/{id}', [MoviegenreController::class, 'show']);
Route::get('editMoviegenre/{id}', [MoviegenreController::class, 'edit']);
Route::put('updateMoviegenre/{id}', [MoviegenreController::class, 'update']);
Route::delete('moviegenre/{id}', [MoviegenreController::class, 'delete']);


//Ánh call api movie
Route::get('movies', [MovieController::class, 'index']); // xuất all phim
Route::get('addMovie', [MovieController::class, 'getMovieGenre']); // chuyen huong den form them moi do the loai phim cho chon
Route::post('storeMovie', [MovieController::class, 'store']); // ấn lưu thêm mới phim mới với thể loại phim
Route::get('showMovie/{id}', [MovieController::class, 'show']);  // show theo id
Route::get('editMovie/{id}', [MovieController::class, 'showEditID']);  // show dữ liệu theo id để edit
Route::put('updateMovie/{id}', [MovieController::class, 'update']);  // cap nhat theo id
Route::delete('movies/{id}', [MovieController::class, 'delete']);  // xoa theo id
Route::post('movieFilter/{id}', [MovieController::class, 'movieFilter']); // lọc phim theo thể loại
Route::post('movieFilterKeyword', [MovieController::class, 'movieFilterKeyword']); // lọc phim theo từ khóa


// Ánh : call api Foods
Route::get('foods', [FoodController::class, 'index']); // xuat all
Route::post('storeFood', [FoodController::class, 'store']); // them ban ghi moi
Route::get('showFood/{id}', [FoodController::class, 'show']);  // show theo id
Route::get('editFood/{id}', [FoodController::class, 'edit']);  // đến from edit do du lieu theo id do
Route::put('updateFood/{id}', [FoodController::class, 'update']);  // cap nhat theo id
Route::delete('deleteFood/{id}', [FoodController::class, 'delete']);  // xoa theo id


// Ánh : call api showtimes : thêm showtime theo phim id và rạp phim phòng
Route::get('showtimes', [ShowtimeController::class, 'index']); // xuat all
Route::get('addShowtime', [ShowtimeController::class, 'addShowtime']); // đưa đến from add thêm showtime đổ rạp + phòng + phim để thêm
Route::post('storeShowtime', [ShowtimeController::class, 'store']); // them ban ghi moi
Route::get('showShowtime/{id}', [ShowtimeController::class, 'show']);  // show theo id
Route::get('editShowtime/{id}', [ShowtimeController::class, 'editShowtime']);  // dua den trang edit
Route::put('updateShowtime/{id}', [ShowtimeController::class, 'update']);  // cap nhat theo id
Route::delete('deleteShowtime/{id}', [ShowtimeController::class, 'delete']);  // xoa theo id


// Ánh : call api vouchers 
Route::get('vouchers', [VoucherController::class, 'index']); // xuat all
Route::post('storeVoucher', [VoucherController::class, 'store']); // them ban ghi moi
Route::get('showVoucher/{id}', [VoucherController::class, 'show']);  // show theo id
Route::get('editVoucher/{id}', [VoucherController::class, 'edit']);  // dua den trang edit theo id do thong tin theo id
Route::put('updateVoucher/{id}', [VoucherController::class, 'update']);  // cap nhat theo id
Route::delete('vouchers/{id}', [VoucherController::class, 'delete']);  // xoa theo id


// // Ánh : call api Booking_details
Route::get('bookingdetails', [BookingDetailController::class, 'index']); // xuat all

// Ánh : call api Payments
// Route::post('bookings/{booking}/payment', [PaymentController::class, 'processPayment']); //http://127.0.0.1:8000/api/bookings/9/payment




// call api type_blogs
// Route::apiResource('type_blogs', TypeBlogController::class);
Route::get('type_blogs', [TypeBlogController::class, 'index']); // xuat all
Route::post('type_blogs', [TypeBlogController::class, 'store']); // them ban ghi moi
Route::get('type_blogs/{id}', [TypeBlogController::class, 'show']);  // show theo id
Route::put('type_blogs/{id}', [TypeBlogController::class, 'update']);  // cap nhat theo id
Route::delete('type_blogs/{id}', [TypeBlogController::class, 'delete']);  // xoa theo id
// call api BlogController
Route::apiResource('blogs', BlogController::class);
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



//cal api contacts
Route::get('contacts', [ContactController::class, 'index']);
Route::get('contacts/{id}', [ContactController::class, 'show']);
Route::get('/contacts/user/{user_id}', [ContactController::class, 'getByUserId']);
Route::post('contacts', [ContactController::class, 'store']);
Route::put('contacts/{id}', [ContactController::class, 'update']);
Route::delete('contacts/{id}', [ContactController::class, 'destroy']);
//call api rotations
Route::get('rotations', [RotationsController::class, 'index']); // Lấy danh sách
Route::get('rotations/{id}', [RotationsController::class, 'show']); // Lấy chi tiết theo id
Route::post('rotations', [RotationsController::class, 'store']); // Tạo mới
Route::put('/rotations/{id}', [RotationsController::class, 'update']);
Route::delete('/rotations/{id}', [RotationsController::class, 'destroy']);


//call api countdown_vouchers
Route::get('countdown_vouchers/', [CountdownVoucherController::class, 'index']);
Route::post('countdown_vouchers', [CountdownVoucherController::class, 'store']);
Route::get('countdown_vouchers/{id}', [CountdownVoucherController::class, 'show']);
Route::put('countdown_vouchers/{id}', [CountdownVoucherController::class, 'update']);
Route::delete('countdown_vouchers/{id}', [CountdownVoucherController::class, 'destroy']);
