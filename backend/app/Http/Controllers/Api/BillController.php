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
        $data = Booking::with(['showtime', 'seat'])->findOrFail($id);

        if (!$data) {
            return response()->json([
                "message" => "không tìm thấy đơn này"
            ], 404);
        }

        $pdf = Pdf::loadView('bills.bill', compact('data'));

        return $pdf->stream("bill_{$data->id}.pdf");
    }
}
