<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Food;
use App\Models\Seat;
use App\Models\Showtime;
use App\Models\Voucher;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class BookingController extends Controller
{


    public function index() {}


    // public function userBooking(Request $request)
    // {


    //     // lấy user_id khi đã login
    //     //$userID = Auth()->id();

    //     // Kiểm tra nếu user chưa đăng nhập
    //     // if (!$userID) {
    //     //     return response()->json([
    //     //         'message' => 'Người dùng chưa đăng nhập.'
    //     //     ], 401); // Trả về mã lỗi 401 nếu người dùng chưa đăng nhập
    //     // }

    //     // check khi chọn all booking
    //     $request->validate([
    //         'user_id' => 'required|exists:users,id', // fix 
    //         'thongtinchieu_id' => 'required|exists:showtimes,id',
    //         'ghi_chu' => 'nullable|string|max:255',
    //         'ma_giam_gia' => 'nullable|string|max:255',
    //         'doan_id' => 'nullable|exists:foods,id',
    //         'ghe_ngoi' => 'required|array',
    //         'ghe_ngoi.*' => 'required|exists:seats,id',
    //         'so_luong_do_an' => 'nullable|numeric|min:1',
    //     ]);

    //     /**
    //      * 
    //      * B1 vào chi tiết phim 
    //      * B2 Chọn Showtime mong muốn
    //      * B3 Chọn showtime xong hiện giao diện ghế ngồi để chọn ghế
    //      * B4 chọn đồ ăn nếu có chọn
    //      * B5 điền mã giảm giá ấn áp mã để giảm giá nếu có
    //      * B6 ấn thanh toán
    //      * 
    //      * **/

    //     // lấy phim từ xuất chiếu khi booking
    //     $showtime = Showtime::with('movie')->find($request->thongtinchieu_id);
    //     if (!$showtime) {
    //         return response()->json([
    //             'message' => 'Suất chiếu không tồn tại theo phim thêm xuất chiếu cho phim này'
    //         ], 404);
    //     }

    //     // lấy giá phim
    //     $gia_phim = $showtime->movie->gia_ve;
    //     // Lấy tên ghế ngồi từ ID đã chọn và tính giá ghế
    //     $selectedSeats = $request->ghe_ngoi;
    //     $seatNames = []; // Mảng chứa tên ghế ngồi
    //     $tong_tien_ghes = 0; // Khởi tạo tổng tiền ghế ngồi


    //     foreach ($selectedSeats as $seatID) {
    //         $seat = Seat::find($seatID);
    //         if ($seat) {
    //             $seatNames[] = $seat->so_ghe_ngoi; // Lưu tên ghế vào mảng
    //             $tong_tien_ghes += $seat->gia_ghe; // Cộng dồn giá ghế vào tổng tiền ghế ngồi
    //         }
    //     }

    //     // Chuyển đổi mảng tên ghế ngồi thành chuỗi (nếu cần)
    //     $ghe_ngoi_str = implode(', ', $seatNames);


    //     // chọn ghế tình tiền theo ghế các ghế đã chọn
    //     // lấy dánh sách ghế ngồi đã chọn theo id
    //     $selectedSeats = $request->ghe_ngoi;

    //     $so_luong_ve  = count($selectedSeats);

    //     // Sắp xếp danh sách ghế để kiểm tra liền kề
    //     sort($selectedSeats);
    //     // kiểm tra tính tiền kề của các ghế : bắt buộc chọn ghế phải liền kề
    //     for ($i = 0; $i < count($selectedSeats) - 1; $i++) {
    //         $currentSeat = Seat::find($selectedSeats[$i]);
    //         $nextSeat = Seat::find($selectedSeats[$i + 1]);

    //         if ($currentSeat->row != $nextSeat->row || $nextSeat->seat_number != $currentSeat->seat_number + 1) {
    //             return response()->json([
    //                 'message' => 'Các ghế đã chọn phải liền kề nhau trong cùng một hàng.'
    //             ], 400);
    //         }
    //     }

    //     // tính tổng tiền theo các ghế lựa chọn và giá phim
    //     $tong_tien_ve = 0;
    //     foreach ($selectedSeats as $seatID) {
    //         $seat = Seat::find($seatID);
    //         $tong_tien_ve += $seat->gia_ghe; // lấy giá tiền của ghế
    //     }

    //     // tính tổng tiền cho vé phim
    //     $tong_tien_ve_phim = $gia_phim * $so_luong_ve; // giá vé phim nhân với số lượng ghế

    //     // tính tổng tiền
    //     $tong_tien = $tong_tien_ve_phim + $gia_do_an + $tong_tien_ve; // tổng tiền vé + tiền đồ ăn + tiền ghế

    //     // tính tổng tiền thanh toán
    //     $tong_tien_thanh_toan = $tong_tien;


    //     // tạo thêm mới booking
    //     $booking = Booking::create([
    //         //'user_id' => $userID,
    //         'user_id' => $request->user_id,
    //         'thongtinchieu_id' => $request->thongtinchieu_id,
    //         'so_luong' =>  $so_luong_ve,
    //         'ghi_chu' => $request->ghi_chu,
    //         'ma_giam_gia' => $request->ma_giam_gia,
    //         'doan_id' => $request->doan_id,
    //         'tong_tien' => $tong_tien,
    //         'tong_tien_thanh_toan' => $tong_tien_thanh_toan,
    //         'ngay_mua' => Carbon::now(), // ngay mua theo ngay hien tai ko can them
    //         'trang_thai' => 0, // mac dinh 0 vi chua thanh toan
    //         'ghe_ngoi' => json_encode($selectedSeats) // Lưu danh sách ghế đã chọn
    //     ]);

    //     return response()->json([
    //         'message' => 'Tạo booking thành công , den trang thanh toan payment!',
    //         'data' => $booking
    //     ], 201);
    // }


    public function storeBooking(Request $request)
    {

        $user = auth()->user();
        // if (!$user) {
        //     return response()->json([
        //         'message' => 'chua dn'
        //     ], 404);
        // }

        // xác thực dữ liệu đầu vào
        $request->validate([
            // 'user_id' => 'required|exists:users,id',

            'thongtinchieu_id' => 'required|exists:showtimes,id',
            'ma_giam_gia' => 'nullable|string|max:255',
            'doan_id' => 'nullable|exists:foods,id',
            'ghe_ngoi' => 'required|array',
            'ghe_ngoi.*' => 'required|exists:seats,id',
            'so_luong_do_an' => 'nullable|numeric|min:1',

        ]);

        // Lấy thông tin suất chiếu
        $showtime = Showtime::with('movie')->find($request->thongtinchieu_id);
        if (!$showtime) {
            return response()->json(['message' => 'Suất chiếu không tồn tại chọn xuất chiếu.'], 404);
        }

        // lấy các ghế ngồi đã chọn
        $selectedSeats = $request->ghe_ngoi;

        $seatNames = [];
        foreach ($selectedSeats as $seatId) {
            $seat = Seat::find($seatId);
            if ($seat) {
                $seatNames[] = $seat->so_ghe_ngoi;
            }
        }

        // sắp xếp các ghế ngồi đã chọn liền kề nhau
        sort($selectedSeats);

        // kiểm tra tính liền kề
        $checkSeats = true;
        for ($i = 0; $i < count($selectedSeats) - 1; $i++) {

            // check kiểm tra chọn ghế phải mỗi lần chọn phải tăng lên 1 
            // Kiểm tra nếu ghế hiện tại không liền kề với ghế tiếp theo
            if ($selectedSeats[$i] + 1 !== $selectedSeats[$i + 1]) {
                // Kiểm tra nếu không có ghế giữa hai ghế này
                if (!in_array($selectedSeats[$i] + 1, $selectedSeats)) {
                    $checkSeats = false;
                    break;
                }
            }
        }

        if (!$checkSeats) {
            return response()->json(['message' => 'Các ghế đã chọn phải liền kề nhau.'], 400);
        }


        // tính toán tính tiền đồ ăn nếu chọn
        $food = $request->doan_id ? Food::find($request->doan_id) : null;

        // nhập số lượng đồ ăn nếu nhập tính tiền theo số lượng 
        // ko nhập số lượng đồ ăn mặc định là 1 
        $so_luong_do_an = $food ? ($request->so_luong_do_an ?? 1) : 0;
        // tính giá đồ ăn 
        $gia_do_an = $food ? $food->gia * $so_luong_do_an : 0; // nếu k chọn đồ ăn thì là 0
        // Lấy tên đồ ăn (nếu có)
        $ten_do_an = $food ? $food->ten_do_an : null;
        // giá phim lấy theo phim
        $gia_ve_phim = $showtime->movie->gia_ve;

        // tính tổng giá vé dựa trên số ghế đã chọn
        $tong_gia_ve_phim = $gia_ve_phim * count($selectedSeats);

        // tính ghế theo từng ghế đã chọn vào tính tiền
        $tong_tien_ghe = 0;
        foreach ($selectedSeats as $seatID) {
            $seat = Seat::find($seatID);
            if ($seat && isset($seat->gia_ghe)) { // Kiểm tra nếu ghế tồn tại và có giá
                $tong_tien_ghe += $seat->gia_ghe; // Cộng giá của từng ghế vào tổng tiền ghế
            }
        }

        // tính tổng tiền theo : gồm gía vé phim và tiền tổng ghế đã chọn
        $tong_tien_ve_phim = $tong_gia_ve_phim + $tong_tien_ghe;

        // tính tổng tiền theo ve phim và thêm đồ ăn
        $tong_tien = $tong_tien_ve_phim + $gia_do_an;

        // Tạo booking
        $booking = Booking::create([
            'user_id' => $user->id,
            'thongtinchieu_id' => $request->thongtinchieu_id,
            'so_luong' => count($selectedSeats),
            'doan_id' => $request->doan_id,
            'tong_tien' => $tong_tien,
            'tong_tien_thanh_toan' => $tong_tien,
            'ngay_mua' => Carbon::now(),
            'trang_thai' => 0, // chưa thanh toán
            'ghe_ngoi' => implode(', ', $seatNames),
            'so_luong_do_an' => $so_luong_do_an,
            'do_an' => $ten_do_an
        ]);

        return response()->json([
            'message' => 'Tạo booking thành công đến trang thanh toán .',
            'data' => $booking
        ], 201);
    }

    public function showBookingDetails($bookingId)
    {

        $booking = Booking::with([
            'user',
            'bookingDetails.seat',
            'food',
            'showtime.movie',
            'showtime.theater',
            'showtime.room',
            'payment'
        ])->find($bookingId);

        if (!$booking) {
            return response()->json([
                'message' => 'Không tìm thấy booking theo ID này'
            ], 404);
        }

        return response()->json([
            'message' => 'Hiển thị chi tiết booking thành công',
            'data' => $booking
        ], 200);
    }


    public function update(Request $request, string $id)
    {

        // dd($request->all());
        $dataID = Booking::find($id);
        if (!$dataID) {
            return response()->json([
                'message' => 'Không có booking theo id này'
            ], 404);
        }

        // Kiểm tra validate dữ liệu gửi lên
        $request->validate([
            'ghi_chu' => 'nullable|string|max:255',
            'doan_id' => 'nullable|exists:foods,id'
        ]);

        // lấy giá đồ ăn hiện tại hoặc giữ nguyên
        $gia_do_an = 0;

        if ($request->has('doan_id') && $request->doan_id != $dataID->doan_id) {
            // Thay đổi đồ ăn, lấy giá mới
            $food = Food::find($request->doan_id);
            $gia_do_an = $food ? $food->gia : 0;
        } else {
            // Nếu không thay đổi, giữ nguyên giá đồ ăn hiện tại
            $food = Food::find($dataID->doan_id);
            $gia_do_an = $food ? $food->gia : 0;
        }

        // lấy giá phim từ thông tin chiếu
        $showtime = Showtime::with('movie')->find($dataID->thongtinchieu_id);
        if (!$showtime) {
            return response()->json([
                'message' => 'Suất chiếu không tồn tại'
            ], 404);
        }

        // lấy giá phim từ movie liên kết
        $gia_phim = $showtime->movie->gia_ve;

        // tính tổng tiền
        $tong_tien = $gia_phim + $gia_do_an;
        $tong_tien_thanh_toan = $tong_tien;

        $dataID->update([
            'ghi_chu' => $request->ghi_chu,
            'doan_id' => $request->doan_id ?: $dataID->doan_id, //  không có thay đổi đồ ăn thì giữ nguyên doan_id cũ
            'tong_tien' => $tong_tien,
            'tong_tien_thanh_toan' => $tong_tien_thanh_toan,
        ]);

        return response()->json([
            'message' => 'Cập nhật dữ liệu thành công, phai den trang thanh toan payment!',
            'data' => $dataID
        ], 200);
    }


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
