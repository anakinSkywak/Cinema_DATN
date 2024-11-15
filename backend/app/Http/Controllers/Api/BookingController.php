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

    // Hàm tính tiền đồ ăn với nhiều món ăn
    private function tinhTienDoAn($foods, $soLuongFoods)
    {
        $tongTienDoAn = 0;
        foreach ($foods as $index => $foodId) {
            $food = Food::find($foodId);
            $soLuong = isset($soLuongFoods[$index]) ? $soLuongFoods[$index] : 1;
            if ($food) {
                $tongTienDoAn += $food->gia * $soLuong;
            }
        }
        return $tongTienDoAn;
    }

    // hàm tính tổng tiền với giá phim , đồ ăn , số lượng ghế , giá ghế
    private function tongTien($showtime, array $selectedSeats, $foodPrice = 0)
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
        // Truy vấn mã giảm giá
        $voucher = Voucher::where('ma_giam_gia', $ma_giam_gia)
            ->where('ngay_het_han', '>=', Carbon::now())
            ->where('trang_thai', 0)  // Trạng thái mã giảm giá còn hiệu lực
            ->whereColumn('so_luong_da_su_dung', '<', 'so_luong')
            ->first();

        // Kiểm tra nếu không tồn tại mã giảm giá
        if (!$voucher) {
            return [
                'error' => 'Mã giảm giá không hợp lệ hoặc đã hết hạn.',
                'tong_tien_sau_giam' => $tong_tien,
            ];
        }

        // Kiểm tra nếu mã giảm giá đã hết số lượng
        if ($voucher->so_luong_da_su_dung >= $voucher->so_luong) {
            return [
                'error' => 'Mã giảm giá đã hết.',
                'tong_tien_sau_giam' => $tong_tien,
            ];
        }

        // Tính mức giảm giá theo % (nếu có)
        $muc_giam_gia = $voucher->muc_giam_gia;
        $giam_gia = $tong_tien * ($muc_giam_gia / 100);

        // Tổng tiền sau khi giảm
        $tong_tien_sau_giam = max($tong_tien - $giam_gia, 0); // Đảm bảo tổng tiền không âm

        // Cập nhật số lượng đã sử dụng
        $voucher->increment('so_luong_da_su_dung');

        return [
            'tong_tien_sau_giam' => $tong_tien_sau_giam,
        ];
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
            'doan.*.doan_id' => 'exists:foods,id',
            'doan.*.so_luong_do_an' => 'nullable|numeric|min:1',
            'ma_giam_gia' => 'nullable|string|max:255',
            'ghi_chu' => 'nullable|string|max:255',
        ]);

        $showtime = Showtime::with('movie')->find($request->thongtinchieu_id);

        if (!$showtime) {
            return response()->json(['message' => 'Suất chiếu không tồn tại.'], 404);
        }

        $selectedSeats = $request->ghe_ngoi;
        $seatNames = $this->getNameSeat($selectedSeats);

        $doAnDetails = [];
        $tongTienDoAn = 0;

        foreach ($request->doan as $doan) {
            $food = Food::find($doan['doan_id']);
            if ($food) {
                $doAnDetails[] = ['ten_do_an' => $food->ten_do_an, 'so_luong' => $doan['so_luong_do_an']];
                $tongTienDoAn += $food->gia * $doan['so_luong_do_an'];
            }
        }

        $doAnString = $this->formatDoAnString($request->doan);

        // Tính tổng tiền
        $tongTien = $this->tongTien($showtime, $selectedSeats, $tongTienDoAn);

        // Kiểm tra và áp dụng mã giảm giá nếu có
        if ($request->ma_giam_gia) {
            $result = $this->tinhTienVoucher($request->ma_giam_gia, $tongTien);
            if (isset($result['error'])) {
                return response()->json($result, 400); 
            }
            $tongTien = $result['tong_tien_sau_giam'];
        }

        // Tạo Booking
        $booking = Booking::create([
            'user_id' => $user->id,
            'thongtinchieu_id' => $request->thongtinchieu_id,
            'so_luong' => count($selectedSeats),
            'ngay_mua' => Carbon::now(),
            'trang_thai' => 0,  // Chưa thanh toán
            'ghe_ngoi' => implode(', ', $seatNames),
            'do_an' => $doAnString,
            'ma_giam_gia' => $request->ma_giam_gia,
            'ghi_chu' => $request->ghi_chu,
            'tong_tien' => $tongTien,
            'tong_tien_thanh_toan' => $tongTien,
        ]);

        // Cập nhật trạng thái ghế
        foreach ($selectedSeats as $seatId) {
            DB::table('seat_showtime_status')->updateOrInsert(
                [
                    'ghengoi_id' => $seatId,
                    'thongtinchieu_id' => $request->thongtinchieu_id,
                    'gio_chieu' => $showtime->gio_chieu
                ],
                ['trang_thai' => 1]
            );
        }

        return response()->json([
            'message' => 'Tạo Booking thành công, vui lòng thanh toán.',
            'data' => $booking,
            'doan_details' =>  $doAnDetails // chỉ để xem dữ liệu thôi
        ], 200);
    }

    // Hàm format tên món ăn và số lượng món ăn thành chuỗi
    private function formatDoAnString($doanDetails)
    {
        $doAnList = [];
        foreach ($doanDetails as $doan) {
            // Kiểm tra nếu 'ten_do_an' tồn tại trong mỗi phần tử
            if (isset($doan['ten_do_an'])) {
                $doAnList[] = $doan['ten_do_an'] . ' (x' . $doan['so_luong_do_an'] . ')';
            } else {
                // Nếu không có tên món ăn, bạn có thể xử lý tùy ý, ví dụ: bỏ qua hoặc báo lỗi
                $doAnList[] = 'Món ăn không xác định (x' . $doan['so_luong_do_an'] . ')';
            }
        }
        return implode(', ', $doAnList); // Chuyển thành chuỗi với định dạng: "Phở bò (x2), Bánh mì (x2)"
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





    public function Booking_y(Request $request)
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
            'ghe_ngoi' => 'required|array|min:1',
            'ghe_ngoi.*' => 'required|exists:seats,id',
            'doan_id' => 'nullable|exists:foods,id',
            'so_luong_do_an' => 'nullable|numeric|min:1',
            'ma_giam_gia' => 'nullable|string|max:255',
            'ghi_chu' => 'nullable|string|max:255',
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

        //$selectedSeats = explode(', ', $selectedSeats->ghe_ngoi);

        $food = $this->getFood($request->doan_id);
        // số lượng đồ ăn
        $so_luong_do_an = $this->soLuongFood($food, $request->so_luong_do_an);
        // tính toán giá đồ ăn
        $gia_do_an = $this->tinhTienDoAn($food, $so_luong_do_an);
        // truy vấn thêm tên đồ ăn vào bảng cột do_an
        $ten_do_an = $food ? $food->ten_do_an : null;
        // tính tong tien booking->
        $tong_tien = $this->tongTien($showtime, $selectedSeats, $gia_do_an);
        // kiểm tra tính tổng tiền khi có mã giảm giá nếu có
        if ($request->ma_giam_gia) {
            $result = $this->tinhTienVoucher($request->ma_giam_gia, $tong_tien);
            $tong_tien = $result['tong_tien_sau_giam'];
        }

        $booking =  Booking::create([
            'user_id' => $user->id,
            'thongtinchieu_id' => $request->thongtinchieu_id,
            'so_luong' => count($selectedSeats),
            'ngay_mua' => Carbon::now(),
            'trang_thai' => 0, // chưa thanh toán
            'ghe_ngoi' => implode(', ', $seatNames),
            'doan_id' => $request->doan_id,
            'so_luong_do_an' => $so_luong_do_an,
            'do_an' => $food ? $ten_do_an : null,
            'ma_giam_gia' => $request->ma_giam_gia,
            'ghi_chu' => $request->ghi_chu,
            'tong_tien' => $tong_tien,
            'tong_tien_thanh_toan' => $tong_tien,
        ]);

        // update chặn ghế ngồi theo các giờ
        // Sau khi tạo booking thành công
        foreach ($selectedSeats as $seatId) {
            DB::table('seat_showtime_status')->updateOrInsert(
                [
                    'ghengoi_id' => $seatId,
                    'thongtinchieu_id' => $request->thongtinchieu_id,
                    'gio_chieu' => $showtime->gio_chieu
                ],
                [
                    'trang_thai' => 1 // 1 = booked
                ]
            );
        }

        return response()->json([
            'message' => 'Tạo Booking ok đến trang thanh toán',
            //'tong_tien' => $tong_tien, // trả về tổng tiền đã giảm
            'data' => $booking,
        ], 200);
    }

    // hàm tính tiền đồ ăn với với giá đồ ăn và số lượng thêm đồ ăn nếu có 
    private function tinhTienDoAn_y($food, $soluong)
    {
        return $food ? $food->gia * $soluong : 0;
    }
}
