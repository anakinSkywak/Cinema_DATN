<?php

// use App\Http\Controllers\Api\Movie_genreController;

use App\Models\Movie;
use App\Http\Controllers\Api\AuthController; //  auth api 
use App\Http\Controllers\Api\FoodController;
use App\Http\Controllers\Api\MovieController;
use App\Http\Controllers\Api\MoviegenreController;
use App\Http\Controllers\Api\RoomController;
use App\Http\Controllers\Api\SeatController;
use App\Http\Controllers\Api\ShowtimeController;
use App\Http\Controllers\Api\TheaterController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\VoucherController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TypeBlogController;
use App\Http\Controllers\Api\BlogController;


// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


// Ánh : call api Users
Route::post('registers', [UserController::class, 'register']);

// route xu li , nhan xac thuc email ve email
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill(); // xác minh email thành công

    return response()->json([
        'message' => 'Email đã được xác minh thành công.'
    ], 200);

})->middleware(['auth:api', 'signed'])->name('verification.verify');
// xac minh an vao neu hien web foud loigin la ok se den de login

// login tra ve token cho fronend 
Route::post('login',[AuthController::class , 'login']);
// api khac cua user viet sau 

//token check tra ve khi login : jwt token
// {
//     "message": "Đăng nhập thành công !",
//     "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYXBpL2xvZ2luIiwiaWF0IjoxNzI4MzUyMzY2LCJleHAiOjE3MjgzNTU5NjYsIm5iZiI6MTcyODM1MjM2NiwianRpIjoiVjVRdHhteWtTSENMM2lJQSIsInN1YiI6IjYiLCJwcnYiOiIyM2JkNWM4OTQ5ZjYwMGFkYjM5ZTcwMWM0MDA4NzJkYjdhNTk3NmY3Iiwicm9sZSI6InVzZXIifQ.iG5P6XdnFB451f6l_wtvsFyLXwDul7usor-MepFJ7w4"
// }

// call user : sửa , xóa , phân quyền , check quyền login : làm sau khi có admin 


// call api movie_genres // xóa của việt Ánh call lại
// Route::get('movie-genres', [Movie_genreController::class, 'index']);
// Route::post('movie-genres', [Movie_genreController::class, 'store']);
// Route::get('movie-genres/{id}', [Movie_genreController::class, 'show']);
// Route::put('movie-genres/{id}', [Movie_genreController::class, 'update']);
// Route::delete('movie-genres/{id}', [Movie_genreController::class, 'destroy']);


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
// Route::get('bookings', [ ::class, 'index']); // xuat all
// Route::post('bookings', [ ::class, 'store']); // them ban ghi moi
// Route::get('bookings/{id}', [ ::class, 'show']);  // show theo id
// Route::put('bookings/{id}', [ ::class, 'update']);  // cap nhat theo id
// Route::delete('bookings/{id}', [ ::class, 'delete']);  // xoa theo id


// Ánh : call api Payments


// Ánh : call api Booking_details

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
