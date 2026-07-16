<?php

namespace App\Providers;

use App\Services\SupabaseService;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

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
        if (! app()->runningUnitTests()) {
            Password::defaults(function () {
                return Password::min(8)
                    ->letters()
                    ->numbers();
            });
        }

        // Production hardening (SEC): force HTTPS and secure/http-only session
        // cookies whenever the app runs in production, regardless of whether the
        // deployment .env sets these explicitly. This prevents an operator from
        // accidentally shipping with APP_DEBUG on or cookies sent over plain HTTP.
        if ($this->app->environment('production')) {
            URL::forceScheme('https');

            config([
                'app.debug' => false,
                'session.secure' => true,
                'session.http_only' => true,
                'session.same_site' => config('session.same_site') ?: 'lax',
            ]);
        }

        RateLimiter::for('login', function (Request $request) {
            $email = (string) $request->input('email');

            return Limit::perMinute(5)->by($email.'|'.$request->ip());
        });
    }
}
