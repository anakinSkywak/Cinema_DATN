<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentSuccessMail extends Mailable
{
    use Queueable, SerializesModels;

    public $membership; // Biến lưu Membership

    public function __construct($membership)
    {
        $this->membership = $membership; // Gán Membership vào biến public
    }

    public function build()
    {
        return $this->subject('Thông báo thanh toán thành công')
                    ->view('emails.payment_success') // Truyền membership sang view
                    ->with('membership', $this->membership);
    }
}
