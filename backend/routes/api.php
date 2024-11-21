<?php


use App\Models\Movie;
use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth;
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
use Illuminate\Routing\Controllers\HasMiddleware;
use App\Http\Controllers\Api\MembershipController;
use App\Http\Controllers\Api\MoviegenreController;
use App\Http\Controllers\Api\MemberShipsController;
use App\Http\Controllers\Api\BookingDetailController;
use App\Http\Controllers\Api\RegisterMemberController;
use App\Http\Controllers\Api\HistoryRotationsController;
use App\Http\Controllers\API\CountdownVoucherController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\Api\AuthController; //  auth api 





use App\Http\Controllers\Api\CouponCodeTakenController;

// route xu li , nhan xac thuc email ve email
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    
    // You can add logging here to debug
    \Illuminate\Support\Facades\Log::info('Email verified for user: ' . $request->user()->id);
    
    $frontendUrl = config('app.frontend_url', 'http://localhost:5173');
    return redirect($frontendUrl); // Add a query param to indicate success
})->middleware(['signed', 'throttle:6,1'])->name('verification.verify');


Route::group(['middleware' => 'api', 'prefix' => 'auth'], function ($router) {
    // Đăng ký người dùng mới
    Route::post('registers', [AuthController::class, 'register']);

    // Đăng nhập và trả về token cho frontend
    Route::post('login', [AuthController::class, 'login'])->name('login');

    // Các route yêu cầu xác thực token
    Route::middleware('auth:api')->group(function() {
        // Lấy thông tin chi tiết của người dùng
        Route::get('profile', [AuthController::class, 'userProfile']);
        
        // Đăng xuất - vô hiệu hóa token
        Route::post('logout', [AuthController::class, 'logout']);
        
        // Cập nhật thông tin tài khoản
        Route::post('updateProfile', [AuthController::class, 'updateProfile']);
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




// chi tiết theo id phim khi ấn vào phim ở home

Route::get('movie-detail/{id}', [MovieController::class, 'movieDetail']); 

Route::get('movie-detail/{movieID}/showtime-date/{date}', [MovieController::class, 'getShowtimesByDate']);

Route::get('movie-detail/{movieID}/showtime/{showtimeID}/seats', [MovieController::class, 'getSeatsByShowtime']);



Route::middleware('auth:api')->group(function () {

    Route::post('booking', [BookingController::class, 'Booking']);

    // đưa đến trang thanh toán với theo boooking id
    Route::post('payment/{bookingId}/{method}', [PaymentController::class, 'createPayment']);

    Route::get('booking-detail', [BookingDetailController::class, 'bookingDetail']);

    // in bill  
    Route::get('/bill/{id}', [BillController::class, 'exportBill']);

});

// Route::middleware('auth:api')->group(function () {

//     Route::get('memberships/{id}', [MemberShipsController::class, 'show']);
//     Route::post('/register-members/{hoivien_id}', [RegisterMemberController::class, 'store']);
// });

Route::get('payment/NCB-return', [PaymentController::class, 'NCBReturn']);
Route::get('payment/MasterCard-return', [PaymentController::class, 'mastercardReturn']);
Route::get('payment/Visa-return', [PaymentController::class, 'visaReturn']);


//Ánh call api rooms
Route::get('rooms', [RoomController::class, 'index']); // xuat all
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
Route::get('movieFilter/{id}', [MovieController::class, 'movieFilter']); // lọc phim theo thể loại
Route::get('movieFilterKeyword', [MovieController::class, 'movieFilterKeyword']); // lọc phim theo từ khóa


// Ánh : call api showtimes : thêm showtime theo phim id và rạp phim phòng
Route::get('showtimes', [ShowtimeController::class, 'index']); // xuat all
Route::get('addShowtime', [ShowtimeController::class, 'addShowtime']); // đưa đến from add thêm showtime đổ phòng + phim để thêm
Route::post('storeShowtime', [ShowtimeController::class, 'store']); // them ban ghi moi
Route::get('showShowtime/{id}', [ShowtimeController::class, 'show']);  // show theo id
Route::get('editShowtime/{id}', [ShowtimeController::class, 'editShowtime']);  // dua den trang edit
Route::put('updateShowtime/{id}', [ShowtimeController::class, 'update']);  // cap nhat theo id
Route::delete('deleteShowtime/{id}', [ShowtimeController::class, 'delete']);  // xoa theo id


// Ánh : call api Foods
Route::get('foods', [FoodController::class, 'index']); // xuat all
Route::post('storeFood', [FoodController::class, 'store']); // them ban ghi moi
Route::get('showFood/{id}', [FoodController::class, 'show']);  // show theo id
Route::get('editFood/{id}', [FoodController::class, 'edit']);  // đến from edit do du lieu theo id do
Route::put('updateFood/{id}', [FoodController::class, 'update']);  // cap nhat theo id
Route::delete('deleteFood/{id}', [FoodController::class, 'delete']);  // xoa theo id


// Ánh : call api vouchers 
Route::get('vouchers', [VoucherController::class, 'index']); // xuat all
Route::post('storeVoucher', [VoucherController::class, 'store']); // them ban ghi moi
Route::get('showVoucher/{id}', [VoucherController::class, 'show']);  // show theo id
Route::get('editVoucher/{id}', [VoucherController::class, 'edit']);  // dua den trang edit theo id do thong tin theo id
Route::put('updateVoucher/{id}', [VoucherController::class, 'update']);  // cap nhat theo id
Route::delete('vouchers/{id}', [VoucherController::class, 'delete']);  // xoa theo id







// call api type_blogs T
// Route::apiResource('type_blogs', TypeBlogController::class);
Route::get('type_blogs', [TypeBlogController::class, 'index']); // xuat all
Route::post('type_blogs', [TypeBlogController::class, 'store']); // them ban ghi moi
Route::get('type_blogs/{id}', [TypeBlogController::class, 'show']);  // show theo id
Route::put('type_blogs/{id}', [TypeBlogController::class, 'update']);  // cap nhat theo id
Route::delete('type_blogs/{id}', [TypeBlogController::class, 'delete']);  // xoa theo id
// call api BlogController T
Route::apiResource('blogs', BlogController::class);
Route::get('blogs', [BlogController::class, 'index']); // xuat all
Route::post('blogs', [BlogController::class, 'store']); // them ban ghi moi
Route::get('blogs/{id}', [BlogController::class, 'show']);  // show theo id
Route::put('blogs/{id}', [BlogController::class, 'update']);  // cap nhat theo id
Route::delete('blogs/{id}', [BlogController::class, 'delete']);  // xoa theo id




// call api MembershipController
Route::apiResource('memberships', MembershipsController::class);
Route::get('memberships', [MembershipsController::class, 'index']); // xuất all dữ liệu
Route::post('memberships', [MembershipsController::class, 'store']); // thêm bản ghi mới
Route::get('memberships/{id}', [MembershipsController::class, 'show']); // hiển thị theo id
Route::put('memberships/{id}', [MembershipsController::class, 'update']); // cập nhật theo id
Route::delete('memberships/{id}', [MembershipsController::class, 'destroy']); // xóa theo id


// call api MemberController
Route::apiResource('members', MemberController::class);
Route::get('members', [MemberController::class, 'index']); // xuất all dữ liệu
Route::post('members', [MemberController::class, 'store']); // thêm bản ghi mới
Route::get('members/{id}', [MemberController::class, 'show']); // hiển thị theo id
Route::put('members/{id}', [MemberController::class, 'update']); // cập nhật theo id
Route::delete('members/{id}', [MemberController::class, 'destroy']); // xóa theo id

Route::get('/membersa/types', [MemberController::class, 'getMemberTypes']); //lấy thẻ hội viên để đk
Route::put('/members/{id}/status', [MemberController::class, 'updateStatus']); // admin cập nhập ẩn member


// call api RegisterMemberController
Route::apiResource('registerMembers', RegisterMemberController::class);
Route::get('registerMembers', [RegisterMemberController::class, 'index']); // xuất all dữ liệu
Route::post('/register-members/{hoivien_id}', [RegisterMemberController::class, 'store']); // thêm bản ghi mới
Route::get('registerMembers/{id}', [RegisterMemberController::class, 'show']); // hiển thị theo id
Route::put('registerMembers/{id}', [RegisterMemberController::class, 'update']); // cập nhật theo id
Route::delete('registerMembers/{id}', [RegisterMemberController::class, 'destroy']); // xóa theo id
 




//vòng quoay
Route::get('rotations', [RotationsController::class, 'index']);
Route::post('rotations', [RotationsController::class, 'store']);
Route::get('rotations/{id}', [RotationsController::class, 'show']);
Route::put('rotations/{id}', [RotationsController::class, 'update']);
Route::delete('rotations/{id}', [RotationsController::class, 'destroy']);


//cal api contacts T
Route::get('contacts', [ContactController::class, 'index']);
Route::get('contacts/{id}', [ContactController::class, 'show']);
Route::get('/contacts/user/{user_id}', [ContactController::class, 'getByUserId']);
Route::post('contacts', [ContactController::class, 'store']);
Route::put('contacts/{id}', [ContactController::class, 'update']);
Route::delete('contacts/{id}', [ContactController::class, 'destroy']);
//call api rotations T
Route::get('rotations', [RotationsController::class, 'index']); // Lấy danh sách
Route::get('rotations/{id}', [RotationsController::class, 'show']); // Lấy chi tiết theo id
Route::post('rotations', [RotationsController::class, 'store']); // Tạo mới
Route::put('/rotations/{id}', [RotationsController::class, 'update']);
Route::delete('/rotations/{id}', [RotationsController::class, 'destroy']);



//call api quay thuong
// Route::get('/quay-thuong', [RotationsController::class, 'quayThuong'])->middleware('auth');
Route::post('/quay-thuong', [RotationsController::class, 'quayThuong']);



// Router cho lịch sử quay thưởng (History Rotations)
Route::get('history-rotations', [HistoryRotationsController::class, 'index']); // Xuất tất cả lịch sử quay thưởng
Route::get('addHistoryRotations', [HistoryRotationsController::class, 'addHistoryRotation']); // Đưa đến form thêm mới lịch sử quay thưởng (nếu cần)
Route::post('storeHistoryRotations', [HistoryRotationsController::class, 'store']); // Thêm bản ghi lịch sử quay thưởng mới
Route::get('history-rotations/{id}', [HistoryRotationsController::class, 'show']);  // Hiển thị lịch sử quay thưởng theo ID
Route::get('editHistoryRotation/{id}', [HistoryRotationsController::class, 'editHistoryRotation']);  // Đưa đến trang chỉnh sửa
Route::put('editHistoryRotation/{id}', [HistoryRotationsController::class, 'update']);  // Cập nhật lịch sử quay thưởng theo ID
Route::delete('history-rotations/{id}', [HistoryRotationsController::class, 'delete']);  // Xóa lịch sử quay thưởng theo ID


//call api countdown_vouchers

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

Route::post('/register-members/{registerMember}/process-payment', [PaymentController::class, 'processPaymentForRegister']);
Route::get('payment/NCB-return1', [PaymentController::class, 'paymentReturn1']);

// Route::middleware('auth:api')->get('/email/verification-status', function (Request $request) {
//     return response()->json([
//         'is_verified' => !is_null($request->user()->email_verified_at),
//         'verified_at' => $request->user()->email_verified_at
//     ]);
// });
Route::post('/register-members/{hoivien_id}/{method}', [PaymentController::class, 'createPayment1']);

Route::get('payment/NCB-return1', [PaymentController::class, 'NCBReturn1']);
