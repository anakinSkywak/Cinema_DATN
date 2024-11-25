<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\URL;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        VerifyEmail::toMailUsing(function ($notifiable, string $otp) {
            return (new MailMessage)
                ->subject('Xác minh địa chỉ email của bạn')
                ->greeting('Chào bạn!')
                ->line('Cảm ơn bạn đã đăng ký tài khoản với chúng tôi. Vui lòng xác minh địa chỉ email của bạn để hoàn tất quá trình đăng ký.')
                ->line('OTP: ' . $otp)
                ->line('Nếu bạn không đăng ký tài khoản này, bạn có thể bỏ qua email này.')
                ->line('Trân trọng,')   
                ->line(config('app.name'));
        });
        

        ResetPassword::toMailUsing(function (object $notifiable, string $token) {
            $frontendUrl = config('app.frontend_url', 'http://localhost:5173');
            $url = $frontendUrl . '/reset-password?token=' . $token . '&email=' . $notifiable->getEmailForPasswordReset();
            
            return (new MailMessage)
                ->subject('Thông báo reset mật khẩu')
                ->greeting('Chào bạn!')
                ->line('Bạn đã yêu cầu đặt lại mật khẩu cho tài khoản của mình. Vui lòng nhấp vào nút bên dưới để đặt lại mật khẩu.')
                ->action('Đặt lại mật khẩu', $url)
                ->line('Nếu bạn không yêu cầu đặt lại mật khẩu, vui lòng bỏ qua email này.')
                ->line('Trân trọng,')
                ->line(config('app.name'));
        });
        
        
    }
}
