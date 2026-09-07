<?php

namespace App\Http\Middleware;

use App\Support\Locales;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

/**
 * Platform pages take their language from the URL prefix (/cs, /en; none = sk).
 * The prefix is also registered as a URL default so route() keeps working.
 */
class SetPlatformLocale
{
    public function handle(Request $request, Closure $next, string $locale = Locales::DEFAULT): Response
    {
        if (! in_array($locale, Locales::SUPPORTED, true)) {
            abort(404);
        }

        app()->setLocale($locale);
        Carbon::setLocale($locale);
        $request->attributes->set('platform_locale', $locale);

        return $next($request);
    }
}
