<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Member;
use App\Models\Booking;
use App\Models\Comment;
use App\Mail\WelcomeEmail;
use Illuminate\Http\Request;
use App\Models\RegisterMember;
use App\Models\PasswordResetToken;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function __construct()
    {
        // nếu route nào không cần đăng nhập thì viết vào đây
        $this->middleware('auth:api', ['except' => [
            'login',
            'register',
            'sendResetLinkEmail',
            'resetPassword',
            'verifyEmail',
        ]]);
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

        // kiểm tra token có tồn tại không
        if (!$token = JWTAuth::attempt($validator->validated())) {
            return response()->json(['error' => 'Không thể đăng nhập'], 401);
        }


        // kiểm tra email có được xác thực không
        $user = auth()->user();
        if ($user->email_verified_at === null) {
            auth()->logout(); // đăng xuất user
            return response()->json([
                'message' => 'Email của bạn không được xác thực. Vui lòng kiểm tra email để xác thực tài khoản.'
            ], 401);
        }

        // nếu tất cả đúng thì trả về token
        return $this->createNewToken($token);
    }

    // làm mới token
    // cách dùng: 
    // 1. đăng nhập và lấy token
    // 2. gửi token đó đến api refresh-token
    // 3. nếu token hết hạn thì sẽ trả về token mới
    // bên frontend: 
    // 1. lưu token vào local storage
    // 2. gửi token đó đến api refresh-token
    // 3. nếu token hết hạn thì sẽ trả về token mới
    public function refresh()
    {
        try {
            $newToken = JWTAuth::refresh();
            return $this->createNewToken($newToken);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Không thể làm mới token'], 401);
        }
    }


    // Đăng ký tài khoản người dùng với xác thực
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ho_ten' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'so_dien_thoai' => 'required|string|max:10|unique:users',
            'password' => 'required|string|min:8',
            // ẩn các trường không cần thiết
            // 'gioi_tinh' => 'required|in:nam,nu,khac',
            // 'vai_tro' => 'required|in:user,admin,nhan_vien',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        try {
            $user = User::create(array_merge(
                $validator->validated(),
                [
                    'password' => bcrypt($request->password),
                    'email_verified_at' => Carbon::now()
                ]
            ));

            $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
            // Lưu cả email và OTP vào cache
            Cache::put('verify_email_' . $otp, $request->email, now()->addMinutes(5));
            Cache::put('verify_otp_' . $request->email, $otp, now()->addMinutes(5));

            // gửi email otp
            Mail::to($user->email)->send(new WelcomeEmail($user, $otp));

            return response()->json([
                'message' => 'Đăng ký tài khoản thành công!, vui lòng kiểm tra email để xác thực tài khoản',
                'user' => $user
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Đã xảy ra lỗi khi đăng ký tài khoản.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // xác thực email bằng OTP
    public function verifyEmail(Request $request)
    {
        try {
            $request->validate([
                'otp' => 'required|numeric'
            ]);

            // Lấy email từ cache dựa vào OTP
            $email = Cache::get('verify_email_' . $request->otp);
            if (!$email) {
                return response()->json([
                    'status' => false,
                    'message' => 'OTP không hợp lệ hoặc đã hết hạn'
                ], 400);
            }

            // Kiểm tra OTP có khớp không
            $cachedOTP = Cache::get('verify_otp_' . $email);
            if (!$cachedOTP || $cachedOTP != $request->otp) {
                return response()->json([
                    'status' => false,
                    'message' => 'OTP không hợp lệ'
                ], 400);
            }

            $user = User::where('email', $email)->first();
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'Không tìm thấy người dùng'
                ], 400);
            }

            $user->email_verified_at = Carbon::now();
            $user->save();

            // Xóa cả hai cache sau khi xác thực thành công
            Cache::forget('verify_email_' . $request->otp);
            Cache::forget('verify_otp_' . $email);

            return response()->json([
                'status' => true,
                'message' => 'Email đã được xác thực thành công'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }


    // khi người dùng quên không nhập otp để otp quá hạn
    public function refeshEmailOtp()
    {
        try {
            // Lấy tất cả các cache keys bắt đầu bằng 'verify_otp_'
            $cacheKeys = Cache::get('verify_otp_*');
            
            // Nếu không tìm thấy email nào trong cache
            if (!$cacheKeys) {
                return response()->json([
                    'message' => 'Không tìm thấy thông tin xác thực nào đang chờ'
                ], 404);
            }

            // Lấy email từ cache key đầu tiên
            $email = str_replace('verify_otp_', '', array_key_first($cacheKeys));
            
            // Kiểm tra thời gian chờ
            $lastRequestTime = Cache::get('last_otp_request_time_' . $email);
            // nếu thời gian chờ nhỏ hơn 60 giây thì trả về thông báo
            if ($lastRequestTime && now()->diffInSeconds($lastRequestTime) < 60) {
                // tính thời gian chờ, diffInSeconds là số giây giữa thời gian hiện tại và thời gian lần trước
                $waitTime = 60 - now()->diffInSeconds($lastRequestTime);
                return response()->json([
                    'message' => "Vui lòng đợi {$waitTime} giây trước khi yêu cầu gửi lại OTP."
                ], 429);
            }

            // Kiểm tra số lần gửi OTP
            $otpRequestCount = Cache::get('otp_request_count_' . $email, 0);
            if ($otpRequestCount >= 5) {
                return response()->json([
                    'message' => 'Bạn đã yêu cầu gửi OTP quá nhiều lần. Vui lòng thử lại sau 10 phút.'
                ], 429);
            }

            // Tạo OTP mới
            $newOtp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

            // Xóa cache OTP cũ
            $oldOtp = Cache::get('verify_otp_' . $email);
            if ($oldOtp) {
                Cache::forget('verify_email_' . $oldOtp);
                Cache::forget('verify_otp_' . $email);
            }

            // Lưu OTP mới vào cache
            Cache::put('verify_email_' . $newOtp, $email, now()->addMinutes(5));
            Cache::put('verify_otp_' . $email, $newOtp, now()->addMinutes(5));

            // Cập nhật số lần gửi và thời gian gửi
            Cache::put('otp_request_count_' . $email, $otpRequestCount + 1, now()->addMinutes(10));
            Cache::put('last_otp_request_time_' . $email, now());

            // Gửi email OTP mới
            $user = User::where('email', $email)->first();
            Mail::to($email)->send(new WelcomeEmail($user, $newOtp));

            return response()->json([
                'message' => 'OTP mới đã được gửi đến email của bạn. Vui lòng kiểm tra email để xác thực tài khoản.',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Có lỗi xảy ra khi gửi OTP. Vui lòng thử lại sau.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    //tạo token mới khi người dùng đăng nhập
    protected function createNewToken($token)
    {
        return response()->json([
            'access-token' => $token,
            'token_type' => 'bearer',
            'expires_in' => 604800, // TTL = 7 ngày (604800 giây)
            'auth' => auth()->user(),
        ]); 
    }

    // hiển thị thông tin người dùng
    public function userProfile()
    {
        // dữ liệu booking user đã đặt
        $dataBooking = Booking::where('user_id', auth()->id())->orderBy('id', 'DESC')->get();
        $dataRegisterMember = RegisterMember::where('user_id', auth()->id())->get();
        $dataComment = Comment::where('user_id', auth()->id())->get();

        if (!auth()->check()) {
            return response()->json(['error' => 'Bạn hiện chưa có tài khoản'], 401);
        }

        // update anh


        return response()->json([
            'data' => [
                'user' => auth()->user(),
                // trả về dữ liệu booking
                'Booking' => $dataBooking,
                'RegisterMember' => $dataRegisterMember,
                'Comment' => $dataComment,
            ],
        ]);
    }

    // đăng xuất người dùng
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
        $user = JWTAuth::parseToken()->authenticate();

        $validator = Validator::make($request->all(), [
            'ho_ten' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'so_dien_thoai' => 'required|string|size:10|unique:users,so_dien_thoai,' . $user->id,
            'gioi_tinh' => 'required|in:nam,nu,khac',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        // chỉ lấy những trường cần cập nhật
        $dataToUpdate = $validator->validated();

        // cập nhật thông tin người dùng
        $user->update($dataToUpdate); // sử dụng update

        return response()->json([
            'message' => 'Bạn đã cập nhật tài khoản thành công'
        ]);
    }
  
    public function sendResetLinkEmail(Request $request)
    {
        // kiếm tra email có hợp lệ không
        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Email không hợp lệ',
                'errors' => $validator->errors()
            ], 400);
        }

        // kiểm tra xem email có tồn tại trong cơ sở dữ liệu không
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'message' => "Không thể tìm thấy email"
            ], 404);
        }

        // gửi link reset mật khẩu
        $status = Password::sendResetLink($request->only('email')); // Fixed: Pass request data directly

        // kiểm tra status và trả về response
        if ($status === Password::RESET_LINK_SENT) {
            return response()->json([
                'message' => 'Link reset mật khẩu đã được gửi tới email của bạn'
            ], 200);
        }

        // trả về thông báo lỗi nếu không thể gửi link
        return response()->json([
            'message' => 'Đã xảy ra lỗi khi gửi link reset mật khẩu. Vui lòng thử lại.'
        ], 500);
    }

    public function resetPassword(Request $request, $token)
    {
        // kiếm tra request có hợp lệ không
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
            'password_confirmation' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 400);
        }

        // sử dụng Laravel's built-in Password facade để xử lý reset
        $status = Password::reset(
            array_merge($request->only('email', 'password', 'password_confirmation'), ['token' => $token]),
            function ($user, $password) {
                $user->password = bcrypt($password);
                $user->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json([
                'message' => 'Mật khẩu đã được cập nhật thành công!'
            ]);
        }

        return response()->json([
            'message' => 'Không thể đặt lại mật khẩu. Vui lòng thử lại.'
        ], 500);
    }

    // show all user
    public function showAllUser()
    {
        $data = User::all();

        if (!$data) {
            return response()->json([
                "message" => "Không tìm thấy user"
            ], 404);
        }
        return response()->json([
            'message' => 'đã lấy thành công tất cả user',
            'data' => $data
        ]);
    }

    // update user bên admin
    public function updateUser(Request $request, $id)
    {
        $data = User::find($id);

        if (!$data) {
            return response()->json([
                "message" => "Không tìm thấy user"
            ], 404);
        }

        $data->update($request->all());

        return response()->json([
            "message" => "Bạn đã cập nhật user thành công"
        ], 200);
    }       

    // xóa user bên admin

    public function deleteUser($id)
    {
        $data = User::find($id);

        if (!$data) {
            return response()->json([
                "message" => "Không tìm thấy user"
            ], 404);
        }

        $data->delete();

        return response()->json([
            "message" => "Bạn đã xóa user thành công"
        ], 200);
    }

}
