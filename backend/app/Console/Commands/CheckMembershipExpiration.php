<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Membership;
use App\Mail\MembershipExpirationNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

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
            } elseif ($expirationDate->diffInDays($currentDate) <= 2) {
                // Thẻ sắp hết hạn (dưới 2 ngày)
                $membership->renewal_message = "Thẻ hội viên sắp hết hạn!!!. Vui lòng gia hạn thẻ!";
            }

            // Kiểm tra nếu có user liên kết
            if ($membership->registerMember && $membership->registerMember->user) {
                $email = $membership->registerMember->user->email;
                $this->info('Sending email to: ' . $email);
                Mail::to($email)->send(new MembershipExpirationNotification($membership)); // Gửi email
            } else {
                $this->info('No user found for membership ID: ' . $membership->id);
            }

            $membership->save();
        }

        $this->info('Checked all memberships for expiration and sent notifications!');
    }
}
