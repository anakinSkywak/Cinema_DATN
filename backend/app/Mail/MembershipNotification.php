<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MembershipNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $membership;
    public $messageContent;

    /**
     * Create a new message instance.
     *
     * @param $membership
     * @param $messageContent
     */
    public function __construct($membership, $messageContent)
    {
        $this->membership = $membership;
        $this->messageContent = $messageContent;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.membership_notification')
                    ->with([
                        'membership' => $this->membership,
                        'messageContent' => $this->messageContent,
                    ]);
    }
}
