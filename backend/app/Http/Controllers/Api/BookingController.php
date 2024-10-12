<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Food;
use App\Models\Showtime;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index() {}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // user booking movie 
        // check khi chọn all booking
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'thongtinchieu_id' => 'required|exists:showtimes,id',
            'so_luong' => 'numeric|min:1',
            'ghi_chu' => 'string|255',
            'ma_giam_gia' => 'string|255',
            'doan_id' => 'required|exists:foods,id'
        ]);

        // lấy phim từ xuất chiếu khi booking
        $showtime = Showtime::with('movie')->find($request->thongtinchieu_id);
        if (!$showtime) {
            return response()->json([
                'message' => 'Suất chiếu không tồn tại'
            ], 404);
        }

        $food = Food::with('movie')->find('doan_id');
        $gia_phim = $showtime->movie->gia_ve;  // lay gia ve de tinh tien ve phim

        //lấy giá đồ ăn từ bảng foods 
        $food = Food::find($request->doan_id);
        $gia_do_an = $food ? $food->gia : 0; // co do an thì tinh tien k co id do an thi = 0 ko co tien
        $so_luong = $request->so_luong ?? 1;

        // tinh tong tien cua ve phim 
        $tong_tien = ($gia_phim * $so_luong) + $gia_do_an; // defult là 1 : đặt được 1 vé 

        // ap dung ma giam gia : code sau

        // tinh toan tien theo the hoi vien
        

        // tong tien thanh toan = tongtien
        $tong_tien_thanh_toan = $tong_tien;

        // tạo thêm mới booking
        $booking = Booking::create([
            'user_id' => $request->user_id,
            'thongtinchieu_id' => $request->thongtinchieu_id,
            'so_luong' => $request->so_luong,
            'ghi_chu' => $request->ghi_chu,
            'ma_giam_gia' => $request->ma_giam_gia,
            'doan_id' => $request->doan_id,
            'tong_tien' => $tong_tien,
            'tong_tien_thanh_toan' => $tong_tien_thanh_toan,
            'ngay_mua' => Carbon::now(), // ngay mua theo ngay hien tai ko can them
            'trang_thai' => 0 // mac dinh 0 vi chua thanh toan
        ]);

        // test 

        // tra ve khi booking voi cac thong tin thanh cong 
        // booking xong chuyen den booking_deltail chon ghe ngoi sau do thanh toan
        // sau do chuyen den paymet call thanh toan va do du lieu vao booking_datail cap nhat trang thai sang da than toan
        return response()->json([
            'message' => 'Tạo booking thành công , den trang thanh toan payment!',
            'data' => $booking
        ], 201);
    }

    /**
     * Display the specified resource.
     */
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


    /**
     * Update the specified resource in storage.
     */
    public function test_đểyên(Request $request, string $id)
    {

        // dd($request->all());
        // neu chua an nut thanh toan co the chinh sua du lieu
        // luu booking vao trang ve phim chua thanh toan de tien hanh thanh toan
        $dataID = Booking::find($id);
        if (!$dataID) {
            return response()->json([
                'message' => 'Không có booking theo id này'
            ], 404);
        }

        // check khi chinh sua
        $request->validate([
            'ghi_chu' => 'nullable|string|max:255',
            'doan_id' => 'nullable|exists:foods,id'
        ]);

        // tim food theo id di de thay doi
        // giu gia do an cu
        $gia_do_an = 0;

        // kiem tra neu thay doi do an lay id moi 
        // neu k thay doi lay gia cu 
        if ($request->has('doan_id') && $request->doan_id != $dataID->doan_id) {
            // lay do an theo id
            // neu thay doi do an them , lay gia moi
            $food = Food::find($request->doan_id);
            if ($food) {
                // lay theo gia do an moi , id
                $gia_do_an = $food->gia;
            }
        } else {
            // nếu không thay đổi đồ ăn thì giữ nguyên giá đồ ăn cũ
            $food = Food::find($dataID->doan_id); // lấy đồ ăn theo id cũ
            $gia_do_an = $food ? $food->gia : 0;
        }

        // Số lượng mặc định là 1, không cần kiểm tra
        $gia_phim = $dataID->tong_tien / $dataID->so_luong; // Tính giá phim (với số lượng là 1)

        // Tính tổng tiền (giá phim hiện tại + giá đồ ăn)
        $tong_tien = $gia_phim + $gia_do_an;
        $tong_tien_thanh_toan = $tong_tien;

        // tạo thêm mới booking
        $dataID->update([
            'ghi_chu' => $request->ghi_chu,
            'doan_id' => $request->doan_id ?: $dataID->doan_id, // Nếu không có thay đổi đồ ăn thì giữ nguyên doan_id cũ
            'tong_tien' => $tong_tien,
            'tong_tien_thanh_toan' => $tong_tien_thanh_toan,
        ]);

        return response()->json([
            'message' => 'Cập nhật dữ liệu thành công , phai den trang thanh toan payment!',
            'data' => $dataID
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

    /**
     * Remove the specified resource from storage.
     */
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
