<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Notifications\ResetPassword;

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
        ResetPassword::createUrlUsing(function ($user, string $token) {
            // Kita arahkan ke URL website (misal: localhost/mobile-reset-password)
            return url('/mobile-reset-password?token=' . $token . '&email=' . urlencode($user->email));
        });
    }
}
