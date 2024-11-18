<?php

namespace App\Http\Controllers\Api;

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
use Endroid\QrCode\Writer\PngWriter;

class PaymentController extends Controller
{


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
            case 'vietqr':
                return $this->paymentVIETQR($booking, $money, $payment);
            case 'viettel_monney':
                return $this->paymentVIETTELMONEY($booking, $money, $payment);
            case 'payoo':
                return $this->paymentPAYOO($booking, $money, $payment);
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
        $vnp_ReturnUrl = "http://localhost:5173/api/payment/NCB-return"; // URL xử lý sau khi thanh toán
        //$vnp_ReturnUrl = "http://localhost:8000/api/payment/NCB-return"; // URL xử lý sau khi thanh toán

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
                    'payment_id' => $payment->id
                ]);


                return response()->json(['message' => 'Thanh toán thành công']);
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
        $vnp_ReturnUrl = "http://localhost:5173/api/payment/Visa-return"; // URL xử lý sau khi thanh toán
        //$vnp_ReturnUrl = "http://localhost:8000/api/payment/Visa-return"; // URL xử lý sau khi thanh toán

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
                    'payment_id' => $payment->id
                ]);


                return response()->json(['message' => 'Thanh toán thành công']);
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
        //$vnp_ReturnUrl = "http://localhost:8000/api/payment/MasterCard-return"; // URL xử lý sau khi thanh toán
        $vnp_ReturnUrl = "http://localhost:5173/api/payment/MasterCard-return"; // URL xử lý sau khi thanh toán

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
                    'payment_id' => $payment->id
                ]);


                return response()->json(['message' => 'Thanh toán thành công']);
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


    private function getVnpayErrorMessage($code)
    {
        $errors = [
            '01' => 'Giao dịch đã tồn tại',
            '02' => 'Merchant không hợp lệ',
            '03' => 'Dữ liệu gửi không đầy đủ',
            '04' => 'Khóa bí mật không hợp lệ',

        ];

        return $errors[$code] ?? 'Lỗi không xác định';
    }

    public function paymentVIETQR($booking, $money, $payment) {}
    public function vietqrReturn(Request $request) {}


    public function paymentVIETTELMONEY($booking, $money, $payment) {}
    public function viettelmoneyReturn(Request $request) {}


    public function paymentPAYOO($booking, $money, $payment) {}
    public function payooReturn(Request $request) {}



    public function processPaymentForRegister(Request $request, RegisterMember $registerMember)
    {
        // Validate phương thức thanh toán
        $request->validate([
            'phuong_thuc_thanh_toan' => 'required|in:credit_card,ncb,paypal,cash,bank_transfer',
        ]);

        // Lấy tổng tiền từ RegisterMember
        $tong_tien = $registerMember->tong_tien;

        // Tạo bản ghi thanh toán mới
        try {
            // Tạo bản ghi thanh toán thành công
            $payment = Payment::create([
                'registermember_id' => $registerMember->id,
                'tong_tien' => $tong_tien,
                'phuong_thuc_thanh_toan' => $request->phuong_thuc_thanh_toan,
                'ma_thanh_toan' => strtoupper(uniqid('PAY_')),
                'ngay_thanh_toan' => Carbon::now(),
                'trang_thai' => 'Đã hoàn thành', // Đã thanh toán
            ]);

            Log::info('Payment created successfully', ['payment' => $payment]);

            // Xử lý thanh toán dựa trên phương thức
            switch ($request->phuong_thuc_thanh_toan) {
                case 'ncb':
                    return $this->paymentNCB1($registerMember, $tong_tien, $payment);
                case 'vietqr':
                    return $this->paymentVIETQR($registerMember, $tong_tien, $payment);
                case 'paypal':
                    return $this->paymentPaypal($registerMember, $tong_tien, $payment);
                case 'credit_card':
                    return $this->paymentCreditCard($registerMember, $tong_tien, $payment);
                default:
                    return response()->json(['error' => 'Phương thức thanh toán không hợp lệ'], 400);
            }

            // Cập nhật trạng thái cho RegisterMember
            $registerMember->update(['trang_thai' => 1]); // Thanh toán thành công

            $membership = MemberShips::create([
                'dangkyhoivien_id' => $registerMember->id,
                'so_the' => strtoupper(uniqid('CARD_')), // Số thẻ unique
                'ngay_dang_ky' => $registerMember->ngay_dang_ky,
                'ngay_het_han' => $registerMember->ngay_het_han, 
            ]);

            return response()->json([
                'message' => 'Thanh toán thành công cho RegisterMember!',
                'register_id' => $registerMember->id,
                'tong_tien_thanh_toan' => $tong_tien,
                'membership' => $membership,
            ], 200);
        } catch (\Exception $e) {
            // Log lỗi nếu xảy ra vấn đề
            Log::error('Payment creation failed', [
                'error' => $e->getMessage(),
                'registermember_id' => $registerMember->id
            ]);

            // Nếu có lỗi xảy ra, tạo bản ghi thanh toán thất bại
            Payment::create([
                'registermember_id' => $registerMember->id,
                'tong_tien' => $tong_tien,
                'phuong_thuc_thanh_toan' => $request->phuong_thuc_thanh_toan,
                'ma_thanh_toan' => strtoupper(uniqid('PAY_FAIL_')), // Đánh dấu là thất bại
                'ngay_thanh_toan' => Carbon::now(),
                'trang_thai' => 'Không thành công',
            ]);

            return response()->json([
                'message' => 'Thanh toán không thành công!',
                'register_id' => $registerMember->id,
                'tong_tien_thanh_toan' => $tong_tien,
            ], 500); // Trả về mã lỗi 500 cho trường hợp lỗi hệ thống
        }
    }

    public function paymentNCB1($registerMember, $money, $payment)
    {
        // Cấu hình của NCB và VNPAY
        $vnp_TmnCode = "0749VTZ7"; // Mã TmnCode thực tế của bạn
        $vnp_HashSecret = "TTUJCPICUHRHA8PY7LLIQSCZU9Q7ND8U"; // Mã HashSecret thực tế của bạn
        $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
        $vnp_ReturnUrl = "http://localhost:8000/api/payment/NCB-return1"; // URL xử lý sau thanh toán

        // Thông tin đơn hàng
        $vnp_TxnRef = $registerMember->id; // Mã đơn hàng (mã ID của RegisterMember)
        $vnp_OrderInfo = "Thanh toán cho RegisterMember ID: " . $registerMember->id;
        $vnp_OrderType = "billpayment";
        $vnp_Amount = intval($money * 100); // Đơn vị tiền tệ là đồng (VND)
        $vnp_Locale = "vn"; // Ngôn ngữ hiển thị là Tiếng Việt
        $vnp_IpAddr = $_SERVER['REMOTE_ADDR']; // Địa chỉ IP của người dùng
        $vnp_BankCode = "NCB"; // Mã ngân hàng (ví dụ: NCB)

        // Dữ liệu cần gửi cho VNPAY
        $inputData = [
            "vnp_Version" => "2.1.0",          // Phiên bản API của VNPAY
            "vnp_TmnCode" => $vnp_TmnCode,     // Mã TmnCode của bạn
            "vnp_Amount" => $vnp_Amount,       // Số tiền thanh toán (đơn vị là đồng)
            "vnp_Command" => "pay",            // Lệnh thanh toán
            "vnp_CreateDate" => date('YmdHis'), // Thời gian tạo giao dịch
            "vnp_CurrCode" => "VND",           // Mã đơn vị tiền tệ (VND)
            "vnp_IpAddr" => $vnp_IpAddr,       // Địa chỉ IP của khách hàng
            "vnp_Locale" => $vnp_Locale,       // Ngôn ngữ của giao diện
            "vnp_OrderInfo" => $vnp_OrderInfo, // Thông tin đơn hàng
            "vnp_OrderType" => $vnp_OrderType, // Loại giao dịch
            "vnp_ReturnUrl" => $vnp_ReturnUrl, // URL trả về sau khi thanh toán
            "vnp_TxnRef" => $vnp_TxnRef,       // Mã giao dịch (ID của RegisterMember)
            "vnp_BankCode" => $vnp_BankCode    // Mã ngân hàng (NCB)
        ];

        // Sắp xếp các tham số theo thứ tự bảng chữ cái
        ksort($inputData);

        // Tạo chuỗi query (dữ liệu gửi đi) mà không có "&" dư thừa
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            $hashdata .= urlencode($key) . "=" . urlencode($value) . "&";
        }

        // Loại bỏ dấu "&" cuối cùng khỏi chuỗi hashdata
        $hashdata = rtrim($hashdata, "&");

        // Tạo hash từ chuỗi đã sắp xếp và thêm vào URL
        $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
        $vnp_Url = $vnp_Url . "?" . $hashdata . '&vnp_SecureHash=' . $vnpSecureHash;

        // Cập nhật thông tin thanh toán vào cơ sở dữ liệu
        $payment->ma_thanh_toan = $vnp_TxnRef;
        $payment->registermember_id = $registerMember->id;
        $payment->chi_tiet_giao_dich = json_encode($inputData); // Lưu lại thông tin giao dịch
        $payment->save();

        // Trả về URL thanh toán cho VNPAY
        return response()->json([
            'message' => 'Chuyển hướng đến trang thanh toán VNPAY',
            'url' => $vnp_Url, // URL thanh toán
        ]);
    }
    public function paymentReturn1(Request $request)
    {
        // Kiểm tra thông tin trả về từ VNPAY
        $vnp_SecureHash = $request->input('vnp_SecureHash');
        $vnp_TmnCode = "0749VTZ7"; // Mã TmnCode của bạn
        $vnp_HashSecret = "TTUJCPICUHRHA8PY7LLIQSCZU9Q7ND8U"; // Mã HashSecret của bạn

        // Lấy các tham số trả về từ VNPAY
        $inputData = $request->all();
        unset($inputData['vnp_SecureHash']); // Loại bỏ tham số SecureHash khỏi danh sách dữ liệu

        // Sắp xếp các tham số theo thứ tự bảng chữ cái
        ksort($inputData);

        // Tạo chuỗi hash từ các tham số đã sắp xếp
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            $hashdata .= urlencode($key) . "=" . urlencode($value) . "&";
        }
        $hashdata = rtrim($hashdata, "&");

        // Tạo lại giá trị SecureHash từ chuỗi hash
        $vnp_SecureHashCheck = hash_hmac('sha512', $hashdata, $vnp_HashSecret);

        // Kiểm tra xem SecureHash trả về có hợp lệ không
        if ($vnp_SecureHash === $vnp_SecureHashCheck) {
            // Kiểm tra trạng thái thanh toán
            $vnp_ResponseCode = $request->input('vnp_ResponseCode');

            if ($vnp_ResponseCode == '00') { // Nếu thanh toán thành công
                $registerMember = RegisterMember::find($request->input('vnp_TxnRef'));
                if ($registerMember) {
                    // Cập nhật trạng thái thanh toán của RegisterMember
                    $registerMember->trang_thai = 1; // Đã thanh toán
                    $registerMember->save();

                    // Cập nhật thông tin thanh toán
                    $payment = Payment::where('registermember_id', $registerMember->id)
                        ->where('ma_thanh_toan', $request->input('vnp_TxnRef'))
                        ->first();

                    if ($payment) {
                        $payment->trang_thai = 'Đã hoàn thành'; // Đã thanh toán thành công
                        $payment->save();
                    }

                    $membership = new MemberShips();
                    $membership->dangkyhoivien_id = $registerMember->id; // Lấy ID từ RegisterMember
                    $membership->so_the = 'CARD' . str_pad($registerMember->id, 6, '0', STR_PAD_LEFT); // Tạo số thẻ, ví dụ: MEM000037
                    $membership->ngay_dang_ky = $registerMember->ngay_dang_ky; // Lấy ngày đăng ký từ RegisterMember
                    $membership->ngay_het_han = $registerMember->ngay_het_han; // Lấy ngày hết hạn từ RegisterMember
                    $membership->save();
    
                    return response()->json([
                        'message' => 'Thanh toán và đăng ký thành công!',
                        'register_id' => $registerMember->id,
                        'tong_tien' => $registerMember->tong_tien,
                        'membership_id' => $membership->id, // ID của bản ghi Membership mới tạo
                        'so_the' => $membership->so_the // Số thẻ người dùng
                    ]);
                }
            } else { // Nếu thanh toán thất bại
                return response()->json(['message' => 'Thanh toán thất bại, vui lòng thử lại sau.'], 500);
            }
        } else {
            // Nếu hash không hợp lệ
            return response()->json(['message' => 'Dữ liệu trả về không hợp lệ.'], 400);
        }
    }
    public function paymentCallback(Request $request)
    {
        // Lấy thông tin callback từ PayPal hoặc phương thức khác
        $paymentStatus = $request->input('payment_status'); // Ví dụ: 'Completed', 'Pending', 'Failed'
        $transactionId = $request->input('txn_id'); // Mã giao dịch từ PayPal hoặc dịch vụ khác
        $registerMemberId = $request->input('register_id'); // ID RegisterMember

        // Kiểm tra xem giao dịch có thành công không
        if ($paymentStatus == 'Completed') {
            // Cập nhật trạng thái thanh toán của RegisterMember
            $registerMember = RegisterMember::find($registerMemberId);
            if ($registerMember) {
                // Đánh dấu trạng thái là đã thanh toán
                $registerMember->trang_thai = 1;
                $registerMember->save();

                // Cập nhật bản ghi thanh toán
                $payment = Payment::where('registermember_id', $registerMember->id)
                    ->where('ma_thanh_toan', $transactionId)
                    ->first();

                if ($payment) {
                    $payment->trang_thai = 1; // Thanh toán thành công
                    $payment->save();
                }

                return response()->json([
                    'message' => 'Thanh toán thành công qua PayPal!',
                    'register_id' => $registerMember->id,
                    'tong_tien' => $registerMember->tong_tien
                ]);
            }
        } else {
            // Nếu thanh toán không thành công
            return response()->json(['message' => 'Thanh toán thất bại qua PayPal hoặc phương thức khác'], 500);
        }
    }
}
