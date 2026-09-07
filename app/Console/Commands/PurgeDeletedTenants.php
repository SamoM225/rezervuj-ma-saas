<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

/**
 * Removes tenants whose owner deleted the account more than 30 days ago (the
 * data retrieval period promised in the DPA). Rows in every tenant-scoped
 * table go with the tenant through cascading foreign keys; uploaded files are
 * removed explicitly.
 */
class PurgeDeletedTenants extends Command
{
    protected $signature = 'tenants:purge-deleted {--days=30 : Grace period after the deletion request} {--dry-run : Only report}';

    protected $description = 'Permanently delete tenant accounts whose grace period after deletion has passed';

    public function handle(): int
    {
        $cutoff = now()->subDays(max(0, (int) $this->option('days')));
        $tenants = Tenant::query()->where('status', Tenant::STATUS_DELETED)->whereNotNull('deleted_at')->where('deleted_at', '<=', $cutoff)->get();

        foreach ($tenants as $tenant) {
            if ($this->option('dry-run')) {
                $this->line("[dry-run] would purge {$tenant->slug} (deleted {$tenant->deleted_at->toDateString()})");

                continue;
            }
            Storage::disk('public')->deleteDirectory('tenants/'.$tenant->id);
            $tenant->delete();
            $this->line("purged {$tenant->slug}");
        }

        $this->info($tenants->count().' tenant(s) '.($this->option('dry-run') ? 'due for purge' : 'purged').'.');

        return self::SUCCESS;
    }
}
