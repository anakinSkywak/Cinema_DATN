<?php

namespace App\Http\Controllers\Api;

use App\Mail\BookingPaymentSuccessMail;
use Auth;
use Carbon\Carbon;
use App\Models\Booking;
use App\Models\Payment;
use Endroid\QrCode\QrCode;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\BookingDetail;
use App\Models\RegisterMember;
use App\Models\MemberShips;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class PaymentController extends Controller
{


    public function index(){

        $payment = Payment::all();

        if($payment->isEmpty()){
            return response()->json([
                'message' => 'Không payment nào'
            ] , 404);
        }

        return response()->json([
            'message' => 'All payment',
            'data' => $payment
        ] , 200);

    
    }

    // nhân viên
    public function createPaymentBookTicket($bookingId, $method)
    {

        $user = auth()->user();
        if (!$user) {
            return response()->json([
                'message' => 'Chưa đăng nhập phải đăng nhập'
            ], 401);
        }

        $booking = Booking::find($bookingId);
        if (!$booking) {
            return response()->json(['message' => 'No booking id'], 404);
        }


        $money = $booking->tong_tien_thanh_toan;

        $payment = new Payment();
        $payment->booking_id = $booking->id;
        $payment->tong_tien = $money;
        $payment->tien_te = 'VND';
        $payment->phuong_thuc_thanh_toan = $method;
        $payment->ma_thanh_toan = $booking->id;
        $payment->trang_thai = 'Đã Hoàn Thành';
        $payment->ngay_thanh_toan = Carbon::now();
        $payment->save();

        switch ($method) {
            case 'thanh_toan_tien_tai_quay':
                return $this->paymentBookTicketNow($booking, $payment);
            default:
                return response()->json(['error' => 'Phương thức thanh toán không hợp lệ'], 400);
        }
    }
    // nhân viên
    public function paymentBookTicketNow($booking, $payment)
    {

        BookingDetail::insert([
            'booking_id' => $booking->id,
            'payment_id' => $payment->id,
        ]);

        Booking::where('id', $booking->id)->update(['trang_thai' => 2]);

        return response()->json([
            'message' => 'Mua vé và tạo vé thanh toán trực tiếp cho khách ok',

        ]);
    }


    // đưa đến from chọn phương thức thanh toán
    public function createPayment($bookingId, $method)
    {

        $user = auth()->user();
        if (!$user) {
            return response()->json([
                'message' => 'Chưa đăng nhập phải đăng nhập'
            ], 401);
        }

        $booking = Booking::find($bookingId);
        if (!$booking) {
            return response()->json(['message' => 'No booking id'], 404);
        }

        //enum('Đang chờ xử lý','Đã hoàn thành','Không thành công','Đã hoàn lại','Đã hủy')
        if ($booking->trang_thai !== 0) {
            return response()->json(['error' => 'Booking đã được thanh toán'], 400);
        }

        $money = $booking->tong_tien_thanh_toan;

        $payment = new Payment();
        $payment->booking_id = $booking->id;
        $payment->tong_tien = $money;
        //$payment->tien_te = 'VND'; 
        $payment->phuong_thuc_thanh_toan = $method;
        $payment->trang_thai = 'Đang chờ xử lý';
        $payment->ngay_thanh_toan = Carbon::now();
        $payment->save();

        // 'credit_card','paypal','cash','bank_transfer','vietqr','vnpay','viettel_money','payoo','mastercard','visa','ncb','jcb'
        switch ($method) {
            case 'ncb':
                return $this->paymentNCB($booking, $money, $payment);
            case 'mastercard':
                return $this->paymentMasterCard($booking, $money, $payment); //MasterCard
            case 'visa':
                return $this->paymentVISA($booking, $money, $payment); //VISA
            default:
                return response()->json(['error' => 'Phương thức thanh toán không hợp lệ'], 400);
        }
    }


    public function paymentNCB($booking, $money, $payment)
    {
        // Cấu hình của VNPAY
        $vnp_TmnCode = "0749VTZ7"; // Thay bằng mã TmnCode thực tế của bạn
        $vnp_HashSecret = "TTUJCPICUHRHA8PY7LLIQSCZU9Q7ND8U"; // Thay bằng mã HashSecret thực tế của bạn
        $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";


       //$vnp_ReturnUrl = "http://localhost:5173/transaction/success"; // URL xử lý sau khi thanh toán
       $vnp_ReturnUrl = "http://localhost:8000/api/payment/ncb-return"; // URL xử lý sau khi thanh toán

        

        $vnp_TxnRef = $booking->id; // Mã đơn hàng
        $vnp_OrderInfo = "Thanh toán booking ID: " . $booking->id;
        $vnp_OrderType = "billpayment";
        $vnp_Amount = intval($money * 100); // Đơn vị tính là đồng, nhân 100 để đúng định dạng
        $vnp_Locale = "vn";
        $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];
        $vnp_BankCode = "NCB"; // Mã ngân hàng demo để chuyển đến giao diện nhập thẻ

        // Dữ liệu cần gửi cho VNPAY
        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_ReturnUrl,
            "vnp_TxnRef" => $vnp_TxnRef,
            "vnp_BankCode" => $vnp_BankCode // Truyền mã ngân hàng vào đây
        );

        // Sắp xếp các tham số và tạo chuỗi query
        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        // Tạo hash và thêm vào URL
        $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
        $vnp_Url = $vnp_Url . "?" . $query . 'vnp_SecureHash=' . $vnpSecureHash;

        // Lưu thông tin thanh toán vào cơ sở dữ liệu
        $payment->ma_thanh_toan = $vnp_TxnRef;
        $payment->booking_id = $booking->id;
        $payment->chi_tiet_giao_dich = json_encode($inputData);
        $payment->save();

        return response()->json([
            'message' => 'Chuyển hướng đến trang thanh toán VNPAY',
            'url' => $vnp_Url,

        ]);
    }

    public function NCBReturn(Request $request)
    {
        $vnp_HashSecret = "TTUJCPICUHRHA8PY7LLIQSCZU9Q7ND8U";

        // Lấy tất cả dữ liệu từ request
        $inputData = $request->all();
        $vnp_SecureHash = $inputData['vnp_SecureHash'] ?? '';

        // Kiểm tra nếu `vnp_SecureHash` không có trong request
        if (!$vnp_SecureHash) {
            return response()->json(['message' => 'Thiếu dữ liệu vnp_SecureHash'], 400);
        }

        // Xóa khóa `vnp_SecureHash` khỏi dữ liệu để tính toán hash
        unset($inputData['vnp_SecureHash']);

        // Sắp xếp lại mảng dữ liệu theo thứ tự tăng dần của khóa
        ksort($inputData);

        // Tạo chuỗi dữ liệu để hash
        $hashData = http_build_query($inputData, '', '&');


        // Tính toán SecureHash từ chuỗi dữ liệu và khóa bí mật
        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        // Kiểm tra SecureHash có khớp không và mã phản hồi từ VNPAY
        if ($secureHash === $vnp_SecureHash) {
            if ($inputData['vnp_ResponseCode'] == '00') {


                // Tìm giao dịch thanh toán dựa trên mã thanh toán
                $payment = Payment::where('ma_thanh_toan', $inputData['vnp_TxnRef'])->first();

                if ($payment) {
                    // trạng thái thanh toán thành công
                    $payment->trang_thai = 'Đã hoàn thành';
                    $payment->save();
                } else {
                    return response()->json(['message' => 'Không tìm thấy giao dịch thanh toán'], 404);
                }

                // Tìm booking dựa trên mã giao dịch
                $booking = Booking::find($inputData['vnp_TxnRef']);

                if ($booking) {
                    $booking->trang_thai = 2;
                    $booking->save();
                }

                BookingDetail::insert([
                    'booking_id' => $booking->id,
                    'payment_id' => $payment->id,
                    //'trang_thai' => 0  // 0 la default ok con 1 thi se la check khach da den va xem phim
                ]);

                // thêm 1 lượt quay khi đặt và trả tiền vé ok để quay trưởng
                User::where('id', $booking->user_id)->increment('so_luot_quay', 1);


                Mail::to($booking->user->email)->send(new BookingPaymentSuccessMail($booking, $payment));

                return response()->json([
                    'message' => 'Thanh toán thành công',
                    'redirect_url' => 'http://localhost:5173/profile' // url tra ve khi thanh toan ok
                ]);

            } else {
                // Xử lý trường hợp `vnp_ResponseCode` không phải '00'
                return response()->json([
                    'message' => 'Thanh toán thất bại',
                    'error_code' => $inputData['vnp_ResponseCode'],
                    'error_message' => $this->getVnpayErrorMessage($inputData['vnp_ResponseCode'])
                ], 400);
            }
        } else {
            // Trả về phản hồi thất bại nếu không khớp SecureHash
            return response()->json(['message' => 'Xác thực chữ ký thất bại'], 400);
        }
    }



    public function paymentVISA($booking, $money, $payment)
    {

        // Cấu hình của VNPAY
        $vnp_TmnCode = "0749VTZ7"; // Thay bằng mã TmnCode thực tế của bạn
        $vnp_HashSecret = "TTUJCPICUHRHA8PY7LLIQSCZU9Q7ND8U"; // Thay bằng mã HashSecret thực tế của bạn
        $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
      
        //$vnp_ReturnUrl = "http://localhost:5173/transaction/success"; // URL xử lý sau khi thanh toán
        $vnp_ReturnUrl = "http://localhost:8000/api/payment/visa-return"; // URL xử lý sau khi thanh toán

        $vnp_TxnRef = $booking->id; // Mã đơn hàng
        $vnp_OrderInfo = "Thanh toán booking ID: " . $booking->id;
        $vnp_OrderType = "billpayment";
        $vnp_Amount = intval($money * 100); // Đơn vị tính là đồng, nhân 100 để đúng định dạng
        $vnp_Locale = "vn";
        $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];
        $vnp_BankCode = "VISA"; // Mã ngân hàng demo để chuyển đến giao diện nhập thẻ

        // Dữ liệu cần gửi cho VNPAY
        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_ReturnUrl,
            "vnp_TxnRef" => $vnp_TxnRef,
            "vnp_BankCode" => $vnp_BankCode // Truyền mã ngân hàng vào đây
        );

        // Sắp xếp các tham số và tạo chuỗi query
        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        // Tạo hash và thêm vào URL
        $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
        $vnp_Url = $vnp_Url . "?" . $query . 'vnp_SecureHash=' . $vnpSecureHash;

        // Lưu thông tin thanh toán vào cơ sở dữ liệu
        $payment->ma_thanh_toan = $vnp_TxnRef;
        $payment->booking_id = $booking->id;
        $payment->chi_tiet_giao_dich = json_encode($inputData);
        $payment->save();

        return response()->json([
            'message' => 'Chuyển hướng đến trang thanh toán Visa',
            'url' => $vnp_Url,

        ]);
    }
    public function visaReturn(Request $request)
    {
        $vnp_HashSecret = "TTUJCPICUHRHA8PY7LLIQSCZU9Q7ND8U";

        // Lấy tất cả dữ liệu từ request
        $inputData = $request->all();
        $vnp_SecureHash = $inputData['vnp_SecureHash'] ?? '';

        // Kiểm tra nếu `vnp_SecureHash` không có trong request
        if (!$vnp_SecureHash) {
            return response()->json(['message' => 'Thiếu dữ liệu vnp_SecureHash'], 400);
        }

        // Xóa khóa `vnp_SecureHash` khỏi dữ liệu để tính toán hash
        unset($inputData['vnp_SecureHash']);

        // Sắp xếp lại mảng dữ liệu theo thứ tự tăng dần của khóa
        ksort($inputData);

        // Tạo chuỗi dữ liệu để hash
        $hashData = http_build_query($inputData, '', '&');


        // Tính toán SecureHash từ chuỗi dữ liệu và khóa bí mật
        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        // Kiểm tra SecureHash có khớp không và mã phản hồi từ VNPAY
        if ($secureHash === $vnp_SecureHash) {
            if ($inputData['vnp_ResponseCode'] == '00') {

                // Giao dịch thành công

                // Tìm giao dịch thanh toán dựa trên mã thanh toán
                $payment = Payment::where('ma_thanh_toan', $inputData['vnp_TxnRef'])->first();

                if ($payment) {
                    // trạng thái thanh toán thành công
                    $payment->trang_thai = 'Đã hoàn thành';
                    $payment->save();
                } else {
                    return response()->json(['message' => 'Không tìm thấy giao dịch thanh toán'], 404);
                }

                // Tìm booking dựa trên mã giao dịch
                $booking = Booking::find($inputData['vnp_TxnRef']);

                if ($booking) {

                    $booking->trang_thai = 2; // Cập nhật trạng thái thành công ở booking
                    $booking->save();
                }

                BookingDetail::insert([
                    'booking_id' => $booking->id,
                    'payment_id' => $payment->id,
                    //'trang_thai' => 0  // 0 la default ok con 1 thi se la check khach da den va xem phim
                ]);

                // thêm 1 lượt quay khi đặt và trả tiền vé ok để quay trưởng
                User::where('id', $booking->user_id)->increment('so_luot_quay', 1);


                Mail::to($booking->user->email)->send(new BookingPaymentSuccessMail($booking, $payment));

                return response()->json([
                    'message' => 'Thanh toán thành công',
                    'redirect_url' => 'http://localhost:5173/profile' // url tra ve khi thanh toan ok
                ]);
            } else {
                // Xử lý trường hợp `vnp_ResponseCode` không phải '00'
                return response()->json([
                    'message' => 'Thanh toán thất bại',
                    'error_code' => $inputData['vnp_ResponseCode'],
                    'error_message' => $this->getVnpayErrorMessage($inputData['vnp_ResponseCode'])
                ], 400);
            }
        } else {
            // Trả về phản hồi thất bại nếu không khớp SecureHash
            return response()->json(['message' => 'Xác thực chữ ký thất bại'], 400);
        }
    }


    public function paymentMasterCard($booking, $money, $payment)
    {

        // Cấu hình của VNPAY
        $vnp_TmnCode = "0749VTZ7"; // Thay bằng mã TmnCode thực tế của bạn
        $vnp_HashSecret = "TTUJCPICUHRHA8PY7LLIQSCZU9Q7ND8U"; // Thay bằng mã HashSecret thực tế của bạn
        $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
        
        //$vnp_ReturnUrl = "http://localhost:5173/transaction/success"; // URL xử lý sau khi thanh toán
        $vnp_ReturnUrl = "http://localhost:8000/api/payment/mastercard-return"; // URL xử lý sau khi thanh toán

        $vnp_TxnRef = $booking->id; // Mã đơn hàng
        $vnp_OrderInfo = "Thanh toán booking ID: " . $booking->id;
        $vnp_OrderType = "billpayment";
        $vnp_Amount = intval($money * 100); // Đơn vị tính là đồng, nhân 100 để đúng định dạng
        $vnp_Locale = "vn";
        $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];
        $vnp_BankCode = "MasterCard"; // Mã ngân hàng demo để chuyển đến giao diện nhập thẻ

        // Dữ liệu cần gửi cho VNPAY
        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_ReturnUrl,
            "vnp_TxnRef" => $vnp_TxnRef,
            "vnp_BankCode" => $vnp_BankCode // Truyền mã ngân hàng vào đây
        );

        // Sắp xếp các tham số và tạo chuỗi query
        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        // Tạo hash và thêm vào URL
        $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
        $vnp_Url = $vnp_Url . "?" . $query . 'vnp_SecureHash=' . $vnpSecureHash;

        // Lưu thông tin thanh toán vào cơ sở dữ liệu
        $payment->ma_thanh_toan = $vnp_TxnRef;
        $payment->booking_id = $booking->id;
        $payment->chi_tiet_giao_dich = json_encode($inputData);
        $payment->save();

        return response()->json([
            'message' => 'Chuyển hướng đến trang thanh toán MasterCard',
            'url' => $vnp_Url,

        ]);
    }
    public function mastercardReturn(Request $request)
    {
        $vnp_HashSecret = "TTUJCPICUHRHA8PY7LLIQSCZU9Q7ND8U";

        // Lấy tất cả dữ liệu từ request
        $inputData = $request->all();
        $vnp_SecureHash = $inputData['vnp_SecureHash'] ?? '';

        // Kiểm tra nếu `vnp_SecureHash` không có trong request
        if (!$vnp_SecureHash) {
            return response()->json(['message' => 'Thiếu dữ liệu vnp_SecureHash'], 400);
        }

        // Xóa khóa `vnp_SecureHash` khỏi dữ liệu để tính toán hash
        unset($inputData['vnp_SecureHash']);

        // Sắp xếp lại mảng dữ liệu theo thứ tự tăng dần của khóa
        ksort($inputData);

        // Tạo chuỗi dữ liệu để hash
        $hashData = http_build_query($inputData, '', '&');


        // Tính toán SecureHash từ chuỗi dữ liệu và khóa bí mật
        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        // Kiểm tra SecureHash có khớp không và mã phản hồi từ VNPAY
        if ($secureHash === $vnp_SecureHash) {
            if ($inputData['vnp_ResponseCode'] == '00') {

                // Giao dịch thành công

                // Tìm giao dịch thanh toán dựa trên mã thanh toán
                $payment = Payment::where('ma_thanh_toan', $inputData['vnp_TxnRef'])->first();

                if ($payment) {
                    // trạng thái thanh toán thành công
                    $payment->trang_thai = 'Đã hoàn thành';
                    $payment->save();
                } else {
                    return response()->json(['message' => 'Không tìm thấy giao dịch thanh toán'], 404);
                }

                // Tìm booking dựa trên mã giao dịch
                $booking = Booking::find($inputData['vnp_TxnRef']);

                if ($booking) {

                    $booking->trang_thai = 2; // Cập nhật trạng thái thành công ở booking
                    $booking->save();
                }

                BookingDetail::insert([
                    'booking_id' => $booking->id,
                    'payment_id' => $payment->id,
                    //'trang_thai' => 0  // 0 la default ok con 1 thi se la check khach da den va xem phim
                ]);

                // thêm 1 lượt quay khi đặt và trả tiền vé ok để quay trưởng
                User::where('id', $booking->user_id)->increment('so_luot_quay', 1);


                Mail::to($booking->user->email)->send(new BookingPaymentSuccessMail($booking, $payment));

                return response()->json([
                    'message' => 'Thanh toán thành công',
                    'redirect_url' => 'http://localhost:5173/profile' // url tra ve khi thanh toan ok
                ]);
            } else {
                // Xử lý trường hợp `vnp_ResponseCode` không phải '00'
                return response()->json([
                    'message' => 'Thanh toán thất bại',
                    'error_code' => $inputData['vnp_ResponseCode'],
                    'error_message' => $this->getVnpayErrorMessage($inputData['vnp_ResponseCode'])
                ], 400);
            }
        } else {
            // Trả về phản hồi thất bại nếu không khớp SecureHash
            return response()->json(['message' => 'Xác thực chữ ký thất bại'], 400);
        }
    }


    public function getVnpayErrorMessage($code)
    {
        $errors = [
            '01' => 'Giao dịch đã tồn tại',
            '02' => 'Merchant không hợp lệ',
            '03' => 'Dữ liệu gửi không đầy đủ',
            '04' => 'Khóa bí mật không hợp lệ',

        ];

        return $errors[$code] ?? 'Lỗi không xác định';
    }

    public function createPayment1($registerMemberId, $method)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json([
                'message' => 'Chưa đăng nhập phải đăng nhập'
            ], 401);
        }

        $registerMember = RegisterMember::find($registerMemberId);
        if (!$registerMember) {
            return response()->json(['message' => 'No register member found'], 404);
        }

        // Check if payment has already been made
        if ($registerMember->trang_thai !== 0) {
            return response()->json(['error' => 'Thanh toán đã tồn tại'], 400);
        }

        $money = $registerMember->tong_tien;

        $payment = new Payment();
        $payment->registermember_id = $registerMember->id; // Changed from booking_id to registermember_id
        $payment->tong_tien = $money;
        $payment->phuong_thuc_thanh_toan = $method;
        $payment->trang_thai = 'Đang chờ xử lý';
        $payment->ngay_thanh_toan = Carbon::now();
        $payment->save();

        // Handle different payment methods
        switch ($method) {
            case 'ncb':
                return $this->paymentNCB1($registerMember, $money, $payment); // Changed booking to registerMember
            case 'vietqr':
                return $this->paymentVIETQR1($registerMember, $money, $payment); // Changed booking to registerMember
            case 'viettel_money':
                return $this->paymentVIETTELMONEY1($registerMember, $money, $payment); // Changed booking to registerMember
            case 'payoo':
                return $this->paymentPAYOO1($registerMember, $money, $payment); // Changed booking to registerMember
            case 'mastercard':
                return $this->paymentMasterCard1($registerMember, $money, $payment); // Changed booking to registerMember
            case 'visa':
                return $this->paymentVISA1($registerMember, $money, $payment); // Changed booking to registerMember
            default:
                return response()->json(['error' => 'Invalid payment method'], 400);
        }
    }

    public function paymentNCB1($registerMember, $money, $payment)
    {
        // VNPAY configuration
        $vnp_TmnCode = "0749VTZ7"; // Your actual TmnCode
        $vnp_HashSecret = "TTUJCPICUHRHA8PY7LLIQSCZU9Q7ND8U"; // Your actual HashSecret
        $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
        $vnp_ReturnUrl = "http://localhost:8000/api/payment/NCB-return1"; // URL to handle payment response

        $vnp_TxnRef = $registerMember->id . '_' . time();

        $vnp_OrderInfo = "Thanh toán register member ID: " . $registerMember->id;
        $vnp_OrderType = "billpayment";
        $vnp_Amount = intval($money * 100); // Convert to the correct unit (Vietnamese đồng)
        $vnp_Locale = "vn";
        $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];
        $vnp_BankCode = "NCB"; // Example bank code

        // Prepare data to send to VNPAY
        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_ReturnUrl,
            "vnp_TxnRef" => $vnp_TxnRef,
            "vnp_BankCode" => $vnp_BankCode // Bank code
        );

        // Sort parameters and create query string
        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        // Generate hash and append to the URL
        $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
        $vnp_Url = $vnp_Url . "?" . $query . 'vnp_SecureHash=' . $vnpSecureHash;

        // Save payment details to database
        $payment->ma_thanh_toan = $vnp_TxnRef;
        $payment->registermember_id = $registerMember->id; // Changed booking_id to registermember_id
        $payment->chi_tiet_giao_dich = json_encode($inputData);
        $payment->save();

        return response()->json([
            'message' => 'Redirecting to VNPAY payment page',
            'url' => $vnp_Url,
        ]);
    }


    public function NCBReturn1(Request $request)
    {
        $vnp_HashSecret = "TTUJCPICUHRHA8PY7LLIQSCZU9Q7ND8U";

        // Lấy dữ liệu đầu vào từ request
        $inputData = $request->all();
        $vnp_SecureHash = $inputData['vnp_SecureHash'] ?? '';

        // Kiểm tra vnp_SecureHash có tồn tại hay không
        if (!$vnp_SecureHash) {
            return response()->json(['message' => 'Thiếu dữ liệu vnp_SecureHash'], 400);
        }

        // Loại bỏ vnp_SecureHash để tính toán lại hash
        unset($inputData['vnp_SecureHash']);
        ksort($inputData); // Sắp xếp dữ liệu theo key
        $hashData = http_build_query($inputData, '', '&');

        // Tính toán hash để xác thực
        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        // Xác thực hash và kiểm tra mã phản hồi
        if ($secureHash === $vnp_SecureHash) {
            if ($inputData['vnp_ResponseCode'] == '00') {
                // Tìm bản ghi thanh toán
                $payment = Payment::where('ma_thanh_toan', $inputData['vnp_TxnRef'])->first();
                if ($payment) {
                    $payment->trang_thai = 'Đã hoàn thành'; // Cập nhật trạng thái thanh toán
                    $payment->save();
                } else {
                    return response()->json(['message' => 'Không tìm thấy thông tin thanh toán'], 404);
                }

                // Tìm bản ghi đăng ký hội viên
                $registerMember = RegisterMember::find($inputData['vnp_TxnRef']);
                if ($registerMember) {
                    $registerMember->trang_thai = 2; // Cập nhật trạng thái đăng ký
                    $registerMember->save();

                    // Kiểm tra hoặc cập nhật bảng memberships
                    $membership = MemberShips::where('dangkyhoivien_id', $registerMember->id)->first();

                    if ($membership) {
                        $membership->ngay_dang_ky = $registerMember->ngay_dang_ky;
                        $membership->ngay_het_han = $registerMember->ngay_het_han;
                        $membership->save();

                        $message = "Membership với ID {$membership->id} đã được cập nhật.";
                    } else {
                        $newMembership = MemberShips::create([
                            'dangkyhoivien_id' => $registerMember->id,
                            'so_the' => 'CARD' . str_pad($registerMember->id, 6, '0', STR_PAD_LEFT),
                            'ngay_dang_ky' => $registerMember->ngay_dang_ky,
                            'ngay_het_han' => $registerMember->ngay_het_han,
                        ]);

                        $message = "Membership mới được tạo với ID {$newMembership->id}.";
                    }

                    return response()->json(['message' => $message]);
                } else {
                    return response()->json(['message' => 'Không tìm thấy thông tin đăng ký hội viên'], 404);
                }
            } else {
                // Thanh toán thất bại
                return response()->json([
                    'message' => 'Thanh toán thất bại',
                    'error_code' => $inputData['vnp_ResponseCode'],
                    'error_message' => $this->getVnpayErrorMessage($inputData['vnp_ResponseCode'])
                ], 400);
            }
        } else {
            return response()->json(['message' => 'Xác thực secure hash thất bại'], 400);
        }
    }
    public function paymentMasterCard1($registerMember, $money, $payment)
    {
        // Cấu hình của VNPAY
        $vnp_TmnCode = "0749VTZ7"; // Mã TmnCode thực tế
        $vnp_HashSecret = "TTUJCPICUHRHA8PY7LLIQSCZU9Q7ND8U"; // Mã HashSecret thực tế
        $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
        $vnp_ReturnUrl = "http://localhost:8000/api/payment/mastercard-return"; // URL xử lý sau khi thanh toán

        $vnp_TxnRef = $registerMember->id . '_' . time();
        $vnp_OrderInfo = "Thanh toán register member ID: " . $registerMember->id;
        $vnp_OrderType = "billpayment";
        $vnp_Amount = intval($money * 100);
        $vnp_Locale = "vn";
        $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];
        $vnp_BankCode = "MasterCard"; // Ngân hàng MasterCard

        // Dữ liệu cần gửi
        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_ReturnUrl,
            "vnp_TxnRef" => $vnp_TxnRef,
            "vnp_BankCode" => $vnp_BankCode
        );

        // Sắp xếp và tạo hash
        ksort($inputData);
        $hashData = urldecode(http_build_query($inputData));
        $vnpSecureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        // Tạo URL thanh toán
        $vnp_Url .= "?" . http_build_query($inputData) . "&vnp_SecureHash=" . $vnpSecureHash;

        // Lưu thông tin thanh toán
        $payment->ma_thanh_toan = $vnp_TxnRef;
        $payment->registermember_id = $registerMember->id;
        $payment->chi_tiet_giao_dich = json_encode($inputData);
        $payment->save();

        return response()->json([
            'message' => 'Redirecting to MasterCard payment page',
            'url' => $vnp_Url,
        ]);
    }
    public function mastercardReturn1(Request $request)
    {
        $vnp_HashSecret = "TTUJCPICUHRHA8PY7LLIQSCZU9Q7ND8U";

        // Lấy tất cả dữ liệu từ request
        $inputData = $request->all();
        $vnp_SecureHash = $inputData['vnp_SecureHash'] ?? '';

        if (!$vnp_SecureHash) {
            return response()->json(['message' => 'Thiếu dữ liệu vnp_SecureHash'], 400);
        }

        unset($inputData['vnp_SecureHash']);
        ksort($inputData);
        $hashData = http_build_query($inputData, '', '&');

        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        if ($secureHash === $vnp_SecureHash) {
            if ($inputData['vnp_ResponseCode'] == '00') {
                $payment = Payment::where('ma_thanh_toan', $inputData['vnp_TxnRef'])->first();
                if ($payment) {
                    $payment->trang_thai = 'Đã hoàn thành';
                    $payment->save();
                }

                $registerMember = RegisterMember::find($payment->registermember_id);
                if ($registerMember) {
                    $registerMember->trang_thai = 2;
                    $registerMember->save();
                }

                return response()->json(['message' => 'Thanh toán thành công']);
            } else {
                return response()->json(['message' => 'Thanh toán thất bại'], 400);
            }
        } else {
            return response()->json(['message' => 'Xác thực chữ ký thất bại'], 400);
        }
    }
    public function paymentVISA1($registerMember, $money, $payment)
    {
        // Cấu hình của VNPAY
        $vnp_TmnCode = "0749VTZ7"; // Mã TmnCode thực tế của bạn
        $vnp_HashSecret = "TTUJCPICUHRHA8PY7LLIQSCZU9Q7ND8U"; // Mã HashSecret thực tế của bạn
        $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
        $vnp_ReturnUrl = "http://localhost:8000/api/payment/Visa-return"; // URL xử lý sau khi thanh toán

        $vnp_TxnRef = $registerMember->id . '_' . time(); // Mã giao dịch
        $vnp_OrderInfo = "Thanh toán register member ID: " . $registerMember->id;
        $vnp_OrderType = "billpayment";
        $vnp_Amount = intval($money * 100); // Đơn vị tính là đồng, nhân 100 để đúng định dạng
        $vnp_Locale = "vn";
        $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];
        $vnp_BankCode = "VISA"; // Mã ngân hàng demo để chuyển đến giao diện nhập thẻ

        // Dữ liệu cần gửi cho VNPAY
        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_ReturnUrl,
            "vnp_TxnRef" => $vnp_TxnRef,
            "vnp_BankCode" => $vnp_BankCode
        );

        // Sắp xếp các tham số và tạo chuỗi query
        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        // Tạo hash và thêm vào URL
        $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
        $vnp_Url = $vnp_Url . "?" . $query . 'vnp_SecureHash=' . $vnpSecureHash;

        // Lưu thông tin thanh toán vào cơ sở dữ liệu
        $payment->ma_thanh_toan = $vnp_TxnRef;
        $payment->registermember_id = $registerMember->id;
        $payment->chi_tiet_giao_dich = json_encode($inputData);
        $payment->save();

        return response()->json([
            'message' => 'Chuyển hướng đến trang thanh toán Visa',
            'url' => $vnp_Url,
        ]);
    }
    public function visaReturn1(Request $request)
    {
        $vnp_HashSecret = "TTUJCPICUHRHA8PY7LLIQSCZU9Q7ND8U";

        // Lấy tất cả dữ liệu từ request
        $inputData = $request->all();
        $vnp_SecureHash = $inputData['vnp_SecureHash'] ?? '';

        // Kiểm tra nếu `vnp_SecureHash` không có trong request
        if (!$vnp_SecureHash) {
            return response()->json(['message' => 'Thiếu dữ liệu vnp_SecureHash'], 400);
        }

        // Xóa khóa `vnp_SecureHash` khỏi dữ liệu để tính toán hash
        unset($inputData['vnp_SecureHash']);

        // Sắp xếp lại mảng dữ liệu theo thứ tự tăng dần của khóa
        ksort($inputData);

        // Tạo chuỗi dữ liệu để hash
        $hashData = http_build_query($inputData, '', '&');

        // Tính toán SecureHash từ chuỗi dữ liệu và khóa bí mật
        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        // Kiểm tra SecureHash có khớp không và mã phản hồi từ VNPAY
        if ($secureHash === $vnp_SecureHash) {
            if ($inputData['vnp_ResponseCode'] == '00') {
                // Tìm bản ghi thanh toán dựa trên mã thanh toán
                $payment = Payment::where('ma_thanh_toan', $inputData['vnp_TxnRef'])->first();

                if ($payment) {
                    $payment->trang_thai = 'Đã hoàn thành'; // Cập nhật trạng thái thanh toán
                    $payment->save();

                    // Tìm bản ghi đăng ký hội viên
                    $registerMember = RegisterMember::find(explode('_', $inputData['vnp_TxnRef'])[0]);

                    if ($registerMember) {
                        $registerMember->trang_thai = 2; // Cập nhật trạng thái đăng ký
                        $registerMember->save();
                        return response()->json(['message' => 'Thanh toán thành công']);
                    }
                    return response()->json(['message' => 'Không tìm thấy thông tin đăng ký hội viên'], 404);
                }
                return response()->json(['message' => 'Không tìm thấy thông tin thanh toán'], 404);
            } else {
                return response()->json([
                    'message' => 'Thanh toán thất bại',
                    'error_code' => $inputData['vnp_ResponseCode'],
                    'error_message' => $this->getVnpayErrorMessage($inputData['vnp_ResponseCode']),
                ], 400);
            }
        } else {
            return response()->json(['message' => 'Xác thực chữ ký thất bại'], 400);
        }
    }
}
