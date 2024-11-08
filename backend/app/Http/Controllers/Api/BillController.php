<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class BillController extends Controller
{
    //

    public function exportBill($id)
    {
        // Lấy dữ liệu từ bảng Booking với các quan hệ
        $data = Booking::with(['showtime', 'seat'])->findOrFail($id);

        if (!$data) {
            return response()->json([
                "message" => "Không tìm thấy đơn này"
            ], 404);
        }

        // Tạo PDF với view và đặt font mặc định hỗ trợ UTF-8
        $pdf = Pdf::loadView('bills.bill', compact('data'))
            // ->setPaper([0, 0, 226.77, 9999], 'portrait')
            ->setPaper('a4')
            ->setOptions([
                'defaultFont' => 'DejaVu Sans'
            ]);

        // Trả về file PDF dưới dạng tải xuống
        return $pdf->download("bill_{$data->id}.pdf");
    }
}
