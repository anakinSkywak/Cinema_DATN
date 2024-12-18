<?php

namespace App\Http\Controllers\Api;

use App\Events\SeatSelectedEvent;
use App\Events\SeatSelectedEventRealTime;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Food;
use App\Models\Payment;
use App\Models\Seat;
use App\Models\SeatShowtimeStatu;
use App\Models\Showtime;
use App\Models\Voucher;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;

use function PHPUnit\Framework\isEmpty;

class BookingController extends Controller
{

    // 0 Chưa thanh toán , 1 là Đã thanh toán , 2 Đã hủy đơn , 3 Lỗi đơn hàng 

    public function index()
    {

        $booking = Booking::all();

        if ($booking->isEmpty()) {
            return response()->json([
                'message' => 'Không có đơn booking nào'
            ], 404);
        }

        return response()->json([
            'message' => 'All Booking',
            'data' => $booking
        ], 200);
    }


    // hàm  truy vấn ghế đã lấy để thêm tên ghế ngồi vào cột ghe_ngoi
    public function getNameSeat(array $selectedSeats)
    {
        $seatNames = [];
        foreach ($selectedSeats as $seatId) {
            $seat = Seat::find($seatId);
            if ($seat) {
                $seatNames[] = $seat->so_ghe_ngoi;
            }
        }
        return $seatNames;
    }

    // hàm tính tổng tiền với giá phim , đồ ăn , số lượng ghế , giá ghế
    public function tongTien(array $selectedSeats, $foodPrice = 0)
    {
        $tong_tien_ghe = 0;

        foreach ($selectedSeats as $seatId) {
            $PriceseatShowtimeStatus = DB::table('seat_showtime_status')
                ->where('ghengoi_id', $seatId)
                ->select('gia_ghe_showtime')->first();

            if ($PriceseatShowtimeStatus) {
                $tong_tien_ghe = $PriceseatShowtimeStatus->gia_ghe_showtime;
            }
        }
        //dd($tong_tien_ghe);
        $tong_ve_phim = count($selectedSeats);

        return ($tong_tien_ghe * $tong_ve_phim) + $foodPrice;
    }


    // tính tiền coupon 
    public function tinhTienCoupon($ma_giam_gia, $tongtien)
    {

        $now = Carbon::now();
        // truy vấn mã giảm giá user login có điều kiện = 0 , ngày hết hạn lớn hớn ngày hiện tại
        $couponUser = DB::table('coupon_code_takens')
            ->join('countdown_vouchers', 'coupon_code_takens.countdownvoucher_id', '=', 'countdown_vouchers.id')
            ->join('coupons', 'countdown_vouchers.magiamgia_id', '=', 'coupons.id')
            ->where('coupon_code_takens.ngay_het_han', '>', $now)
            ->where('coupons.ma_giam_gia', $ma_giam_gia)
            ->where('coupon_code_takens.trang_thai', 0)
            ->select(
                'coupon_code_takens.id as coupon_takens_id',
                'coupons.ma_giam_gia',
                'coupons.muc_giam_gia',
                'coupons.gia_don_toi_thieu',
                'coupons.Giam_max',
                'coupons.mota',
                'coupon_code_takens.ngay_het_han'
            )->first();

        // check coupon có hợp lệ không 
        if (!$couponUser) {
            return [
                'error' => 'Mã giảm giá không tồn tại hoặc đã hết hạn.',
                'tong_tien_sau_giam' => $tongtien,
            ];
        }

        //dd($couponUser->gia_don_toi_thieu);
        // check gia của booking không = với gia_don_toi_thieu của coupon
        $gia_don_toi_thieu = (float)$couponUser->gia_don_toi_thieu;
        // Check giá trị đơn hàng
        if ($tongtien < $gia_don_toi_thieu) {
            return [
                'error' => "Đơn hàng không đủ giá trị tối thiểu ({$gia_don_toi_thieu}) VND) để áp dụng mã giảm giá!",
                'tong_tien_sau_giam' => $tongtien,
            ];
        }

        // tính giảm giá cho đơn booking
        $muc_giam_gia = $couponUser->muc_giam_gia;
        $giam_gia = ($tongtien * $muc_giam_gia) / 100;

        // áp dụng mã giảm giá tối đa nếu theo của mã giảm gía đó
        if (!empty($couponUser->Giam_max)) {
            $giam_gia = min($giam_gia, $couponUser->Giam_max);
        }

        // tổng tiền sau khi giảm giá
        $tong_tien_sau_giam = max($tongtien - $giam_gia, 0);
        return [
            'tong_tien_sau_giam' => $tong_tien_sau_giam,
            'giam_gia' => $giam_gia,
            'ma_giam_gia' => $couponUser->ma_giam_gia,
        ];
    }


    // hàm realtime chọn ghế chặn realtime và bỏ chặn khi ấn lại ghế

    public function selectSeat(Request $request)
    {

        $user = auth()->user();
        if (!$user) {
            return response()->json(['message' => 'Chưa đăng nhập, vui lòng đăng nhập'], 401);
        }

        $seatId = $request->input('ghengoi_id');
        $showtimeId = $request->input('thongtinchieu_id');

        // Kiểm tra tính hợp lệ của dữ liệu
        if (!$seatId || !$showtimeId) {
            return response()->json(['message' => 'Dữ liệu không hợp lệ'], 422);
        }

        // Kiểm tra nếu ghế đã bị chọn trước đó 
        $existingSeat = SeatShowtimeStatu::where('ghengoi_id', $seatId)
            ->where('thongtinchieu_id', $showtimeId)
            ->first();

        if (!$existingSeat) {
            return response()->json(['message' => 'Ghế không tồn tại'], 404);
        }

        if ($existingSeat->trang_thai == 1) {
            return response()->json([
                'error' => 'Ghế đã được booking bởi khách hàng khác!',
                'data' => [
                    'seatId' => $seatId,
                    'showtimeId' => $showtimeId,
                ],
            ], 409);
        }


        if ($existingSeat->trang_thai == 3) {
            $existingSeat->update([
                'trang_thai' => 0,
                'user_id' => null,
            ]);

            // Phát sự kiện bỏ chọn ghế
            event(new SeatSelectedEvent($seatId,  $showtimeId));

            return response()->json([
                'message' => 'Ghế đã được bỏ chọn thành công',
                'data' => [
                    'seatId' => $seatId,
                    'showtimeId' => $showtimeId,
                ],
            ]);
        }

        // Nếu ghế đã chọn và người dùng muốn bỏ chọn cập nhật thành 0
        if ($existingSeat && $existingSeat->trang_thai == 3) {

            $existingSeat->update(['trang_thai' => 0, 'user_id' => null]); // bỏ chọn 

            //  sự kiện bỏ chọn ghế
            event(new SeatSelectedEvent($seatId, $showtimeId));

            return response()->json([
                'message' => 'Ghế đã được bỏ chọn thành công',
                'data' => $seatId,
            ]);
        }

        // Nếu ghế không ở trạng thái đang chọn (3), chuyển sang trạng thái đang chọn (3)
        $existingSeat->update([
            'trang_thai' => 3,
            'user_id' => $user->id,
        ]);

        // Phát sự kiện chọn ghế
        event(new SeatSelectedEvent($seatId,  $showtimeId));

        return response()->json([
            'message' => 'Ghế đã được chọn thành công',
            'data' => [
                'seatId' => $seatId,
                'showtimeId' => $showtimeId,
                'user_id' => $user->id,
            ],
        ]);
    }


    // Hàm xử lý đặt vé với đồ ăn và tính tiền

    public function Booking(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['message' => 'Chưa đăng nhập, vui lòng đăng nhập'], 401);
        }

        $request->validate([
            'thongtinchieu_id' => 'required|exists:showtimes,id',
            'ghe_ngoi' => 'required|array|min:1',
            'ghe_ngoi.*' => 'required|exists:seats,id',
            'doan' => 'nullable|array',
            'doan.*.doan_id' => 'nullable|exists:foods,id',
            'doan.*.so_luong_do_an' => 'nullable|numeric|min:1',
            'ma_giam_gia' => 'nullable',
            'ghi_chu' => 'nullable|string|max:255',
        ]);

        $now = Carbon::now();
        // truy vấn memberships xem có user_id theo login hay ko giảm giá tiền theo % thẻ đó khi booking
        $member =  DB::table('memberships')
            ->join('register_members', 'memberships.dangkyhoivien_id', 'register_members.id')
            ->join('members', 'register_members.hoivien_id', 'members.id')
            ->where('memberships.user_id', $user->id)
            ->where('memberships.ngay_het_han', '>', $now)
            ->where('memberships.trang_thai', 0)
            ->select( // dữ liệu xem thôi
                'memberships.id as membership_id',
                'members.loai_hoi_vien',
                'members.uu_dai',
                'register_members.ngay_dang_ky',
                'register_members.ngay_het_han',
                'memberships.trang_thai'
            )->first();

        // xử lí tự giảm tiêm khi user login đã đăng ký tành viên
        // nếu thẻ thành viên tồn tại, lấy tỷ lệ giảm giá
        $memberDiscount = 0;
        if ($member && isset($member->uu_dai)) {
            $memberDiscount = (float) $member->uu_dai;
        }

        // lấy tên ghế ngồi
        $selectedSeats = $request->ghe_ngoi;
       
        // số lượng ghế được booking : có thể table số lượng sau
        $selectedSeatMax = count($selectedSeats);
        if ($selectedSeatMax > 8) {
            return response()->json([
                'message' => 'Bạn không thể booking vé phim với lớn hơn 8 ghế 1 lần !',
            ], 400);
        }

        // check khi lưu booking theo thongtinhchieu_id và ghngoi
        // check trang_thai = 1 đặt đặt , 3 đang có người chọn ko cho lưu booking vói id ghế 
        $seatShowTimeStatus = SeatShowtimeStatu::where('thongtinchieu_id', $request->thongtinchieu_id)
            ->whereIn('ghengoi_id', $request->ghe_ngoi)->get();

        // lặp kiểm tra
        // duyệt qua mảng ghế và kiểm tra trạng thái
        $invalidSeats = [];
        foreach ($seatShowTimeStatus as $seatStatus) {
            if ($seatStatus->trang_thai == 1) { // 1 là đã có người đặt không cho đặt nữa
                $invalidSeats[]  = $seatStatus->ghengoi_id;
            }
        }

        if (!empty($invalidSeats)) {
            return response()->json([
                // có thế chuyển hướng về trang front mong muốn 
                'message' => 'Một số ghế đã chọn không thể đặt vì không còn trống nữa !',
                'invalid_seats' => $invalidSeats // các ghế đã có người đặt or chọn rồi
            ], 400);
        }

        $seatNames = $this->getNameSeat($selectedSeats);

        $doAnDetails = [];
        $tongTienDoAn = 0;
        if (!empty($request->doan)) {
            foreach ($request->doan as $doan) {
                $food = Food::find($doan['doan_id']);
                if ($food) {
                    $doAnDetails[] = ['ten_do_an' => $food->ten_do_an, 'so_luong_do_an' => $doan['so_luong_do_an']];
                    $tongTienDoAn += $food->gia * $doan['so_luong_do_an'];
                }
            }
        }
        $doAnString = $this->formatDoAnString($doAnDetails);

        // tính tổng tiền
        $tongTien = $this->tongTien($selectedSeats, $tongTienDoAn);

        if ($memberDiscount > 0) {
            $tongTien -= ($tongTien * $memberDiscount) / 100;
        }

        // kiểm tra mã giảm giá nếu có dùng
        if ($request->ma_giam_gia) {
            $result = $this->tinhTienCoupon($request->ma_giam_gia, $tongTien);
            if (isset($result['error'])) {
                return response()->json($result, 400);
            }
            $tongTien = $result['tong_tien_sau_giam'];

            $coupon = DB::table('coupons')->where('ma_giam_gia', $request->ma_giam_gia)->first();
            if (!$coupon) {
                return response()->json(['message' => 'Mã giảm giá không tồn tại.'], 400);
            }
        } else {
            $coupon = null;
        }

        $barcode = 'VE-' . substr(strval(rand(10000, 999999)), 0, 6);
        $id = rand(10000, 9999999);

        // Tạo Booking
        // 0 Chưa thanh toán , 1 là Đã thanh toán , 2 Đã hủy đơn , 3 Lỗi đơn hàng ,
        $booking = Booking::create([
            'id' => $id,
            'user_id' => $user->id,
            'thongtinchieu_id' => $request->thongtinchieu_id,
            'so_luong' => count($selectedSeats),
            'ngay_mua' => Carbon::now(),
            'trang_thai' => 0,  // Chưa thanh toán 
            'ghe_ngoi' => implode(', ', $seatNames),
            'do_an' => $doAnString,
            'ma_giam_gia' => $coupon ? $coupon->ma_giam_gia : null,
            'ghi_chu' => $request->ghi_chu,
            'tong_tien' => $tongTien,
            'tong_tien_thanh_toan' => $tongTien,
            'barcode' => $barcode, // ma barcode
            'coupon_id' => $coupon ? $coupon->id : null,
        ]);

        // Cập nhật trạng thái ghế trong seat_showtime_status
        foreach ($selectedSeats as $seatId) {
            DB::table('seat_showtime_status')->where('thongtinchieu_id', $request->thongtinchieu_id)->where('ghengoi_id', $seatId)->update(['trang_thai' => 1, 'user_id' => $user->id]);
        }

        return response()->json([
            'message' => 'Tạo Booking thành công, vui lòng thanh toán.',
            'data' => $booking,
            //'doan_details' =>  $doAnDetails // chỉ để xem dữ liệu thôi
        ], 201);
    }



    // Hàm format tên món ăn và số lượng món ăn thành chuỗi
    public function formatDoAnString($doanDetails)
    {
        $doAnList = [];
        foreach ($doanDetails as $doan) {
            if (isset($doan['ten_do_an']) && isset($doan['so_luong_do_an'])) {
                $doAnList[] = $doan['ten_do_an'] . ' (x' . $doan['so_luong_do_an'] . ')';
            }
        }
        return implode(', ', $doAnList);
    }


    // xu ly sau
    public function update(Request $request, string $id)
    {

        // dd($request->all());

    }

    // 
    public function delete(string $id)
    {
        // delete theo id
        $dataID = Booking::find($id);

        if (!$dataID) {
            return response()->json([
                'message' => 'Không có booking theo id này'
            ], 404);
        }

        $dataID->delete();

        return response()->json([
            'message' => 'Xóa thành công booking theo id'
        ], 200);
    }
}
