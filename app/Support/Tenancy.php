<?php

namespace App\Support;

use App\Models\BusinessSetting;
use App\Models\Setting;
use App\Models\Tenant;
use Illuminate\Support\Facades\URL;

/**
 * Holds the tenant of the current request / job. Set by the ResolveTenant
 * middleware for web requests and by runAs() for queued jobs and commands.
 *
 * When no tenant is set, tenant-owned models are NOT scoped — that is only
 * legitimate in platform-level code (signup, platform admin, tenant loops).
 */
final class Tenancy
{
    private static ?Tenant $current = null;

    public static function current(): ?Tenant
    {
        return self::$current;
    }

    public static function id(): ?int
    {
        return self::$current?->id;
    }

    public static function check(): bool
    {
        return self::$current !== null;
    }

    private static ?string $platformTimezone = null;

    public static function set(Tenant $tenant): void
    {
        self::$current = $tenant;
        self::useTimezone($tenant->timezone ?: null);
        BusinessSetting::forgetCache();
        Setting::forgetCache();
        URL::defaults(['tenant' => $tenant->slug]);
    }

    public static function forget(): void
    {
        self::$current = null;
        self::useTimezone(null);
        BusinessSetting::forgetCache();
        Setting::forgetCache();
    }

    /**
     * Run a callback with the given tenant as current, restoring the previous
     * one afterwards. Used by jobs and scheduled commands that loop tenants.
     *
     * @template T
     *
     * @param  callable(Tenant): T  $callback
     * @return T
     */
    public static function runAs(Tenant $tenant, callable $callback): mixed
    {
        $previous = self::$current;
        self::set($tenant);

        try {
            return $callback($tenant);
        } finally {
            if ($previous) {
                self::set($previous);
            } else {
                self::forget();
            }
        }
    }

    /**
     * Dates and times are stored naive (date + time columns), so "now" must be
     * the tenant's local time. Null restores the platform default.
     */
    private static function useTimezone(?string $timezone): void
    {
        self::$platformTimezone ??= (string) config('app.timezone', 'UTC');
        $timezone = $timezone && in_array($timezone, timezone_identifiers_list(), true) ? $timezone : self::$platformTimezone;
        config(['app.timezone' => $timezone]);
        date_default_timezone_set($timezone);
    }
}
