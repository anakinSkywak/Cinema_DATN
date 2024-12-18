<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Seat;
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
    public function getSeatPriceList()
{
    $data = DB::table('seat_prices')
        ->select('id','thu_trong_tuan', 'ngay_cu_the', 'loai_ghe', 'gio_bat_dau', 'gio_ket_thuc', 'gia_ghe' , 'ten_ngay_le' , 'la_ngay_le' , 'trang_thai')
        ->orderByRaw("FIELD(thu_trong_tuan, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'), ngay_cu_the IS NOT NULL, ngay_cu_the ASC")
        ->orderByRaw("FIELD(loai_ghe, 'Thường', 'Đôi', 'Vip')") 
        ->orderBy('gio_bat_dau', 'asc') 
        ->get()
        ->groupBy(function ($item) {
            return $item->thu_trong_tuan ?? $item->ten_ngay_le;
        })
        ->map(function ($group) {
            return $group->groupBy('loai_ghe')->map(function ($times) {
                return $times->map(function ($time) {
                    return [
                        'id' => $time->id,
                        'thu_trong_tuan' => $time->thu_trong_tuan,
                        'loai_ghe' => $time->loai_ghe,
                        'ngay_cu_the' => $time->ngay_cu_the,
                        'gio_bat_dau' => $time->gio_bat_dau,
                        'gio_ket_thuc' => $time->gio_ket_thuc,
                        'gia_ghe' => $time->gia_ghe,
                        'ten_ngay_le' => $time->ten_ngay_le,
                        'la_ngay_le' => $time->la_ngay_le,
                        'trang_thai' => $time->trang_thai,
                    ];
                });
            });
        });

   
    $sortedData = $data->sortKeysUsing(function ($key1, $key2) {
        $order = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday', ''];
        return array_search($key1, $order) <=> array_search($key2, $order);
    });

    return response()->json($sortedData, 200);
}



    // from thêm mới bảng giá seat đổ all thể loại ghế để chọn
    public function getTypySeats()
    {

        // Monday ,  Tuesday  , Wednesday , Thursday , Friday , Saturday , Sunday
        // lấy thể loại ghế có ở seat
        $getTypeSeat = DB::table('seats')
            ->select('loai_ghe_ngoi')
            ->whereNull('deleted_at')
            ->distinct()
            ->get();

        if ($getTypeSeat->isEmpty()) {
            return response()->json([
                'message' => "Không có thể loại ghế nào thêm ghế !",
            ], 404);
        }

        return response()->json([
            'message' => "Lấy tất cả thể loại ghế thành công",
            'data' => $getTypeSeat
        ], 200);
    }


    // thêm mới giá ghế 
    public function store(Request $request)
    {
        $validated = $request->validate([
            'loai_ghe' => 'required|string|max:255',
            'thu_trong_tuan' => 'nullable|string|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'ngay_cu_the' => 'nullable|date',
            'gio_bat_dau' => 'required|date_format:H:i',
            'gio_ket_thuc' => 'required|date_format:H:i|after:gio_bat_dau',
            'gia_ghe' => 'required|numeric|min:1',
            'ten_ngay_le' => 'nullable|string|max:255',
            'la_ngay_le' => 'nullable|boolean',
        ]);

<<<<<<< HEAD
        // 1 Kiểm tra logic không nhập cả "thứ trong tuần" và "ngày cụ thể"
        if (!empty($request->thu_trong_tuan) && !empty($request->ngay_cu_the)) {
            return response()->json([
                'message' => 'Không thể nhập cả thứ trong tuần và ngày cụ thể!',
            ], 422);
        }
=======
        // check 
        

>>>>>>> 495117c8e4d360aecef38803b624572de0198f3f

        // 2 Nếu là ngày lễ, tên ngày lễ phải có
        if (!empty($request->la_ngay_le) && empty($request->ten_ngay_le)) {
            return response()->json([
                'message' => 'Phải nhập tên ngày lễ nếu đây là ngày lễ!',
            ], 422);
        }

        // 3 Nếu không phải ngày lễ, tên ngày lễ phải để trống
        if (empty($request->la_ngay_le) && !empty($request->ten_ngay_le)) {
            return response()->json([
                'message' => 'Tên ngày lễ phải để trống nếu không phải ngày lễ!',
            ], 422);
        }

        // kiểm tra loại ghế có tồn tại
        $checkTypeSeat = Seat::where('loai_ghe_ngoi', $request->loai_ghe)->doesntExist();
        if ($checkTypeSeat) {
            return response()->json([
                'message' => 'Loại ghế không đúng trong bảng Seats!',
            ], 409);
        }

        // A Kiểm tra trùng thời gian theo "ngày cụ thể"**
        if (!empty($request->ngay_cu_the)) {
            $existingTimesSpecificDay = SeatPrice::where('loai_ghe', $request->loai_ghe)
                ->whereDate('ngay_cu_the', $request->ngay_cu_the) // Chỉ kiểm tra theo ngày cụ thể
                ->get(['gio_bat_dau', 'gio_ket_thuc']);

            $conflictTimesSpecificDay = [];
            foreach ($existingTimesSpecificDay as $existingTime) {
                if (
                    ($request->gio_bat_dau < $existingTime->gio_ket_thuc && $request->gio_ket_thuc > $existingTime->gio_bat_dau)
                ) {
                    $conflictTimesSpecificDay[] = [
                        'gio_bat_dau' => $existingTime->gio_bat_dau,
                        'gio_ket_thuc' => $existingTime->gio_ket_thuc
                    ];
                }
            }

            if (!empty($conflictTimesSpecificDay)) {
                return response()->json([
                    'message' => 'Khoảng thời gian đã tồn tại hoặc bị trùng lặp ngày cụ thể với các khoảng thời gian sau:',
                    'conflict_times_specific_day' => $conflictTimesSpecificDay
                ], 422);
            }
        }

        // B kiểm tra trùng thời gian theo "thứ trong tuần"**
        if (!empty($request->thu_trong_tuan)) {
            $existingTimesDayOfWeek = SeatPrice::where('loai_ghe', $request->loai_ghe)
                ->where('thu_trong_tuan', $request->thu_trong_tuan) // Chỉ kiểm tra theo thứ trong tuần
                ->get(['gio_bat_dau', 'gio_ket_thuc']);

            $conflictTimesDayOfWeek = [];
            foreach ($existingTimesDayOfWeek as $existingTime) {
                if (
                    ($request->gio_bat_dau < $existingTime->gio_ket_thuc && $request->gio_ket_thuc > $existingTime->gio_bat_dau)
                ) {
                    $conflictTimesDayOfWeek[] = [
                        'gio_bat_dau' => $existingTime->gio_bat_dau,
                        'gio_ket_thuc' => $existingTime->gio_ket_thuc
                    ];
                }
            }

            if (!empty($conflictTimesDayOfWeek)) {
                return response()->json([
                    'message' => 'Khoảng thời gian đã tồn tại hoặc bị trùng lặp thứ trong tuần với các khoảng thời gian sau:',
                    'conflict_times_day_of_week' => $conflictTimesDayOfWeek
                ], 422);
            }
        }

        // thêm mới dữ liệu vào bảng `seat_prices`
        $seatPrice = SeatPrice::create($validated);

        return response()->json([
            'message' => 'Thêm mới giá ghế thành công.',
            'data' => $seatPrice,
        ], 201);
    }



    // xóa theo id
    public function delete(string $id)
    {

        $dataID = SeatPrice::find($id);

        if (!$dataID) {
            return response()->json(['message' => 'Không có dữ liệu Seat Price theo id này'], 404);
        }

        $dataID->delete();

        return response()->json(['message' => 'Xóa Seat Price theo id thành công'], 200);
    }


    // show theo id có thể dùng hoặc không
    public function show(string $id) {

        $seatprice = SeatPrice::find($id);

        if (!$seatprice) {
            return response()->json(['message' => 'Không có dữ liệu seat price theo id này'], 404);
        }

        return response()->json([
            'message' => 'Lấy thông tin eat price theo ID thành công',
            'data' => $seatprice,
        ], 200);
    }




    // đưa đến from edit đổ thông tin edit đó theo id : ko làm
    public function edit(string $id) {}


    // cập nhật dữ liệu mới theo id : ko làm
    public function update(Request $request, $id) {}
}
