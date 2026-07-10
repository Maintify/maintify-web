<?php

namespace App\Providers;

use App\Services\SupabaseService;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Daftarkan SupabaseService sebagai singleton
        $this->app->singleton(SupabaseService::class, function ($app) {
            return new SupabaseService;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (!app()->runningUnitTests()) {
            \Illuminate\Validation\Rules\Password::defaults(function () {
                return \Illuminate\Validation\Rules\Password::min(8)
                    ->letters()
                    ->numbers();
            });
        }

        RateLimiter::for('login', function (Request $request) {
            $email = (string) $request->input('email');

            return Limit::perMinute(5)->by($email.'|'.$request->ip());
        });
    }
}
