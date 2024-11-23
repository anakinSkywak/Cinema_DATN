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

    // show các đơn đã mua theo userid đó
    public function bookingDetail(Request $request)
    {

        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'message' => 'Vui lòng đăng nhập',
            ], 401);
        }

        $bookDetails = DB::table('booking_details')
            ->join('bookings', 'booking_details.booking_id', '=', 'bookings.id')
            ->join('payments', 'booking_details.payment_id', '=', 'payments.id')
            ->where('bookings.user_id', $user->id)
            ->select('booking_details.*', 'bookings.*', 'payments.*')
            ->get();

        return response()->json([
            'message' => 'booking detail theo user id',
            'data' => $bookDetails
        ], 200);
    }

    // đổ all booking detail trong admin
    public function bookingDetailAll(Request $request)
    {

        $bookDetailall = DB::table('booking_details')
            ->join('bookings', 'booking_details.booking_id', '=', 'bookings.id')
            ->join('payments', 'booking_details.payment_id', '=', 'payments.id')
            ->select('booking_details.*', 'bookings.*', 'payments.*')
            ->get();

        return response()->json([
            'message' => 'booking detail ở admin',
            'data' => $bookDetailall
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
                'payments.phuong_thuc_thanh_toan',
                'payments.trang_thai',
                //'bookings.tong_tien',
            )->where('users.ho_ten', 'LIKE', "%{$search}%")
            ->orWhere('users.email', 'LIKE', "%{$search}%")
            ->orWhere('users.so_dien_thoai', 'LIKE', "%{$search}%")
            ->orWhereDate('bookings.ngay_mua', $search)
            ->get();

        if ($results->isEmpty()) {
            return response()->json(['message' => 'Không tìm thấy đơn booking nào.'], 404);
        }

        return response()->json([
            'message' => 'Kết quả tìm kiếm:',
            'data' => $results
        ], 200);
    }

    // chưc năng check vé xác nhận khách đến theo id booking_detail
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
