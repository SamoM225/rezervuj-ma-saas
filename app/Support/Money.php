<?php

namespace App\Support;

/**
 * Formats prices in the current tenant's currency. Kept deliberately small:
 * the platform shows a price next to a service, it does not do accounting.
 */
class Money
{
    /** Currencies customarily written without decimals. */
    private const WHOLE = ['CZK', 'HUF', 'PLN', 'JPY'];

    /** @var array<string, string> */
    private const SYMBOLS = ['EUR' => '€', 'CZK' => 'Kč', 'HUF' => 'Ft', 'PLN' => 'zł', 'GBP' => '£', 'USD' => '$', 'CHF' => 'CHF'];

    public static function currency(): string
    {
        return strtoupper((string) (Tenancy::current()?->currency ?: 'EUR'));
    }

    public static function format(float $amount, ?string $currency = null): string
    {
        $currency = strtoupper($currency ?: self::currency());
        $decimals = in_array($currency, self::WHOLE, true) ? 0 : 2;
        $number = number_format($amount, $decimals, ',', ' ');
        $symbol = self::SYMBOLS[$currency] ?? $currency;

        return in_array($currency, ['GBP', 'USD'], true) ? $symbol.$number : $number.' '.$symbol;
    }
}
