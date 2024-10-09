<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
   // dang nhap tra ve token
   public function login(Request $request)
   {

       // du lieu de dang nhap cua user 
       $dataLogin = $request->only(['email', 'password']);

       // check token 
       // kiem tra thong tin dang nhap cua user voi attempt
       // xac thuc voi thong tin dang nhap JWTAuth::attempt($dataLogin)
       if (!$token = JWTAuth::attempt($dataLogin)) {
           
           // tra ve thong bao
           return response()->json([
               'error' => 'Thong tin dang nhap khong hop le !'
           ], 401);
       }

       // tra ve token neu dang nhap thanh cong
       return response()->json([
           'message' => 'Đăng nhập thành công !',
           'token' => $token
       ], 200);
   }


   
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
