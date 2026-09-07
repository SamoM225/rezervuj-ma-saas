<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

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
        $this->configureRateLimiting();

        // Harden sessions only when the site really runs over HTTPS (APP_URL
        // decides). Forcing https:// on a plain-HTTP deployment would break every
        // asset URL and make browsers drop the Secure session cookie.
        if (str_starts_with(strtolower((string) config('app.url')), 'https://')) {
            URL::forceScheme('https');
            config([
                'session.secure' => true,
                'session.http_only' => true,
                // 'none' lets the booking flow work inside the website widget (iframe on another domain);
                // CSRF tokens still protect every form.
                'session.same_site' => 'none',
            ]);
        }
    }

    /**
     * Named limiters get their own buckets. Inline `throttle:N,M` limiters all
     * share one key per IP, so a generous group limit and a strict endpoint
     * limit would otherwise count each other's requests.
     */
    private function configureRateLimiting(): void
    {
        $byIp = fn (Request $request) => $request->ip();
        $byEmailAndIp = fn (Request $request) => Str::lower((string) $request->input('email')).'|'.$request->ip();

        RateLimiter::for('login', fn (Request $request) => Limit::perMinute(10)->by($byEmailAndIp($request)));
        RateLimiter::for('otp', fn (Request $request) => Limit::perMinute(10)->by($byIp($request)));
        RateLimiter::for('otp-resend', fn (Request $request) => Limit::perMinutes(10, 3)->by($byIp($request)));
        RateLimiter::for('password', fn (Request $request) => Limit::perMinutes(10, 5)->by($byEmailAndIp($request)));
        RateLimiter::for('signup', fn (Request $request) => Limit::perMinutes(10, 10)->by($byIp($request)));
        RateLimiter::for('booking', fn (Request $request) => Limit::perMinute(20)->by($byIp($request)));
        RateLimiter::for('hold', fn (Request $request) => Limit::perMinute(30)->by($byIp($request)));
        RateLimiter::for('cancel', fn (Request $request) => Limit::perMinute(5)->by($byIp($request)));
        RateLimiter::for('bug-report', fn (Request $request) => Limit::perMinutes(10, 5)->by($byIp($request)));
        RateLimiter::for('scheduler', fn (Request $request) => Limit::perMinute(5)->by($byIp($request)));
        RateLimiter::for('public-api', fn (Request $request) => Limit::perMinute(60)->by($byIp($request)));
    }
}
