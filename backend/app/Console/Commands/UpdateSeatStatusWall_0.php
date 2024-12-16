<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateSeatStatusWall_0 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'revert:seat-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Đã cập nhật lại ghế bạn chọn thành Trống do bạn hết thời gian 6 phút chọn ghế mà không booking !';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // truy vấn
        $now = Carbon::now();

        // lấy danh sách ghế ở seat_showtime_status = 3 nhưng quá hạn 3 phút không thành 1
        $seat_showtime_status = DB::table('seat_showtime_status')
            ->where('trang_thai', 3)

            //->get(); // test trường hợp không set thời gian updated_at sau bn phút

        ->where('updated_at'  , '<' , $now->subMinutes(6))->get(); // sau 6p trong lúc booking

        // truy vấn liên tục ghế có update = 3 sau 6p ko thành 1 thì update thành lại 0 trống
        foreach ($seat_showtime_status as $seat) {
            DB::table('seat_showtime_status')
                ->where('ghengoi_id', $seat->ghengoi_id)
                ->where('thongtinchieu_id', $seat->thongtinchieu_id)
                ->update([
                    'trang_thai' => 0,
                    'user_id' => null,
                    'updated_at' => $now,
                ]);
            $this->info("Ghế theo id {$seat->ghengoi_id} đã quá 6 phút và được chuyển về thành Trống");
        }
    }
}
