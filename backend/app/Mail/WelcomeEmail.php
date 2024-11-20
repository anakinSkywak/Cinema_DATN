<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class WelcomeEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Các thuộc tính của class WelcomeEmail
     * 
     * @var User $user Thông tin người dùng
     * @var string $otp Mã OTP xác thực
     */
    public $user;
    public $otp;

    /**
     * Khởi tạo instance của WelcomeEmail
     * 
     * @param User $user Thông tin người dùng đăng ký
     * @param string $otp Mã OTP để xác thực email
     * @return void
     */
    public function __construct(User $user, $otp)
    {
        $this->user = $user;
        $this->otp = $otp;
    }

    /**
     * Build email welcome
     * 
     * @return $this
     */
    public function build()
    {
        return $this->view('email.emailVerify')
                    ->subject('Chào mừng bạn đến với hệ thống của chúng tôi')
                    ->with('otp', $this->otp);  
    }
} 