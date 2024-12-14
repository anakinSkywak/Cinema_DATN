<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MembershipNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $membership;
    public $message;

    public function __construct($membership, $message)
    {
        $this->membership = $membership;
        $this->message = $message;
    }

    public function build()
    {
        return $this->view('emails.membership_notification')
                    ->with([
                        'membership' => $this->membership,
                        'message' => $this->message, 
                    ]);
    }
}
