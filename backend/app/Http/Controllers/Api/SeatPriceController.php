<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SeatPrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SeatPriceController extends Controller
{


    // list seat price all tất cả có thể dùng hoặc không
    public function listSeatPrice()
    {
        $seatPriceAll = SeatPrice::all();

        if ($seatPriceAll->isEmpty()) {
            return response()->json([
                'message' => 'Không có dữ liệu nào của Giá Ghế !'
            ], 404);
        }

        return response()->json([
            'message' => 'Lấy tất cả dữ liệu bảng giá ghế thành công',
            'data' => $seatPriceAll
        ], 200);
    }


    // list nhóm bảng giá ghế theo thứ vào với nhau
    public function table_seat_price()
    {

        // list 
        // truy vấn từng loại ghế + thứ + giờ

        // Monday ,  Tuesday  , Wednesday , Thursday , Friday , Saturday , Sunday

        $SeatMonday = DB::table('seat_prices')
            ->where('thu_trong_tuan', '=', "Monday")
            //->where('loai_ghe', '=', "Thường")
            ->get();

        // $SeatTuesday = DB::table('seat_prices')
        //     ->where('thu_trong_tuan', '=', "Tuesday")
        //     //->where('loai_ghe', '=', "Thường")
        //     ->get();

        // $SeatWednesday = DB::table('seat_prices')
        //     ->where('thu_trong_tuan', '=', "Wednesday")
        //     //->where('loai_ghe', '=', "Thường")
        //     ->get();


        return response()->json([
            'message' => "Thêm mới giá ghế cố định thành công",
            'seat-Monday' => $SeatMonday,
            // 'seat-Tuesday' => $SeatTuesday,
            // 'seat-Wednesday' => $SeatWednesday
        ], 200);


        //dd($SeatThuong);
    }

    // from thêm mới bảng giá seat đổ all thể loại ghế để chọn

    // thêm mới giá ghế  : chưa xử lý xong + chưa check 
    public function store(Request $request)
    {

        $validated = $request->validate([
            'loai_ghe' => 'required|string|max:255',
            'thu_trong_tuan' => 'nullable|string|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'ngay_cu_the'  => 'nullable|date',
            'gio_bat_dau'  => 'required|date_format:H:i',
            'gio_ket_thuc'  => 'required|date_format:H:i',
            'gia_ghe'  => 'required|numeric|min:1',
            'ten_ngay_le'  => 'nullable|string|max:255',
            'la_ngay_le' => 'nullable|boolean',
        ]);

        // check thêm mới 

        // 
        
        // thêm mới 
        $seatPrice = SeatPrice::create($validated);

        return response()->json([
            'message' => "Thêm mới giá ghế cố định thành công",
            'data' => $seatPrice
        ], 201);
    }


    // show theo id
    public function show(string $id) {}


    // đưa đến from edit đổ thông tin edit đó theo id
    public function edit(string $id) {}


    // cập nhật dữ liệu mới theo id
    public function update(Request $request, $id) {}


    // xóa theo id 
    public function delete(string $id) {}
}
