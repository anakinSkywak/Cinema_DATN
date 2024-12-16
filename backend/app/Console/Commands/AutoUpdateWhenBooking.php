<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use DB;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB as FacadesDB;

class AutoUpdateWhenBooking extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'revert:auto-update-when-booking';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cập nhật ghế từ 1 thành 0 khi booking mà không thanh toán thành công vẫn giữ booking không thanh toán thành công ';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();


        $bookings = FacadesDB::table('bookings')
            ->where('trang_thai', 0)
            ->where('updated_at', '<', $now->subMinutes(18)) // sau 18p ko thành 1 hủy update lại ghế
            ->get();

        if ($bookings->isEmpty()) {
            $this->info('Không có booking nào cần cập nhật.');
            return;
        }

        foreach ($bookings as $booking) {
            // phân tách chuỗi
            $seatList = explode(',', $booking->ghe_ngoi);

            foreach ($seatList as $seat) {

                $seatStatus = FacadesDB::table('seat_showtime_status')
                    ->where('thongtinchieu_id', $booking->thongtinchieu_id)
                    ->where('ten_ghe_ngoi', trim($seat))
                    ->first();

                // chỉ cập nhật nếu trạng thái không phải là 0
                if ($seatStatus && $seatStatus->trang_thai != 0) {
                    FacadesDB::table('seat_showtime_status')
                        ->where('thongtinchieu_id', $booking->thongtinchieu_id)
                        ->where('ten_ghe_ngoi', trim($seat))
                        ->update([
                            'trang_thai' => 0,
                            'user_id' => null,
                            'updated_at' => $now,
                        ]);

                    $this->info("Ghế '{$seat}' thuộc thông tin chiếu ID {$booking->thongtinchieu_id} đã được cập nhật trạng thái thành 0.");
                }
            }

            // cập nhật booking thành 2 hủy đơn
            FacadesDB::table('bookings')
                ->where('id', $booking->id)
                ->update([
                    'trang_thai' => 2,
                    'updated_at' => $now,
                ]);

            $this->info("Booking ID {$booking->id} cập nhật thành 2 hủy đơn hàng");
        }
    }
}
