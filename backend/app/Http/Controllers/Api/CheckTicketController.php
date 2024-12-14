<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Barryvdh\DomPDF\PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckTicketController extends Controller
{
    // check mã barcode nếu có trả in vé cho khách 
    public function checkBarcodeExportTicket(Request $request)
    {

        // lấy đầu vào mã barcode
        $barcode = $request->input('barcode');

        // kiểm tra mã barcode trong bảng booking_detail
        $checkBarcode = DB::table('booking_details')->join('bookings', 'booking_details.booking_id', '=', 'bookings.id')
            ->join('users', 'bookings.user_id', '=', 'users.id')->join('payments', 'booking_details.payment_id', '=', 'payments.id')->join('showtimes', 'bookings.thongtinchieu_id', '=', 'showtimes.id')
            ->join('rooms', 'showtimes.room_id', '=', 'rooms.id')->join('movies', 'showtimes.phim_id', '=', 'movies.id')
            ->select(
                'booking_details.id',
                'bookings.ngay_mua',
                'bookings.so_luong',
                'movies.ten_phim',
                'showtimes.thoi_luong_chieu',
                'showtimes.ngay_chieu',
                'showtimes.gio_chieu',
                'rooms.ten_phong_chieu',
                'bookings.ghe_ngoi',
                'bookings.do_an',
                'bookings.ma_giam_gia',
                'bookings.ghi_chu',
                'bookings.tong_tien_thanh_toan',
                'payments.tien_te',
                'payments.ngay_thanh_toan',
                'payments.phuong_thuc_thanh_toan',
                'payments.trang_thai',
                'booking_details.trang_thai',

            )->where('booking_details.barcode', $barcode)->first();

        if (!$checkBarcode) {
            return response()->json([
                'message' => 'Không có vé của mã barcode này !',
            ], 404);
        }

        if ($checkBarcode->trang_thai == 1) {
            return response()->json([
                'message' => 'Đơn theo mã barcode này đã nhận vé vào phòng chiếu rồi !',
            ], 409);
        }

        // tạo file pdf 
        $pdf = FacadePdf::loadView('Ticket-Movie.ticket', ['ticket' => $checkBarcode]);

        DB::table('booking_details')->where('barcode', $barcode)->update(['trang_thai' => 1]);

        // trả về file pdf tải về khi ấn tìm kiếm và có đơn đó theo mã barcode
        return response($pdf->output(), 200)->header('Content-Type', 'application/pdf');
    }
}
