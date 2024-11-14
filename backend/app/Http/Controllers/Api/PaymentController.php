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

        // $validMethods = ['vnpay', 'vietqr', 'viettel_monney', 'payoo'];

        // if (!in_array($method, $validMethods)) {
        //     return response()->json(['error' => 'Phương thức thanh toán không hợp lệ'], 400);
        // }

        switch ($method) {
            case 'vnpay':
                return $this->paymentVNPAY($booking, $money, $payment);
            case 'vietqr':
                return $this->paymentVIETQR($booking, $money, $payment);
            case 'viettel_monney':
                return $this->paymentVIETTELMONEY($booking, $money, $payment);
            case 'payoo':
                return $this->paymentPAYOO($booking, $money, $payment);
            default:
                return response()->json(['error' => 'Phương thức thanh toán không hợp lệ'], 400);
        }
    }

    public function processPaymentForRegister($registerMemberID, $method)
    {
        $registerMember = RegisterMember::find($registerMemberID);
        if (!$registerMember) {
            return response()->json(['message' => 'Không tìm thấy RegisterMember'], 404);
        }

        // Kiểm tra trạng thái thanh toán
        if ($registerMember->trang_thai !== 0) {
            return response()->json(['error' => 'Đã thanh toán hoặc trạng thái không hợp lệ'], 400);
        }

        $money = $registerMember->tong_tien;

        // Bắt đầu transaction để đảm bảo tính nhất quán dữ liệu
        DB::beginTransaction();
        try {
            // Tạo bản ghi thanh toán
            $payment = new Payment();
            $payment->registermember_id = $registerMember->id;
            $payment->tong_tien = $money;
            $payment->phuong_thuc_thanh_toan = $method;
            $payment->trang_thai = 'Đang chờ xử lý';
            $payment->ngay_thanh_toan = Carbon::now();
            $payment->save();

            // Tạo URL thanh toán nếu phương thức là VNPAY
            if ($method === 'vnpay') {
                $paymentResponse = $this->paymentVNPAY1($registerMember, $money, $payment);

                // Kiểm tra và sử dụng URL trả về từ paymentVNPAY1
                if (isset($paymentResponse['url'])) {
                    DB::commit();
                    return response()->json([
                        'message' => 'Chuyển hướng đến trang thanh toán VNPAY',
                        'url' => $paymentResponse['url'], // Sử dụng URL từ response trả về
                    ]);
                }
            }

            // Xử lý sau khi thanh toán thành công
            if ($payment->trang_thai === 'Đã hoàn thành') {
                // Tạo bản ghi Membership sau khi thanh toán thành công
                $ngay_dang_ky = Carbon::now();
                $ngay_het_han = $ngay_dang_ky->copy()->addMonths($registerMember->thoi_gian);
                MemberShips::create([
                    'dangkyhoivien_id' => $registerMember->id,
                    'so_the' => 'MS' . $registerMember->id,
                    'ngay_dang_ky' => $ngay_dang_ky,
                    'ngay_het_han' => $ngay_het_han,
                ]);

                // Cập nhật trạng thái của RegisterMember
                $registerMember->trang_thai = 1; // Đã thanh toán
                $registerMember->save();
            }

            DB::commit();

            return response()->json([
                'message' => 'Thanh toán thành công và tạo Membership',
                'data' => $registerMember
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi khi xử lý thanh toán', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Có lỗi xảy ra khi xử lý thanh toán',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function paymentVNPAY1($registerMember, $money, $payment)
    {
        // Cấu hình của VNPAY
        $vnp_TmnCode = "ZGLC6HIB";
        $vnp_HashSecret = "OS9ZZLFY31UDMY5AFETJNY73VPW8MPYN";
        $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
        $vnp_ReturnUrl = "http://localhost:8000/api/payment/vnpay-return";

        $vnp_TxnRef = $registerMember->id;
        $vnp_OrderInfo = "Thanh toán RegisterMember ID: " . $registerMember->id;
        $vnp_OrderType = "RegisterMember";
        $vnp_Amount = intval($money * 100);
        $vnp_Locale = "VN";
        $vnp_BankCode = "VNBANK";
        $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];

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
        );

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

        $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
        $vnp_Url = $vnp_Url . "?" . $query . 'vnp_SecureHash=' . $vnpSecureHash;

        // Lưu thông tin thanh toán vào cơ sở dữ liệu
        $payment->ma_thanh_toan = $vnp_TxnRef;
        $payment->registermember_id = $registerMember->id; // Sử dụng registermember_id
        $payment->chi_tiet_giao_dich = json_encode($inputData);
        $payment->save();

        return [
            'message' => 'Chuyển hướng đến trang thanh toán VNPAY',
            'url' => $vnp_Url,
        ];
    }










    public function paymentVNPAY($booking, $money, $payment)
    {

        // Cấu hình của VNPAY
        $vnp_TmnCode = "ZGLC6HIB";
        $vnp_HashSecret = "OS9ZZLFY31UDMY5AFETJNY73VPW8MPYN";
        $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
        $vnp_ReturnUrl = "http://localhost:8000/api/payment/vnpay-return";

        $vnp_TxnRef = $booking->id;
        $vnp_OrderInfo = "Thanh toán booking ID: " . $booking->id;
        $vnp_OrderType = "Booking";
        $vnp_Amount = intval($money * 100);
        $vnp_Locale = "VN";
        $vnp_BankCode = "VNBANK";
        $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];

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
        );

        if (isset($vnp_BankCode) && $vnp_BankCode != "") {
            $inputData['vnp_BankCode'] = $vnp_BankCode;
        }

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
            //'payment' => $payment,
            'url' => $vnp_Url,
        ]);
    }

    public function paymentVIETQR($booking, $money, $payment) {}
    public function paymentVIETTELMONEY($booking, $money, $payment) {}
    public function paymentPAYOO($booking, $money, $payment) {}

    // Xử lý callback từ VNPAYs
    public function vnpayReturn(Request $request)
    {
        // Khóa bí mật từ VNPAY
        $vnp_HashSecret = "OS9ZZLFY31UDMY5AFETJNY73VPW8MPYN";
        //$vnp_HashSecret = env('VNPAY_HASH_SECRET');  // Hoặc sử dụng biến môi trường nếu cần

        // Lấy tất cả dữ liệu từ request
        $inputData = $request->all();
        $vnp_SecureHash = $inputData['vnp_SecureHash'];  // Lấy từ request

        // Xóa khóa vnp_SecureHash khỏi input để tính toán hash
        unset($inputData['vnp_SecureHash']);

        // Sắp xếp lại mảng dữ liệu theo thứ tự tăng dần của khóa
        ksort($inputData);

        // Tạo chuỗi dữ liệu để hash
        $hashData = urldecode(http_build_query($inputData, '', '&'));

        // Tính toán SecureHash từ chuỗi dữ liệu và khóa bí mật
        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        // Kiểm tra SecureHash và mã phản hồi từ VNPAY
        if ($secureHash === $vnp_SecureHash && $inputData['vnp_ResponseCode'] == '00') {
            // Cập nhật trạng thái thanh toán thành công

            // Tìm giao dịch thanh toán dựa trên mã thanh toán
            $payment = Payment::where('ma_thanh_toan', $inputData['vnp_TxnRef'])->first();

            if ($payment) {
                // Cập nhật trạng thái thanh toán thành công
                $payment->trang_thai = 'Đã hoàn thành';
                $payment->save();
            }

            // Tìm booking dựa trên mã giao dịch
            $booking = Booking::find($inputData['vnp_TxnRef']);

            if ($booking) {
                // Cập nhật trạng thái booking thành công
                $booking->trang_thai = 2; // 2 có thể là trạng thái "Đã thanh toán" hoặc trạng thái thành công của bạn
                $booking->save();
            }

            // Trả về phản hồi thành công
            return response()->json(['message' => 'Thanh toán thành công']);
        } else {
            // Trả về phản hồi thất bại nếu không khớp SecureHash hoặc mã phản hồi không phải '00'
            return response()->json(['message' => 'Thanh toán thất bại'], 400);
        }
    }
    public function vietqrReturn(Request $request) {}
    public function viettelmoneyReturn(Request $request) {}
    public function payooReturn(Request $request) {}



    // bỏ
    // public function processPaymentBooking(Request $request, $bookingId)
    // {

    //     // Lấy thông tin booking theo id booking khi call go dung id cua bang booking
    //     $bookingId = Booking::findOrFail($bookingId);
    //     if (!$bookingId) {
    //         return response()->json([
    //             'message' => 'Booking id này không tồn tại !'
    //         ], 404);
    //     }

    //     // validate check thanh toan
    //     $request->validate([
    //         'phuong_thuc_thanh_toan' => 'required|in:credit_card,paypal,cash,bank_transfer',
    //     ]);


    //     $tong_tien = $bookingId->tong_tien_thanh_toan;

    //     // tao ban ghi thanh toan
    //     $payment =  Payment::create([
    //         'booking_id' => $bookingId->id,
    //         'tong_tien' => $tong_tien,
    //         'phuong_thuc_thanh_toan' => $request->phuong_thuc_thanh_toan,
    //         'ma_thanh_toan' => strtoupper(uniqid('PAY_')), // giả định thanh toán ok để test tích hơp sau 
    //         'ngay_thanh_toan' => Carbon::now(),
    //         'trang_thai' => 1, //  1 là đã thanh toán
    //     ]);

    //     // cập nhật trạng thái cho booking và booking_detail thanh toán thanhd công full 1 
    //     $bookingId->update(['trang_thai' => 1]); // thanh toán ok

    //     // Thêm thông tin vào booking_details
    //     BookingDetail::create([
    //         'booking_id' => $bookingId->id,
    //         'trang_thai' => 1, // trạng thái đã thanh toán (1)
    //         'thanhtoan_id' => $payment->id, // ID thanh toán vừa tạo
    //     ]);

    //     // Trả về phản hồi sau khi thanh toán thành công
    //     return response()->json([
    //         'message' => 'Thanh toán thành công',
    //         'payment' => $payment
    //     ], 201);
    // }


    // public function processPaymentForRegister(Request $request, RegisterMember $registerMember)
    // {
    //     // Validate phương thức thanh toán
    //     $request->validate([
    //         'phuong_thuc_thanh_toan' => 'required|in:credit_card,paypal,cash,bank_transfer',
    //     ]);

    //     // Lấy tổng tiền từ RegisterMember
    //     $tong_tien = $registerMember->tong_tien;

    //     // Tạo bản ghi thanh toán mới
    //     try {
    //         // Tạo bản ghi thanh toán thành công
    //         $payment = Payment::create([
    //             'registermember_id' => $registerMember->id,
    //             'tong_tien' => $tong_tien,
    //             'phuong_thuc_thanh_toan' => $request->phuong_thuc_thanh_toan,
    //             'ma_thanh_toan' => strtoupper(uniqid('PAY_')),
    //             'ngay_thanh_toan' => Carbon::now(),
    //             'trang_thai' => 1, // Đã thanh toán
    //         ]);

    //         Log::info('Payment created successfully', ['payment' => $payment]);

    //         // Cập nhật trạng thái cho RegisterMember
    //         $registerMember->update(['trang_thai' => 1]); // Thanh toán thành công

    //         return response()->json([
    //             'message' => 'Thanh toán thành công cho RegisterMember!',
    //             'register_id' => $registerMember->id,
    //             'tong_tien_thanh_toan' => $tong_tien,
    //         ], 200);
    //     } catch (\Exception $e) {
    //         Log::error('Payment creation failed', [
    //             'error' => $e->getMessage(),
    //             'registermember_id' => $registerMember->id
    //         ]);

    //         // Nếu có lỗi xảy ra, tạo bản ghi thanh toán thất bại
    //         Payment::create([
    //             'registermember_id' => $registerMember->id,
    //             'tong_tien' => $tong_tien,
    //             'phuong_thuc_thanh_toan' => $request->phuong_thuc_thanh_toan,
    //             'ma_thanh_toan' => strtoupper(uniqid('PAY_FAIL_')), // Đánh dấu là thất bại
    //             'ngay_thanh_toan' => Carbon::now(),
    //             'trang_thai' => 0,
    //         ]);

    //         return response()->json([
    //             'message' => 'Thanh toán không thành công!',
    //             'register_id' => $registerMember->id,
    //             'tong_tien_thanh_toan' => $tong_tien,
    //         ], 500); // Trả về mã lỗi 500 cho trường hợp lỗi hệ thống
    //     }
    // }
}
