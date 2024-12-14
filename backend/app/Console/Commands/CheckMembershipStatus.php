<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Membership;
use Carbon\Carbon;
use App\Jobs\SendMembershipEmailJob;

class CheckMembershipExpiration extends Command
{
    protected $signature = 'membership:check-expiration';
    protected $description = 'Check and notify users of upcoming membership expiration';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $currentDate = Carbon::now();
        $memberships = Membership::all();

        foreach ($memberships as $membership) {
            $expirationDate = Carbon::parse($membership->ngay_het_han);

            if ($expirationDate->isBefore($currentDate)) {
                // Thẻ đã hết hạn
                $membership->trang_thai = 1;
                $membership->renewal_message = "Thẻ hội viên đã hết hạn. Vui lòng đăng ký lại thẻ hội viên mới!";
                SendMembershipEmailJob::dispatch($membership, $membership->renewal_message);
            } elseif ($expirationDate->diffInDays($currentDate) <= 2) {
                // Thẻ sắp hết hạn (dưới 2 ngày)
                $membership->renewal_message = "Thẻ hội viên sắp hết hạn!!!. Vui lòng gia hạn thẻ!";
                SendMembershipEmailJob::dispatch($membership, $membership->renewal_message);
            }
            $membership->save();
        }

        $this->info('Checked all memberships for expiration!');
    }
}
