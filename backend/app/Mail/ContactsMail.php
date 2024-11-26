<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class ContactsMail extends Mailable
{
    public $contact;
    public $admin_reply;  // Thêm thuộc tính để lưu phản hồi của admin

    public function __construct($contact, $admin_reply)
    {
        $this->contact = $contact;
        $this->admin_reply = $admin_reply;  // Gán phản hồi của admin
    }

    public function build()
    {
        return $this->subject('Phản hồi từ hệ thống')
                    ->view('emails.contacts')
                    ->with([
                        'ho_ten' => $this->contact['ho_ten'],
                        'noidung' => $this->contact['noidung'],
                        'admin_reply' => $this->admin_reply,  // Truyền phản hồi admin vào view
                    ]);
    }
}
