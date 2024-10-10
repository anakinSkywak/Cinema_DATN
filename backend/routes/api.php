<?php

use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BlogController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\MemberController;
use App\Http\Controllers\Api\TypeBlogController;
use App\Http\Controllers\Api\MemberShipsController;
use App\Http\Controllers\Api\Movie_genreController;
use App\Http\Controllers\Api\RegisterMemberController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('/user', function (Request $request) {
    return $request->user();
});
Route::post('registers', [UserController::class, 'register']);
Route::get('movie-genres', [Movie_genreController::class, 'index']);
Route::post('movie-genres', [Movie_genreController::class, 'store']);
Route::get('movie-genres/{id}', [Movie_genreController::class, 'show']);
Route::put('movie-genres/{id}', [Movie_genreController::class, 'update']);
Route::delete('movie-genres/{id}', [Movie_genreController::class, 'destroy']);


Route::apiResource('members', MemberController::class);
Route::apiResource('register-members', RegisterMemberController::class);
Route::apiResource('member-ships', MemberShipsController::class);




