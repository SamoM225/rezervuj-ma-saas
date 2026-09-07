<?php

namespace App\Support;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

/**
 * Cron-less maintenance helper for shared hosting without a system cron.
 * Runs the GDPR data-retention purge at most once per calendar day, using a
 * file marker (no DB/cache dependency) so concurrent requests are safe.
 */
class ScheduledMaintenance
{
    public static function purgeIfDue(): void
    {
        try {
            $marker = storage_path('app/maintenance_purge_last_run');
            $today = now()->toDateString();

            if (is_file($marker) && trim((string) @file_get_contents($marker)) === $today) {
                return;
            }

            // Claim today's run first (double runs are harmless — the purge is
            // idempotent — but this keeps it to ~once/day).
            @file_put_contents($marker, $today, LOCK_EX);

            Artisan::call('bookings:purge-expired');
        } catch (\Throwable $e) {
            Log::error('ScheduledMaintenance: purge failed', ['error' => $e->getMessage()]);
        }
    }
}
