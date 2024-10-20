<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Member;
use App\Models\RegisterMember;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    // Đăng nhập và trả về token
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Thực hiện xác thực và tạo token
        if (!$token = JWTAuth::attempt($validator->validated())) {
            return response()->json(['error' => 'Không thể đăng nhập'], 401);
        }

        // Trả về token
        return $this->createNewToken($token);
    }

    // Đăng ký tài khoản người dùng với xác thực
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ho_ten' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'so_dien_thoai' => 'required|string|max:10|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'gioi_tinh' => 'required|in:nam,nu,khac',
            'vai_tro' => 'required|in:user,admin,nhan_vien',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $user = User::create(array_merge(
            $validator->validated(),
            ['password' => bcrypt($request->password)]
        ));

        // khi nào cần xác nhận bằng mail thì dùng 
        // $user->sendEmailVerificationNotification();

        return response()->json([
            'message' => 'Đăng ký tài khoản thành công, Kiểm tra email để xác thực email chính chủ'
        ], 201);
    }

    // Tạo token mới khi người dùng đăng nhập
    protected function createNewToken($token)
    {
        return response()->json([
            'access-token' => $token,
            'token_type' => 'bearer',
            'expires_in' => config('jwt.ttl') * 60, // Lấy TTL từ tệp cấu hình
            'auth' => auth()->user(),
        ]);
    }

    // Hiển thị thông tin người dùng
    public function userProfile()
    {
        // dữ liệu booking user đã đặt
        $dataBooking = Booking::where('user_id', auth()->id())->orderBy('id', 'DESC')->get();
        $dataRegisterMember = RegisterMember::where('user_id', auth()->id())->get();

        if (!auth()->check()) {
            return response()->json(['error' => 'Bạn hiện chưa có tài khoản'], 401);
        }
        return response()->json([
            'data' => [
                'user' => auth()->user(),
                // trả về dữ liệu booking
                'Booking' => $dataBooking,
                'RegisterMember' => $dataRegisterMember,
            ],
        ]);
    }

    // Đăng xuất người dùng
    public function logout()
    {
        try {
            auth()->logout();
            return response()->json(['message' => 'đăng xuất thành công']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Đã xảy ra lỗi khi đăng xuất'], 500);
        }
    }

    public function updateProfile(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate(); // Lấy đối tượng User

        $validator = Validator::make($request->all(), [
            'ho_ten' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->user_id,
            'so_dien_thoai' => 'required|string|max:10|unique:users,so_dien_thoai,' . $user->user_id,
            'gioi_tinh' => 'required|in:nam,nu,khac',
            'vai_tro' => 'required|in:user,admin,nhan_vien',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        // Chỉ lấy những trường cần cập nhật
        $dataToUpdate = $validator->validated();

        // Cập nhật thông tin người dùng
        $user->update($dataToUpdate); // Sử dụng update

        return response()->json([
            'message' => 'Bạn đã cập nhật tài khoản thành công'
        ]);
    }

    // xác nhận đăng ký thành công
}
