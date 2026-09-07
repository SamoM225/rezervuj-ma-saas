<?php

use App\Http\Middleware\AntiBot;
use App\Http\Middleware\Authenticate;
use App\Http\Middleware\CheckBlacklist;
use App\Http\Middleware\ResolveTenant;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\RunDueMaintenance;
use App\Http\Middleware\SecurityHeaders;
use App\Http\Middleware\SetLocale;
use App\Http\Middleware\SetPlatformLocale;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Routing\Middleware\ThrottleRequests;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // The app sits behind a local reverse proxy (Cloudflare Tunnel / nginx), so
        // honour X-Forwarded-* headers for the scheme and client IP.
        $middleware->trustProxies(at: '*');

        $middleware->alias([
            'role' => RoleMiddleware::class,
            'auth' => Authenticate::class,
            'locale' => SetLocale::class,
            'blacklist' => CheckBlacklist::class,
            'antibot' => AntiBot::class,
            'tenant' => ResolveTenant::class,
            'platform.locale' => SetPlatformLocale::class,
        ]);

        // Signed webhooks from payment providers carry no CSRF token.
        $middleware->validateCsrfTokens(except: ['webhooks/*']);

        // Security headers for all responses
        $middleware->append(SecurityHeaders::class);

        // Locale is applied per route group (after the tenant is resolved), see routes/web.php.

        // Rate limiting for web routes. Generous because the booking flow fires
        // many AJAX calls (cities/categories/services/workers/dates/times/hold);
        // sensitive endpoints (login, booking, hold) have their own tighter limits.
        $middleware->appendToGroup('web', ThrottleRequests::class.':180,1');

        // Cron-less daily maintenance (data-retention purge) triggered by traffic.
        $middleware->appendToGroup('web', RunDueMaintenance::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
