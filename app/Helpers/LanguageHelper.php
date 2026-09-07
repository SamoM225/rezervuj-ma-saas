<?php

namespace App\Helpers;

use App\Models\BusinessSetting;
use Carbon\Carbon;

class LanguageHelper
{
    /**
     * All supported locales with their names
     */
    public static array $locales = [
        'sk' => [
            'name' => 'Slovenčina',
            'native' => 'Slovenčina',
            'flag' => '🇸🇰',
            'code' => 'sk-SK',
        ],
        'en' => [
            'name' => 'English',
            'native' => 'English',
            'flag' => '🇬🇧',
            'code' => 'en-US',
        ],
        'cs' => [
            'name' => 'Čeština',
            'native' => 'Čeština',
            'flag' => '🇨🇿',
            'code' => 'cs-CZ',
        ],
    ];

    /**
     * Get translated text with optional fallback
     */
    public static function trans(string $key, array $replace = [], ?string $locale = null): string
    {
        return trans($key, $replace, $locale ?? app()->getLocale());
    }

    /**
     * Get current locale
     */
    public static function getCurrentLocale(): string
    {
        return app()->getLocale();
    }

    /**
     * Get current locale info
     */
    public static function getCurrentLocaleInfo(): array
    {
        $locale = self::getCurrentLocale();

        return self::$locales[$locale] ?? self::$locales['sk'];
    }

    /**
     * Get available languages for this business
     */
    public static function getAvailableLanguages(): array
    {
        try {
            $setting = BusinessSetting::get('available_languages', null);

            if ($setting === null) {
                return [self::getDefaultLanguage()]; // not configured: the business' own language only
            }

            $languages = is_array($setting) ? $setting : json_decode($setting, true);

            return is_array($languages) && ! empty($languages) ? $languages : ['sk'];
        } catch (\Exception $e) {
            return ['sk'];
        }
    }

    /**
     * Get available languages with full info
     */
    public static function getAvailableLanguagesWithInfo(): array
    {
        $available = self::getAvailableLanguages();
        $result = [];

        foreach ($available as $locale) {
            if (isset(self::$locales[$locale])) {
                $result[$locale] = self::$locales[$locale];
            }
        }

        return $result;
    }

    /**
     * Get all supported languages with full info
     */
    public static function getAllLanguages(): array
    {
        return self::$locales;
    }

    /**
     * Check if a locale is available for this business
     */
    public static function isLanguageAvailable(string $locale): bool
    {
        return in_array($locale, self::getAvailableLanguages(), true);
    }

    /**
     * Get default language for this business
     */
    public static function getDefaultLanguage(): string
    {
        try {
            return BusinessSetting::get('default_language', 'sk');
        } catch (\Exception $e) {
            return 'sk';
        }
    }

    /**
     * Check if language switcher should be shown
     */
    public static function shouldShowSwitcher(): bool
    {
        try {
            // Only show if enabled and more than 1 language available
            $enabled = BusinessSetting::get('language_switcher_enabled', true);
            $languages = self::getAvailableLanguages();

            return $enabled && count($languages) > 1;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get language switcher position
     */
    public static function getSwitcherPosition(): string
    {
        try {
            return BusinessSetting::get('language_switcher_position', 'header');
        } catch (\Exception $e) {
            return 'header';
        }
    }

    /**
     * Get locale name in its native language
     */
    public static function getLocaleName(string $locale): string
    {
        return self::$locales[$locale]['native'] ?? $locale;
    }

    /**
     * Get locale flag emoji
     */
    public static function getLocaleFlag(string $locale): string
    {
        return self::$locales[$locale]['flag'] ?? '';
    }

    /**
     * Format date according to current locale
     */
    public static function formatDate(\DateTime|Carbon $date, string $format = 'medium'): string
    {
        $locale = self::getCurrentLocale();

        $formats = [
            'sk' => [
                'short' => 'd.m.Y',
                'medium' => 'd. F Y',
                'long' => 'l, d. F Y',
            ],
            'en' => [
                'short' => 'm/d/Y',
                'medium' => 'F d, Y',
                'long' => 'l, F d, Y',
            ],
            'cs' => [
                'short' => 'd.m.Y',
                'medium' => 'd. F Y',
                'long' => 'l, d. F Y',
            ],
        ];

        $dateFormat = $formats[$locale][$format] ?? $formats['sk'][$format];

        return $date->translatedFormat($dateFormat);
    }

    /**
     * Format time according to current locale
     */
    public static function formatTime(\DateTime|Carbon $time): string
    {
        $locale = self::getCurrentLocale();

        // Most European locales use 24-hour format
        return $locale === 'en' ? $time->format('g:i A') : $time->format('H:i');
    }
}
