<?php

namespace App\Console;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Membership;
use App\Jobs\SendMembershipEmailJob;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */

    protected function schedule(Schedule $schedule): void
    {
        // Cập nhật trạng thái săn mã giảm giá mỗi ngày lúc 00:00
        $schedule->command('countdown:update-status')->dailyAt('00:00');
        $schedule->command('membership:check-expiration')->daily();

        
        // trạng thái ghế khi chọn mà ko thành 1 sau 6p = update ghế thành 0 trống
        $schedule->command('revert:seat-status')->everyMinute();

        // trạng thái update ghế có trạng thái = 1 đã đặt
        // nhưng booking chưa thanh toán thành công thì ghế đó về 0 trống
        // 18 phút cả bền thanh toán vnpay
        $schedule->command('revert:auto-update-when-booking')->everyMinute();

    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');
        require base_path('routes/console.php');
    }
}
