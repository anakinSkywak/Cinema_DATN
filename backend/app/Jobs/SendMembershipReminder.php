<?php

namespace App\Jobs;

use Carbon\Carbon;
use App\Models\Membership;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use App\Mail\MembershipNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendMembershipReminder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            // Lấy tất cả các thẻ hội viên có ngày hết hạn trong 2 ngày tới
            $memberships = Membership::with('registerMember.user')
                ->where('ngay_het_han', '<=', now()->addDays(2)) // Lọc theo ngày hết hạn
                ->get();

            foreach ($memberships as $membership) {
                // Chuyển đổi ngày hết hạn
                $expirationDate = Carbon::parse($membership->ngay_het_han);
                $messageContent = '';

                // Kiểm tra xem thẻ đã hết hạn chưa
                if ($expirationDate->isBefore(now())) {
                    $membership->trang_thai = 1; // Đánh dấu là hết hạn
                    $messageContent = "Thẻ hội viên của bạn đã hết hạn. Vui lòng đăng ký lại thẻ mới!";
                }
                // Kiểm tra thẻ sắp hết hạn (trong vòng 2 ngày)
                elseif ($expirationDate->diffInDays(now()) <= 2) {
                    $messageContent = "Thẻ hội viên của bạn sắp hết hạn. Vui lòng gia hạn thẻ!";
                }
                // Nếu thẻ còn thời gian sử dụng
                else {
                    $messageContent = "Thẻ hội viên của bạn còn thời gian sử dụng.";
                }

                // Cập nhật thông báo và trạng thái thẻ
                $membership->renewal_message = $messageContent;
                $membership->save();

                // Gửi email thông báo cho người dùng
                $email = $membership->registerMember->user->email;
                Mail::to($email)->send(new MembershipNotification($membership, $messageContent));
            }
        } catch (\Exception $e) {
            // Ghi log lỗi nếu có
            Log::error('Error in SendMembershipReminder Job: ' . $e->getMessage());
        }
    }
}
