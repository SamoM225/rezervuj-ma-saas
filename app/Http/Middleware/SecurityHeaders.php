<?php

namespace App\Http\Middleware;

use App\Models\BusinessSetting;
use App\Support\Tenancy;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Security headers to protect against common web vulnerabilities.
     * These headers should be applied to all responses in production.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Prevent MIME type sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Marketing pages, legal hub, directory and public tenant profiles are
        // indexable; the booking flow, auth and staff areas are not (the profile
        // is the canonical public page of a business).
        $staffAreas = ['admin', 'worker', 'login', 'logout', '2fa', 'account', 'platform', 'billing'];
        $segments = $request->segments();
        $privateAreas = array_merge($staffAreas, ['booking', 'booking-confirmation', 'web', 'api', 'register', 'forgot-password', 'reset-password', 'widget.js', 'bug-report', 'language', 'webhooks', 'internal']);
        $isPrivate = in_array($segments[0] ?? '', $privateAreas, true) || in_array($segments[1] ?? '', $privateAreas, true);
        if ($isPrivate) {
            $response->headers->set('X-Robots-Tag', 'noindex, nofollow');
        }

        // Clickjacking: staff pages are never framed; the public booking flow may be
        // embedded by the websites listed in the admin ("Widget" settings).
        // Paths are /{tenant}/admin/… in the SaaS, /admin/… on the platform itself.
        $embeddable = ! in_array($segments[0] ?? '', $staffAreas, true) && ! in_array($segments[1] ?? '', $staffAreas, true);
        $frameAncestors = "'self'";
        if ($embeddable && Tenancy::check()) {
            $origins = collect(preg_split('/[\s,]+/', (string) BusinessSetting::get('embed_allowed_origins', '')))
                ->filter(fn ($origin) => preg_match('#^https?://[a-z0-9.-]+(:\d+)?$#i', $origin))
                ->unique();
            if ($origins->isNotEmpty()) {
                $frameAncestors .= ' '.$origins->implode(' ');
            }
        }
        if ($frameAncestors === "'self'") {
            $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        }

        // Enable XSS filtering in browsers
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Control referrer information
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Permissions Policy (formerly Feature-Policy)
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');

        // HSTS only makes sense on an actual HTTPS response.
        if (app()->environment('production') && $request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        // Content Security Policy. Inline scripts/styles are used by Blade views
        // and Alpine; cdn.jsdelivr.net serves Chart.js and the QR code library.
        $csp = implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://www.paypal.com https://www.sandbox.paypal.com",
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com",
            "font-src 'self' data: https://fonts.gstatic.com", // data: = FullCalendar's bundled icon font
            "img-src 'self' data: blob: https:",
            "connect-src 'self' https://www.paypal.com https://www.sandbox.paypal.com",
            "frame-src 'self' https://www.paypal.com https://www.sandbox.paypal.com",
            "object-src 'none'",
            "base-uri 'self'",
            "form-action 'self'",
            'frame-ancestors '.$frameAncestors,
        ]);

        // Use Content-Security-Policy-Report-Only first to test
        // Then switch to Content-Security-Policy when confident
        if (app()->environment('production')) {
            $response->headers->set('Content-Security-Policy', $csp);
        } else {
            $response->headers->set('Content-Security-Policy-Report-Only', $csp);
        }

        return $response;
    }
}
