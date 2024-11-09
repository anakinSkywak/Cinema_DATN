<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Showtime;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class BillController extends Controller
{
    //

    public function exportBill($id)
    {
        // Lấy dữ liệu từ bảng Booking với các quan hệ
        $data = Booking::with(['showtime', 'seat'])->findOrFail($id);
        $tenPhim = Showtime::join('movies', 'showtimes.phim_id', '=', 'movies.id')
            ->where('showtimes.id', $data->thongtinchieu_id)
            ->select('movies.ten_phim') // 'ten_phim' là cột tên phim trong bảng movies
            ->first();
        $tenRoom = Showtime::join('rooms', 'showtimes.room_id', '=', 'rooms.id')
            ->where('showtimes.id', $data->thongtinchieu_id)
            ->select('rooms.ten_phong_chieu') // 'ten_phong_chieu' là cột tên phòng chiếu trong bảng rooms
            ->first();
        

        if (!$data) {
            return response()->json([
                "message" => "Không tìm thấy đơn này"
            ], 404);
        }

        // Tạo PDF với view và đặt font mặc định hỗ trợ UTF-8
        $pdf = Pdf::loadView('bills.bill', compact([
            'data',
            'tenPhim',
            'tenRoom',
        ]))
            // ->setPaper([0, 0, 226.77, 9999], 'portrait')
            ->setPaper('a4')
            ->setOptions([
                'defaultFont' => 'DejaVu Sans'
            ]);

        // Trả về file PDF dưới dạng tải xuống
        return $pdf->download("bill_{$data->id}.pdf");
    }
}
