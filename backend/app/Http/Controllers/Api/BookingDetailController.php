<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BookingDetailController extends Controller
{

    // show tất cả các đơn đã mua theo userid đó ở client
    public function bookingDetail(Request $request)
    {

        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'message' => 'Vui lòng đăng nhập',
            ], 401);
        }

        //dd($user);

        $bookDetails = DB::table('booking_details')->join('bookings', 'booking_details.booking_id', '=', 'bookings.id')
            ->join('users', 'bookings.user_id', '=', 'users.id')->join('payments', 'booking_details.payment_id', '=', 'payments.id')->join('showtimes', 'bookings.thongtinchieu_id', '=', 'showtimes.id')
            ->join('rooms', 'showtimes.room_id', '=', 'rooms.id')->join('movies', 'showtimes.phim_id', '=', 'movies.id')->where('bookings.user_id', $user->id)
            ->select(
                'booking_details.id',
                'users.ho_ten',
                'users.email',
                'users.so_dien_thoai',
                'bookings.ngay_mua',
                'bookings.so_luong',
                'movies.ten_phim',
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
                'booking_details.trang_thai'

            )->get();

        if ($bookDetails->isEmpty()) {
            return response()->json([
                'message' => 'Bạn chưa có đơn booking vé phim nào',
            ], 404);
        }

        return response()->json([
            'message' => 'Booking detail all theo user login',
            'data' => $bookDetails
        ], 200);
    }


    // show bookticket theo id đó


    // tải bill về theo id booking_detail đó
    public function exportBill() {}



    // đổ all booking detail trong admin
    public function bookingDetailAll(Request $request)
    {

        $bookDetails = DB::table('booking_details')->join('bookings', 'booking_details.booking_id', '=', 'bookings.id')
            ->join('users', 'bookings.user_id', '=', 'users.id')->join('payments', 'booking_details.payment_id', '=', 'payments.id')->join('showtimes', 'bookings.thongtinchieu_id', '=', 'showtimes.id')
            ->join('rooms', 'showtimes.room_id', '=', 'rooms.id')->join('movies', 'showtimes.phim_id', '=', 'movies.id')
            ->select(
                'booking_details.id',
                'users.ho_ten',
                'users.email',
                'users.so_dien_thoai',
                'bookings.ngay_mua',
                'bookings.so_luong',
                'movies.ten_phim',
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
                'booking_details.trang_thai', // trạng thái booking_detail 0 là chưa đến 1 là nhân viên xác nhận đã đến
                'booking_details.barcode'

            )->get();

        if ($bookDetails->isEmpty()) {
            return response()->json([
                'message' => 'Không có đơn Booking Detail của khách nào !',
            ], 404);
        }

        return response()->json([
            'message' => 'Lấy Booking Detail của khách hàng thành công',
            'data' => $bookDetails
        ], 200);
    }

    // tìm kiếm đơn theo tên khách hàng or email , số id đơn booking , ngày booking
    // gộp chung khi nhập input tìm kiểm đổ ra kết quả
    public function searchBookingDetail(Request $request, $search)
    {

        $results = DB::table('booking_details')->join('bookings', 'booking_details.booking_id', '=', 'bookings.id')
            ->join('users', 'bookings.user_id', '=', 'users.id')->join('payments', 'booking_details.payment_id', '=', 'payments.id')->join('showtimes', 'bookings.thongtinchieu_id', '=', 'showtimes.id') // Thêm JOIN với bảng showtime
            ->join('rooms', 'showtimes.room_id', '=', 'rooms.id')->join('movies', 'showtimes.phim_id', '=', 'movies.id')->select(
                'booking_details.id',
                'users.ho_ten',
                'users.email',
                'users.so_dien_thoai',
                'bookings.ngay_mua',
                'bookings.so_luong',
                'movies.ten_phim',
                'showtimes.ngay_chieu',
                'showtimes.gio_chieu',
                'rooms.ten_phong_chieu',
                'bookings.ghe_ngoi',
                'bookings.do_an',
                'bookings.ma_giam_gia',
                'bookings.ghi_chu',
                'bookings.tong_tien_thanh_toan',
                'bookings.barcode',
                'payments.phuong_thuc_thanh_toan',
                'payments.ngay_thanh_toan',
                'payments.trang_thai',
                //'bookings.tong_tien',
            )->where('users.ho_ten', 'LIKE', "%{$search}%")
            ->orWhere('users.email', 'LIKE', "%{$search}%")
            ->orWhere('users.so_dien_thoai', 'LIKE', "%{$search}%")
            ->orWhere('booking_details.barcode', 'LIKE', "%{$search}%")
            ->orWhereDate('bookings.ngay_mua', $search)
            ->get();


        if ($results !== $search) {
            return response()->json(['message' => 'Không tìm thấy đơn booking nào theo của dữ liệu này ! '], 404);
        }

        return response()->json([
            'message' => 'Kết quả tìm kiếm:',
            'data' => $results
        ], 200);
    }

    // nhập barcode theo booking detail in vé vào room và update trang_thai = 1

    public function confirmArrival(Request $request, string $id)
    {

        $dataID = BookingDetail::find($id);
        if (!$dataID) {
            return response()->json([
                'message' => 'Không có dữ liệu  theo id này',
            ], 404);
        }

        $dataID->update(['trang_thai' => 1]);

        // trả về 
        return response()->json([
            'message' => 'Check vé ok',
            'data' => $dataID
        ], 200);
    }
}
