<?php

namespace App\Support;

use App\Models\Booking;
use App\Models\City;
use App\Models\Tenant;

/**
 * Plan enforcement. Free tenants get 50 bookings per calendar month, one
 * location and the standard look; Pro removes the limits (config/tenancy.php).
 * Cancelled bookings do not count, so a customer cancelling frees the slot again.
 */
final class PlanLimits
{
    public static function monthlyBookingLimit(Tenant $tenant): ?int
    {
        $limit = $tenant->planLimits()['bookings_per_month'] ?? null;

        return $limit === null ? null : (int) $limit;
    }

    public static function bookingsThisMonth(Tenant $tenant): int
    {
        return Booking::acrossTenants()
            ->where('tenant_id', $tenant->id)
            ->where('status', '!=', 'cancelled')
            ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->count();
    }

    public static function canCreateBooking(Tenant $tenant): bool
    {
        $limit = self::monthlyBookingLimit($tenant);

        return $limit === null || self::bookingsThisMonth($tenant) < $limit;
    }

    /** Remaining bookings this month, or null when unlimited. */
    public static function remainingBookings(Tenant $tenant): ?int
    {
        $limit = self::monthlyBookingLimit($tenant);

        return $limit === null ? null : max(0, $limit - self::bookingsThisMonth($tenant));
    }

    /** Boolean plan features: custom_branding, multilingual_booking_page, embed_widget. */
    public static function allows(Tenant $tenant, string $feature): bool
    {
        return (bool) ($tenant->planLimits()[$feature] ?? false);
    }

    public static function maxLocations(Tenant $tenant): ?int
    {
        $max = $tenant->planLimits()['locations'] ?? null;

        return $max === null ? null : (int) $max;
    }

    public static function canAddLocation(Tenant $tenant): bool
    {
        $max = self::maxLocations($tenant);

        return $max === null || City::acrossTenants()->where('tenant_id', $tenant->id)->count() < $max;
    }

    /** Convenience for controllers and views running inside a tenant request. */
    public static function currentAllows(string $feature): bool
    {
        $tenant = Tenancy::current();

        return $tenant !== null && self::allows($tenant, $feature);
    }

    public static function currentCanAddLocation(): bool
    {
        $tenant = Tenancy::current();

        return $tenant !== null && self::canAddLocation($tenant);
    }
}
