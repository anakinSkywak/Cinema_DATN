<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\HistoryRotation;
use Carbon\Carbon;

class DeleteExpiredHistoryRotations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:delete-expired-history-rotations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Xóa các bản ghi lịch sử quay thưởng đã hết hạn';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Lấy danh sách các bản ghi hết hạn (ngay_het_han <= hiện tại)
        $expiredRecords = HistoryRotation::where('ngay_het_han', '<=', Carbon::now())->get();

        // Kiểm tra xem có bản ghi nào hết hạn không
        if ($expiredRecords->isEmpty()) {
            $this->info('Không có bản ghi nào hết hạn để xóa.');
            return;
        }

        // Đếm số lượng bản ghi trước khi xóa
        $deletedCount = $expiredRecords->count();

        // Xóa các bản ghi hết hạn
        foreach ($expiredRecords as $record) {
            $record->delete();
        }

        // Thông báo sau khi hoàn tất
        $this->info("Đã xóa thành công $deletedCount bản ghi hết hạn.");
    }
}
