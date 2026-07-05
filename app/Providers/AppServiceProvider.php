<?php

namespace App\Providers;

use App\Services\SupabaseService;
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
        \Illuminate\Support\Facades\RateLimiter::for('login', function (\Illuminate\Http\Request $request) {
            $email = (string) $request->input('email');
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(5)->by($email . '|' . $request->ip());
        });
    }
}
