<?php

namespace App\Http\Controllers;

use App\Helpers\LanguageHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LanguageController extends Controller
{
    /**
     * Switch the application language
     */
    public function switch(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'locale' => 'required|string|in:sk,en,cs',
        ]);

        $locale = $validated['locale'];
        $referer = $request->header('referer');
        $redirectUrl = ($referer && parse_url($referer, PHP_URL_HOST) === $request->getHost()) ? $referer : url('/');

        // Staff pick their own admin language; it does not touch the booking page.
        if ($request->boolean('admin')) {
            session()->put('admin_locale', $locale);
            app()->setLocale($locale);

            return redirect($redirectUrl)->cookie('admin_locale', $locale, 60 * 24 * 365);
        }

        if (! LanguageHelper::isLanguageAvailable($locale)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('common.error'),
                    'error' => 'This language is not enabled.',
                ], 400);
            }

            return back()->with('error', __('common.error'));
        }

        session()->put('locale', $locale);
        session()->save();
        app()->setLocale($locale);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'locale' => $locale,
                'message' => __('common.saved_successfully'),
            ]);
        }

        // Stay on the page the visitor was reading; only same-host referers are accepted (see above).
        return redirect($redirectUrl)->cookie('locale', $locale, 60 * 24 * 365);
    }

    /**
     * Get available languages (API endpoint)
     */
    public function available(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'current' => LanguageHelper::getCurrentLocale(),
            'default' => LanguageHelper::getDefaultLanguage(),
            'available' => LanguageHelper::getAvailableLanguagesWithInfo(),
            'show_switcher' => LanguageHelper::shouldShowSwitcher(),
            'switcher_position' => LanguageHelper::getSwitcherPosition(),
        ]);
    }

    /**
     * Get all supported languages (API endpoint)
     */
    public function all(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'languages' => LanguageHelper::getAllLanguages(),
        ]);
    }
}
