<?php


// để yên
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
use App\Http\Controllers\Api\RotationController;
use App\Http\Controllers\Api\ShowtimeController;
use App\Http\Controllers\Api\TypeBlogController;
use App\Http\Controllers\Api\MembershipController;
use App\Http\Controllers\Api\MoviegenreController;
use App\Http\Controllers\Api\BookingDetailController;
use App\Http\Controllers\Api\RegisterMemberController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\Api\AuthController; //  auth api 
// để yên



// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


// route xu li , nhan xac thuc email ve email
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill(); // xác minh email thành công

    return response()->json([
        'message' => 'Email đã được xác minh thành công.'
    ], 200);
})->middleware(['auth:api', 'signed'])->name('verification.verify');
// xac minh an vao neu hien web foud loigin la ok se den de login

// 
Route::group(['middleware' => 'api', 'prefix' => 'auth'], function ($router) {
    // Đăng ký người dùng mới
    Route::post('registers', [AuthController::class, 'register']);

    // Đăng nhập và trả về token cho frontend
    Route::post('login', [AuthController::class, 'login']);

    // Lấy thông tin chi tiết của người dùng (yêu cầu phải có token hợp lệ)
    Route::get('profile', [AuthController::class, 'userProfile']);  

    // Đăng xuất (invalidate token để người dùng không thể tiếp tục sử dụng token cũ)
    Route::post('logout', [AuthController::class, 'logout']);
    // update tài khoản phía user
    Route::post('updateProfile', [AuthController::class, 'updateProfile']);
});



// call user : sửa , xóa , phân quyền , check quyền login : làm sau khi có admin 
// Ánh call user : sửa theo id , xóa theo id , show user theo id phía người dùng

// Ánh code user bên admin



// Ánh : call api moviegenres
Route::get('moviegenres', [MoviegenreController::class, 'index']);
Route::post('moviegenres', [MoviegenreController::class, 'store']);
Route::get('moviegenres/{id}', [MoviegenreController::class, 'show']);
Route::put('moviegenres/{id}', [MoviegenreController::class, 'update']);
Route::delete('moviegenres/{id}', [MoviegenreController::class, 'delete']);

//Ánh call api theaters
Route::get('theaters', [TheaterController::class, 'index']); // xuat all
Route::post('theaters', [TheaterController::class, 'store']); // them ban ghi moi
Route::get('theaters/{id}', [TheaterController::class, 'show']);  // show theo id
Route::put('theaters/{id}', [TheaterController::class, 'update']);  // cap nhat theo id
Route::delete('theaters/{id}', [TheaterController::class, 'delete']);  // xoa theo id

//Ánh call api rooms
Route::get('rooms', [RoomController::class, 'index']); // xuat all
Route::post('rooms', [RoomController::class, 'store']); // them ban ghi moi
Route::get('rooms/{id}', [RoomController::class, 'show']);  // show theo id
Route::put('rooms/{id}', [RoomController::class, 'update']);  // cap nhat theo id
Route::delete('rooms/{id}', [RoomController::class, 'delete']);  // xoa theo id

//Ánh call api xuat all ghe theo id room phòng , và all ghế 
Route::get('seats', [SeatController::class, 'index']); // xuat all
//Route::post('seats' , [SeatController::class , 'store']); // them ban ghi moi ko cần thiết
Route::get('seats/{id}', [SeatController::class, 'show']);  // show theo id
Route::put('seats/{id}', [SeatController::class, 'update']);  // cap nhat theo id
Route::delete('seats/{id}', [SeatController::class, 'delete']);  // xoa theo id

//Ánh call api movie
Route::get('movies', [MovieController::class, 'index']); // xuat all
Route::post('movies', [MovieController::class, 'store']); // them ban ghi moi
Route::get('movies/{id}', [MovieController::class, 'show']);  // show theo id
Route::put('movies/{id}', [MovieController::class, 'update']);  // cap nhat theo id
Route::delete('movies/{id}', [MovieController::class, 'delete']);  // xoa theo id

// Ánh : call api Foods
Route::get('foods', [FoodController::class, 'index']); // xuat all
Route::post('foods', [FoodController::class, 'store']); // them ban ghi moi
Route::get('foods/{id}', [FoodController::class, 'show']);  // show theo id
Route::put('foods/{id}', [FoodController::class, 'update']);  // cap nhat theo id
Route::delete('foods/{id}', [FoodController::class, 'delete']);  // xoa theo id

// Ánh : call api showtimes
Route::get('showtimes', [ShowtimeController::class, 'index']); // xuat all
Route::post('showtimes', [ShowtimeController::class, 'store']); // them ban ghi moi
Route::get('showtimes/{id}', [ShowtimeController::class, 'show']);  // show theo id
Route::put('showtimes/{id}', [ShowtimeController::class, 'update']);  // cap nhat theo id
Route::delete('showtimes/{id}', [ShowtimeController::class, 'delete']);  // xoa theo id

// Ánh : call api vouchers 
Route::get('vouchers', [VoucherController::class, 'index']); // xuat all
Route::post('vouchers', [VoucherController::class, 'store']); // them ban ghi moi
Route::get('vouchers/{id}', [VoucherController::class, 'show']);  // show theo id
Route::put('vouchers/{id}', [VoucherController::class, 'update']);  // cap nhat theo id
Route::delete('vouchers/{id}', [VoucherController::class, 'delete']);  // xoa theo id

// Ánh : call api Bookings // call sau call showtimes trước
//Route::get('bookings', [BookingController::class, 'index']); // xuat all
Route::post('bookings', [BookingController::class, 'store']); // them ban ghi moi
Route::get('bookings/{id}', [BookingController::class, 'show']);  // show theo id
Route::put('bookings/{id}', [BookingController::class, 'update']);  // cap nhat theo id
Route::delete('bookings/{id}', [BookingController::class, 'delete']);  // xoa theo id
// show chi tiết booking theo id
Route::get('bookings/{booking}/details', [BookingController::class, 'showBookingDetails']);


// // Ánh : call api Booking_details
Route::get('bookingdetails', [BookingDetailController::class, 'index']); // xuat all
Route::post('bookings/{booking}/select-seat', [BookingDetailController::class, 'selectSeat']); //http://127.0.0.1:8000/api/bookings/9/select-seat


// Ánh : call api Payments
Route::post('bookings/{booking}/payment', [PaymentController::class, 'processPayment']); //http://127.0.0.1:8000/api/bookings/9/payment


// Ánh : call countdownVoucher : săn mã voucher



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


//vòng quoay
Route::get('rotations', [RotationController::class, 'index']);
Route::post('rotations', [RotationController::class, 'store']);
Route::get('rotations/{id}', [RotationController::class, 'show']);
Route::put('rotations/{id}', [RotationController::class, 'update']);
Route::delete('rotations/{id}', [RotationController::class, 'destroy']);


//lien he
Route::get('contacts', [ContactController::class, 'index']);
Route::post('contacts', [ContactController::class, 'store']);
Route::get('contacts/{id}', [ContactController::class, 'show']);
Route::put('contacts/{id}', [ContactController::class, 'update']);
Route::delete('contacts/{id}', [ContactController::class, 'destroy']);
