<?php

namespace App\Jobs;

use App\Models\Membership;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\MembershipNotification;

class SendMembershipReminder implements ShouldQueue
{
    use Dispatchable, Queueable, SerializesModels;

    /**
     * Tạo job mới.
     *
     * @return void
     */
    public function __construct()
    {
        // Bạn có thể truyền các tham số nếu cần thiết
    }

    /**
     * Xử lý công việc gửi email nhắc nhở thẻ hội viên.
     *
     * @return void
     */
    // public function handle()
    // {
    //     // Lấy tất cả thẻ hội viên
    //     $memberships = Membership::with('registerMember.user')  // Eager load quan hệ với user
    //         ->where('ngay_het_han', '<=', Carbon::now()->addDays(2)) // Thẻ hội viên hết hạn trong 2 ngày
    //         ->get();

    //     foreach ($memberships as $membership) {
    //         // Kiểm tra điều kiện hết hạn hoặc gần hết hạn
    //         $expirationDate = Carbon::parse($membership->ngay_het_han);
    //         $messageContent = '';

    //         if ($expirationDate->isBefore(Carbon::now())) {
    //             $membership->trang_thai = 1; // Thẻ hết hạn
    //             $messageContent = "Thẻ hội viên của bạn đã hết hạn. Vui lòng đăng ký lại thẻ mới!";
    //         } else {
    //             if ($expirationDate->diffInDays(Carbon::now()) <= 2) {
    //                 $messageContent = "Thẻ hội viên của bạn sắp hết hạn. Vui lòng gia hạn thẻ để tiếp tục sử dụng dịch vụ!";
    //             } else {
    //                 $messageContent = "Thẻ hội viên của bạn còn thời gian sử dụng.";
    //             }
    //             $membership->trang_thai = 0;  // Thẻ còn hiệu lực
    //         }

    //         // Cập nhật trạng thái và thông báo
    //         $membership->renewal_message = $messageContent;
    //         $membership->save();

    //         // Gửi email thông báo cho người dùng
    //         $email = $membership->registerMember->user->email;
    //         Mail::to($email)->send(new MembershipNotification($membership, $messageContent));
    //     }
    // }
}
