<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use PHPUnit\Metadata\Uses;

class UserController extends Controller
{


    // register tai khoan user co xac thuc
    public function register(Request $request)
    {

        // check du lieu khi dang ky
        $validated = $request->validate([
            'ho_ten' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'so_dien_thoai' => 'required|string|max:10|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'gioi_tinh' => 'required|in:nam,nu,khac',
            'role' => 'required|in:user,admin,nhan_vien',
        ]);

        // tạo người user voi ma hoa mk vao db
        $user = User::create([
            'ho_ten' => $validated['ho_ten'],
            'email' => $validated['email'],
            'so_dien_thoai' => $validated['so_dien_thoai'],
            'password' => bcrypt($validated['password']), // 
            'gioi_tinh' => $validated['gioi_tinh'],
            'role' => $validated['role'],
        ]);
        //dd($user);

        // gui email xac thuc cho nguoi dung
        $user->sendEmailVerificationNotification();
        

        // tra ve check email de xac thuc la email chinh chu
        return response()->json([
            'message' => 'Đăng ký tài khoản thành công , Kiểm tra email để xác thực email chính chủ'
        ], 201);
    }



    /**
     * Display a listing of the resource.
     */
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