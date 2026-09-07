<?php

namespace App\Support;

use Carbon\Carbon;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\Facades\Cache;

/**
 * Temporary reservations of a time slot while a visitor completes the form.
 *
 * Holds are kept in a cache store (Redis on the VPS, the database cache on
 * shared hosting) under one key per worker and day, so a conflict check is a
 * single read. Every write goes through an atomic lock and entries expire on
 * their own, so nothing needs a cleanup job.
 */
class SlotHolds
{
    /**
     * @return array{token: string, service_id: int|null, start: string, end: string, expires_at: string}|null
     *         The new hold, or null when the slot is already held by someone else.
     */
    public static function place(int $workerId, ?int $serviceId, string $date, string $start, string $end, string $token, int $minutes): ?array
    {
        return self::withLock($workerId, $date, function () use ($workerId, $serviceId, $date, $start, $end, $token, $minutes) {
            $holds = array_values(array_filter(self::active($workerId, $date), fn ($hold) => $hold['token'] !== $token));

            foreach ($holds as $hold) {
                if (self::overlaps($hold, $start, $end)) {
                    return null;
                }
            }

            $hold = [
                'token' => $token,
                'service_id' => $serviceId,
                'start' => $start,
                'end' => $end,
                'expires_at' => now()->addMinutes($minutes)->toIso8601String(),
            ];
            $holds[] = $hold;
            self::write($workerId, $date, $holds, $minutes);

            return $hold;
        });
    }

    /** Whether somebody other than $exceptToken currently holds a slot overlapping the range. */
    public static function conflicts(int $workerId, string $date, string $start, string $end, ?string $exceptToken = null): bool
    {
        foreach (self::active($workerId, $date) as $hold) {
            if ($hold['token'] !== $exceptToken && self::overlaps($hold, $start, $end)) {
                return true;
            }
        }

        return false;
    }

    /** Drop every hold of one visitor for the given worker and day (after they booked). */
    public static function release(int $workerId, string $date, string $token): void
    {
        self::withLock($workerId, $date, function () use ($workerId, $date, $token) {
            $holds = array_values(array_filter(self::active($workerId, $date), fn ($hold) => $hold['token'] !== $token));
            self::write($workerId, $date, $holds, 10);
        });
    }

    /** @return array<int, array{token: string, service_id: int|null, start: string, end: string, expires_at: string}> */
    public static function active(int $workerId, string $date): array
    {
        $holds = self::store()->get(self::key($workerId, $date), []);

        return array_values(array_filter(is_array($holds) ? $holds : [], fn ($hold) => Carbon::parse($hold['expires_at'])->isFuture()));
    }

    public static function store(): Repository
    {
        return Cache::store(config('booking.hold_store'));
    }

    private static function write(int $workerId, string $date, array $holds, int $minutes): void
    {
        $key = self::key($workerId, $date);
        if ($holds === []) {
            self::store()->forget($key);

            return;
        }
        // The key lives as long as its longest hold, plus a small margin.
        self::store()->put($key, $holds, now()->addMinutes($minutes + 1));
    }

    private static function withLock(int $workerId, string $date, callable $callback): mixed
    {
        return self::store()->lock('lock:'.self::key($workerId, $date), 5)->block(3, $callback);
    }

    private static function key(int $workerId, string $date): string
    {
        return "slot-holds:{$workerId}:{$date}";
    }

    private static function overlaps(array $hold, string $start, string $end): bool
    {
        return $hold['start'] < $end && $hold['end'] > $start;
    }
}
