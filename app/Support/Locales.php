<?php

namespace App\Support;

use Illuminate\Support\Facades\Route;

/**
 * Locale-prefixed platform URLs. Slovak lives at the root, other locales under
 * /cs and /en. Platform routes are registered once per locale with the route
 * name prefixed by the locale ("sk.register", "cs.register", …).
 */
final class Locales
{
    public const SUPPORTED = ['sk', 'cs', 'en'];

    public const DEFAULT = 'sk';

    /** BCP-47 tags for hreflang / og:locale. */
    public const TAGS = ['sk' => 'sk-SK', 'cs' => 'cs-CZ', 'en' => 'en'];

    public static function prefix(string $locale): string
    {
        return $locale === self::DEFAULT ? '' : $locale;
    }

    /** URL of a platform route in the given (or current) locale. */
    public static function route(string $name, array $params = [], ?string $locale = null, bool $absolute = true): string
    {
        $locale ??= app()->getLocale();
        if (! in_array($locale, self::SUPPORTED, true)) {
            $locale = self::DEFAULT;
        }

        // The locale is a literal URI prefix of the route group, not a parameter.
        return route("{$locale}.{$name}", $params, $absolute);
    }

    /** Same page in every locale, for hreflang and the language switcher. @return array<string, string> */
    public static function alternates(?string $name = null, array $params = []): array
    {
        $current = Route::current();
        if ($name === null) {
            $name = $current ? preg_replace('/^(sk|cs|en)\./', '', (string) $current->getName()) : 'home';
            $params = $current ? array_diff_key($current->parameters(), ['locale' => true]) : [];
        }
        $urls = [];
        foreach (self::SUPPORTED as $locale) {
            if (Route::has("{$locale}.{$name}")) {
                $urls[$locale] = self::route($name, $params, $locale);
            }
        }

        return $urls;
    }

    /** Best locale for a first visit without a prefix, from Accept-Language. */
    public static function negotiate(?string $acceptLanguage): string
    {
        foreach (explode(',', (string) $acceptLanguage) as $part) {
            $tag = strtolower(substr(trim(explode(';', $part)[0]), 0, 2));
            if (in_array($tag, self::SUPPORTED, true)) {
                return $tag;
            }
        }

        return self::DEFAULT;
    }
}
