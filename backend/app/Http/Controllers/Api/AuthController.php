<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Member;
use App\Models\Booking;
use Illuminate\Http\Request;
use App\Models\RegisterMember;
use App\Models\PasswordResetToken;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Password;
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
        $user->sendEmailVerificationNotification();
        
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
        // $dataRegisterMember = RegisterMember::where('user_id', auth()->id())->get();

        if (!auth()->check()) {
            return response()->json(['error' => 'Bạn hiện chưa có tài khoản'], 401);
        }
        return response()->json([
            'data' => [
                'user' => auth()->user(),
                // trả về dữ liệu booking
                'Booking' => $dataBooking,
                // 'RegisterMember' => $dataRegisterMember,
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
            // dùng email để cập nhật mật khẩu
            // 'password' => 'nullable|string|min:8|confirmed', // Cho phép trường password không bắt buộc
            'gioi_tinh' => 'required|in:nam,nu,khac',
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
    public function sendResetLinkEmail(Request $request)
    {
        // Validate email input
        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Email không hợp lệ',
                'errors' => $validator->errors()
            ], 400);
        }
    
        // Kiểm tra xem email có tồn tại trong cơ sở dữ liệu không
        $user = User::where('email', $request->email)->first();
    
        if (!$user) {
            return response()->json([
                'message' => "Không thể tìm thấy email"
            ], 404); // Trả về mã 404 nếu không tìm thấy email
        }
    
        // Attempt to send reset link
        $status = Password::sendResetLink([
            'email' => $user->email // Gửi email, không phải là đối tượng User
        ]);
    
        // Check status and return appropriate response
        if ($status === Password::RESET_LINK_SENT) {
            return response()->json([
                'message' => 'Link reset mật khẩu đã được gửi tới email của bạn'
            ], 200);
        }
    
        // Trả về thông báo lỗi nếu không thể gửi link
        return response()->json([
            'message' => 'Đã xảy ra lỗi khi gửi link reset mật khẩu. Vui lòng thử lại.'
        ], 500);
    }
    
    
    public function resetPassword(Request $request, $token){
          // Tìm token đặt lại mật khẩu từ bảng password_reset_tokens
        $passwordReset = PasswordResetToken::where('token', $token)->first();

        // Kiểm tra token có tồn tại không
        if (!$passwordReset) {
            return response()->json([
                'message' => 'Token không hợp lệ hoặc đã hết hạn.'
            ], 422);
        }

        // Tìm người dùng theo email từ bản ghi reset password
        $user = User::where('email', $passwordReset->email)->first();

        // Kiểm tra người dùng có tồn tại không
        if (!$user) {
            return response()->json([
                'message' => 'Không tìm thấy người dùng với email này.'
            ], 404);
        }

        // Cập nhật mật khẩu mới cho người dùng
        $user->password = bcrypt($request->input('password'));
        $user->save();

        // Xóa token sau khi thành công
        $passwordReset->delete();

        return response()->json([
            'message' => 'Mật khẩu đã được cập nhật thành công!'
        ]);
    }
    
}
