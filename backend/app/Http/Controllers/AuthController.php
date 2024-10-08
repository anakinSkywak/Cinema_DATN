<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;

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
}
