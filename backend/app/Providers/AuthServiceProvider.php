<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Ánh xạ các chính sách xác thực
        $this->registerPolicies();

        // Thiết lập thời gian hết hạn token
        Passport::tokensExpireIn(now()->addDays(15));

        // Thiết lập thời gian hết hạn token refresh
        Passport::refreshTokensExpireIn(now()->addDays(30));
        
        // Thiết lập thời gian hết hạn token personal access
        Passport::personalAccessTokensExpireIn(now()->addMonths(6));
    }
}
