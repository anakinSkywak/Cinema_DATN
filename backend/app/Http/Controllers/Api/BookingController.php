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
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;

use function PHPUnit\Framework\isEmpty;

class BookingController extends Controller
{


    // hàm check ghế chọn theo id phải liền kề nhau khi chọn ghế 
    private function checkSeat(array $selectedSeats)
    {
        // sắp xếp các ghế ngồi đã chọn liền kề nhau
        sort($selectedSeats);

        // kiểm tra tính liền kề
        for ($i = 0; $i < count($selectedSeats) - 1; $i++) {
            // check kiểm tra chọn ghế phải mỗi lần chọn phải tăng lên 1 
            // Kiểm tra nếu ghế hiện tại không liền kề với ghế tiếp theo
            if ($selectedSeats[$i] + 1 !== $selectedSeats[$i + 1]) {
                // Kiểm tra nếu không có ghế giữa hai ghế này
                if (!in_array($selectedSeats[$i] + 1, $selectedSeats)) {
                    return false;
                }
            }
        }

        return true;
    }

    // hàm  truy vấn ghế đã lấy để thêm tên ghế ngồi vào cột ghe_ngoi
    private function getNameSeat(array $selectedSeats)
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

    // hàm lấy đồ ăn theo id
    private function getFood($foodID)
    {
        return $foodID ? Food::find($foodID) : null;
    }

    // hàm lấy số lượng đồ ăn để tính tiền
    private function soLuongFood($food, $requestSoLuong)
    {
        return $food ? ($requestSoLuong ?? 1) : 0;
    }

    // hàm tính tiền đồ ăn với với giá đồ ăn và số lượng thêm đồ ăn nếu có 
    private function tinhTienDoAn($food, $soluong)
    {
        return $food ? $food->gia * $soluong : 0; // truy vấn giá * số lượng nhập ở ô input
    }

    // hàm tính tổng tiền với giá phim , đồ ăn , số lượng ghế , giá ghế
    private function tongTien($showtime, array $selectedSeats, $foodPrice)
    {
        $gia_ve_phim = $showtime->movie->gia_ve;
        $tong_gia_ve_phim = $gia_ve_phim * count($selectedSeats);

        $tong_tien_ghe = 0;
        foreach ($selectedSeats as $seatId) {
            $seat = Seat::find($seatId);
            if ($seat && isset($seat->gia_ghe)) {
                $tong_tien_ghe += $seat->gia_ghe;
            }
        }

        return $tong_gia_ve_phim + $tong_tien_ghe + $foodPrice;
    }


    // hàm sử dụng voucher nếu có sử dụng tính tiền khi sử dụng voucher
    public function tinhTienVoucher($ma_giam_gia, $tong_tien)
    {

        // truy vẫn mã giảm giá theo ma_giam_gia và lấy muc_giam_gia de tinh toan tổng tiền sau khi sử dụng voucher
        //$voucher = Voucher::where('ma_giam_gia', $ma_giam_gia)->where('ngay_het_han', '>=', Carbon::now())->where('so_luong_da_su_dung', '<', DB::raw('so_luong'))->first();

        $voucher = Voucher::where('ma_giam_gia', $ma_giam_gia)
            ->where('ngay_het_han', '>=', Carbon::now())
            ->where('trang_thai', 0) // giả sử 1 là trạng thái còn hiệu lực
            ->whereColumn('so_luong_da_su_dung', '<', 'so_luong')
            ->first();

        // check nếu ko tồn tại voucher tông tien giảm giá vẫn = tong tiền cũ
        if (!$voucher) {

            return [
                'error' => 'Mã giảm giá không hợp lệ , không đúng hoặc đã hết hạn .',
                'tong_tien_sau_giam' => $tong_tien,
            ];
        }

        if ($voucher->so_luong_da_su_dung >= $voucher->so_luong) {
            return [
                'error' => 'mã đã hết.',
                'tong_tien_sau_giam' => $tong_tien,
            ];
        }

        // tính toán mức giảm giá theo % giảm giá
        $muc_giam_gia = $voucher->muc_giam_gia; // truy vấn lấy mức giảm giá

        // giám giá tiền 
        $giam_gia = $tong_tien * ($muc_giam_gia / 100);

        // tổng tiền sau giảm
        $tong_tien_sau_giam = max($tong_tien - $giam_gia, 0); // đảm bảo tiền ko âm

        // cập nhật so_luong_da_su_dung tăng lên mỗi lần sử dụng
        $voucher->increment('so_luong_da_su_dung');

        return [
            // 'message' => 'Áp dụng mã giảm giá thành công.',
            'tong_tien_sau_giam' => $tong_tien_sau_giam,
        ];
    }


    public function storeBooking(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json([
                'message' => 'Chưa đăng nhập phải đăng nhập'
            ], 401);
        }

        // xác thực dữ liệu đầu vào
        $request->validate([
            'thongtinchieu_id' => 'required|exists:showtimes,id',
            'ma_giam_gia' => 'nullable|string|max:255',
            'magiamgia_id' => 'nullable|exists:vouchers,id',
            'doan_id' => 'nullable|exists:foods,id',
            'ghe_ngoi' => 'required|array|min:1',
            'ghe_ngoi.*' => 'required|exists:seats,id',
            'so_luong_do_an' => 'nullable|numeric|min:1',
        ]);

        $showtime = Showtime::with('movie')->find($request->thongtinchieu_id);
        if (!$showtime) {
            return response()->json([
                'message' => 'Suất chiếu không tồn tại.'
            ], 404);
        }

        // Lấy và kiểm tra các ghế ngồi
        $selectedSeats = $request->ghe_ngoi;

        // lấy tên ghế ngồi để lưu vào cột ghe_ngoi
        $seatNames = $this->getNameSeat($selectedSeats);

        // check ghế ngồi
        if (!$this->checkSeat($selectedSeats)) {
            return response()->json(['message' => 'Các ghế đã chọn phải liền kề nhau.'], 400);
        }

        // tính toán tổng tiền
        $food = $this->getFood($request->doan_id);

        // số lượng đồ ăn
        $so_luong_do_an = $this->soLuongFood($food, $request->so_luong_do_an);

        // tính toán giá đồ ăn
        $gia_do_an = $this->tinhTienDoAn($food, $so_luong_do_an);

        // truy vấn thêm tên đồ ăn vào bảng cột do_an
        $ten_do_an = $food ? $food->ten_do_an : null;

        // tính tong tien 
        $tong_tien = $this->tongTien($showtime, $selectedSeats, $gia_do_an);

        // kiểm tra tính tổng tiền khi có mã giảm giá nếu có
        // Kiểm tra tính tổng tiền khi có mã giảm giá nếu có
        if ($request->ma_giam_gia) {
            $result = $this->tinhTienVoucher($request->ma_giam_gia, $tong_tien);
            $tong_tien = $result['tong_tien_sau_giam']; // Cập nhật tổng tiền
        }


        $booking =  Booking::create([
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
            'do_an' => $ten_do_an,
            'ma_giam_gia' => $request->ma_giam_gia // luu mã giảm giá nếu có
        ]);

        // // Sau khi tạo booking thành công
        foreach ($selectedSeats as $seatId) {
            DB::table('seat_showtime_status')->updateOrInsert(
                [
                    'ghengoi_id' => $seatId,
                    'thongtinchieu_id' => $request->thongtinchieu_id
                ],
                [
                    'trang_thai' => 1 // 1 = booked
                ]
            );
        }

        return response()->json([
            'message' => 'Tạo booking thành công đến trang thanh toán.',
            'tong_tien' => $tong_tien, // trả về tổng tiền đã giảm
            'data' => $booking
        ], 201);

        // {                   
        //     "thongtinchieu_id":12,                
        //     "doan_id": 2,                   
        //     "ghe_ngoi": [525,526,527],
        //     "so_luong_do_an" : 1  
        //     "ma_giam_gia" : "Giam"  
        // }
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
