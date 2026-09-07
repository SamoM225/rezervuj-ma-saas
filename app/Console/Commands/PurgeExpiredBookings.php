<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\BusinessSetting;
use App\Models\Tenant;
use App\Support\Tenancy;
use Illuminate\Console\Command;

/**
 * GDPR data retention: permanently deletes bookings (and their personal data)
 * older than the retention period. Runs per tenant; a tenant may shorten the
 * platform default with the `booking_retention_days` business setting.
 */
class PurgeExpiredBookings extends Command
{
    protected $signature = 'bookings:purge-expired {--dry-run : Only report what would be deleted}';

    protected $description = 'Delete bookings older than the GDPR retention period (all tenants)';

    public function handle(): int
    {
        $total = 0;

        Tenant::query()->each(function (Tenant $tenant) use (&$total) {
            $total += Tenancy::runAs($tenant, function () use ($tenant) {
                $default = (int) config('gdpr.retention_days', 730);
                $days = (int) BusinessSetting::get('booking_retention_days', $default);
                $days = max(1, min($days, $default));
                $cutoff = now()->subDays($days)->toDateString();

                // Include soft-deleted rows: retention means the data is gone, not hidden.
                $query = Booking::withTrashed()->where('date', '<', $cutoff);
                $count = (clone $query)->count();

                if ($this->option('dry-run')) {
                    $this->line("[dry-run] {$tenant->slug}: {$count} bookings older than {$days} days (before {$cutoff}).");

                    return 0;
                }

                $query->forceDelete();
                if ($count > 0) {
                    $this->line("{$tenant->slug}: deleted {$count} bookings older than {$days} days.");
                }

                return $count;
            });
        });

        $this->info($this->option('dry-run') ? 'Dry run finished.' : "Deleted {$total} bookings in total.");

        return self::SUCCESS;
    }
}
