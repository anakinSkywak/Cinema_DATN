<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Membership;
use Carbon\Carbon;
use Mail;
use App\Mail\MembershipNotification;

// class SendMembershipReminder extends Command
// {
//     protected $signature = 'membership:send-reminder';
//     protected $description = 'Send reminder email to users when membership is about to expire or has expired';

//     public function __construct()
//     {
//         parent::__construct();
//     }

//     public function handle()
//     {
//         $memberships = Membership::with('registerMember.user')
//             ->where('ngay_het_han', '<=', Carbon::now()->addDays(2)) // Hết hạn hoặc gần hết hạn
//             ->get();
    
//         foreach ($memberships as $membership) {
//             $expirationDate = Carbon::parse($membership->ngay_het_han);
//             $messageContent = '';
    
//             if ($expirationDate->isBefore(Carbon::now())) {
//                 $membership->trang_thai = 1; // Thẻ hết hạn
//                 $messageContent = "Thẻ hội viên của bạn đã hết hạn. Vui lòng đăng ký lại thẻ mới!";
//             } elseif ($expirationDate->diffInDays(Carbon::now()) <= 2) {
//                 $messageContent = "Thẻ hội viên của bạn sắp hết hạn. Vui lòng gia hạn thẻ!";
//             } else {
//                 $messageContent = "Thẻ hội viên của bạn còn thời gian sử dụng.";
//             }
    
//             $membership->renewal_message = $messageContent;
//             $membership->save(); // Lưu lại các thay đổi
    
//             $email = $membership->registerMember->user->email;
//             Mail::to($email)->send(new MembershipNotification($membership, $messageContent));
//         }
//     }
    
// }

