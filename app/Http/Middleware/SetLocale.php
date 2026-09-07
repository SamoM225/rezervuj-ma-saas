<?php

namespace App\Http\Middleware;

use App\Models\BusinessSetting;
use App\Support\Tenancy;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Supported locales
     */
    private array $supportedLocales = ['sk', 'en', 'cs'];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $this->determineLocale($request);

        // Set the application locale
        app()->setLocale($locale);

        // Store in session for persistence (staff and customers separately)
        session([$this->isStaffArea($request) ? 'admin_locale' : 'locale' => $locale]);

        // Set Carbon locale for date formatting
        Carbon::setLocale($locale);

        return $next($request);
    }

    /**
     * Determine the locale to use based on various sources
     */
    private function determineLocale(Request $request): string
    {
        // Staff area: the signed-in person's own choice, independent of the languages
        // the booking page offers to customers.
        if ($this->isStaffArea($request)) {
            foreach ([$request->get('locale'), session('admin_locale'), $request->cookie('admin_locale')] as $candidate) {
                if (is_string($candidate) && in_array($candidate, $this->supportedLocales, true)) {
                    return $candidate;
                }
            }
            $default = (string) $this->getDefaultLanguage();

            return in_array($default, $this->supportedLocales, true) ? $default : 'sk';
        }

        // 1. Check URL parameter (highest priority for switching)
        if ($request->has('locale') && $this->isValidLocale($request->get('locale'))) {
            return $request->get('locale');
        }

        // 2. Check session
        if (session()->has('locale') && $this->isValidLocale(session('locale'))) {
            return session('locale');
        }

        // 3. Check cookie
        if ($request->cookie('locale') && $this->isValidLocale($request->cookie('locale'))) {
            return $request->cookie('locale');
        }

        // 4. Check Accept-Language header
        $browserLocale = $this->getLocaleFromBrowser($request);
        if ($browserLocale && $this->isValidLocale($browserLocale)) {
            return $browserLocale;
        }

        // 5. Use the business' default language
        $defaultLocale = $this->getDefaultLanguage();
        if ($defaultLocale && $this->isValidLocale($defaultLocale)) {
            return $defaultLocale;
        }

        // 6. Fallback to Slovak
        return 'sk';
    }

    /**
     * Check if locale is valid and available for this business
     */
    private function isValidLocale(string $locale): bool
    {
        // Check if locale is in supported locales
        if (! in_array($locale, $this->supportedLocales, true)) {
            return false;
        }

        // Check if locale is available for this business
        $availableLanguages = $this->getAvailableLanguages();

        return in_array($locale, $availableLanguages, true);
    }

    /**
     * Get the business' available languages from BusinessSettings
     */
    private function getAvailableLanguages(): array
    {
        if (! Tenancy::check()) {
            return $this->supportedLocales;
        }

        try {
            $setting = BusinessSetting::get('available_languages', null);

            if ($setting === null) {
                // Not configured yet: offer only the business' own language, exactly
                // like signup does. Anything else would let the browser's
                // Accept-Language flip a Slovak salon into English.
                return [$this->getDefaultLanguage() ?? 'sk'];
            }

            $languages = is_array($setting) ? $setting : json_decode($setting, true);

            return is_array($languages) && ! empty($languages) ? $languages : ['sk'];
        } catch (\Exception $e) {
            return ['sk'];
        }
    }

    /**
     * Get the business' default language from BusinessSettings
     */
    private function getDefaultLanguage(): ?string
    {
        if (! Tenancy::check()) {
            return config('tenancy.default_locale', 'sk');
        }

        try {
            return BusinessSetting::get('default_language', Tenancy::current()?->locale ?? 'sk');
        } catch (\Exception $e) {
            return 'sk';
        }
    }

    /**
     * Extract locale from browser's Accept-Language header
     */
    private function getLocaleFromBrowser(Request $request): ?string
    {
        $acceptLanguage = $request->header('Accept-Language');

        if (! $acceptLanguage) {
            return null;
        }

        // Parse Accept-Language header
        $locales = [];
        $parts = explode(',', $acceptLanguage);

        foreach ($parts as $part) {
            $part = trim($part);
            $segments = explode(';', $part);
            $locale = strtolower(substr($segments[0], 0, 2));

            $quality = 1.0;
            if (isset($segments[1]) && preg_match('/q=([\d.]+)/', $segments[1], $matches)) {
                $quality = (float) $matches[1];
            }

            $locales[$locale] = $quality;
        }

        arsort($locales);

        foreach (array_keys($locales) as $locale) {
            if (in_array($locale, $this->supportedLocales, true)) {
                return $locale;
            }
        }

        return null;
    }

    /** Admin, staff and sign-in pages under the tenant prefix. */
    private function isStaffArea(Request $request): bool
    {
        return in_array($request->segment(2), ['admin', 'worker', 'account', 'login', 'logout', 'two-factor', '2fa', 'forgot-password', 'reset-password'], true);
    }
}
