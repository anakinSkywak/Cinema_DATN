<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Showtime;
use App\Models\Voucher;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class BillController extends Controller
{
    public function exportBill($id)
    {
        // Lấy dữ liệu từ bảng Booking với các quan hệ
        $data = Booking::with([
            'showtime',
            'seat',
            'voucher',
            'food',
            'user',
            'payment',
        ])->find($id);

        if (!$data) {
            return response()->json([
                "message" => "Không tìm thấy đơn này"
            ], 404);
        }

        // Lấy thông tin phim và giá vé từ bảng movies
        $tenPhim = Showtime::join('movies', 'showtimes.phim_id', '=', 'movies.id')
            ->where('showtimes.id', $data->thongtinchieu_id)
            ->select('movies.ten_phim', 'movies.gia_ve')
            ->first();

        if (!$tenPhim) {
            return response()->json([
                "message" => "Không tìm thấy thông tin phim"
            ], 404);
        }

        // Lấy thông tin phòng chiếu từ bảng rooms
        $tenRoom = Showtime::join('rooms', 'showtimes.room_id', '=', 'rooms.id')
            ->where('showtimes.id', $data->thongtinchieu_id)
            ->select('rooms.ten_phong_chieu')
            ->first();

        if (!$tenRoom) {
            return response()->json([
                "message" => "Không tìm thấy thông tin phòng chiếu"
            ], 404);
        }

        // Lấy giá trị voucher nếu có
        $giaTriVoucher = $data->voucher ? $data->voucher->muc_giam_gia : 0;

        // Tạo PDF với view và đặt font mặc định hỗ trợ UTF-8
        $pdf = Pdf::loadView('bills.bill', compact(
            'data',
            'tenPhim', 
            'tenRoom',
            'giaTriVoucher'
        ))
        ->setPaper([0, 0, 226.77, 9999], 'portrait')
        ->setOptions([
            'defaultFont' => 'DejaVu Sans'
        ]);

        // Trả về file PDF dưới dạng tải xuống
        return $pdf->download("bill_{$data->id}.pdf");
    }
}
