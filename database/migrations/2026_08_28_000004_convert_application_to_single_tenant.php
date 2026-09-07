<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The application was deliberately converted from a multi-tenant SaaS
     * into one installation for one business. This migration:
     *
     *  1. picks the "primary" tenant (the real business) when the database
     *     still holds several tenants, removes the other tenants' records
     *     (demo data) and collapses per-tenant settings into one namespace;
     *  2. drops every tenant discriminator column, the tenants tables and the
     *     leftover global-admin flag.
     *
     * Business records of the primary tenant are retained untouched.
     */
    public function up(): void
    {
        if (Schema::hasTable('tenants')) {
            $this->consolidateData();
        }

        $sqlite = DB::getDriverName() === 'sqlite';

        if (Schema::hasColumn('users', 'current_tenant_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropConstrainedForeignId('current_tenant_id');
            });
        }
        if (Schema::hasColumn('users', 'is_global_admin')) {
            Schema::table('users', fn (Blueprint $table) => $table->dropColumn('is_global_admin'));
        }

        // Tables where tenant_id leads a composite UNIQUE index. MariaDB backs the
        // foreign key with that index, so the order must be: FK -> index -> column.
        // SQLite cannot drop a foreign key by name; its FKs disappear with the column.
        foreach ([
            'business_settings' => ['business_settings_tenant_id_foreign', 'business_settings_tenant_key_unique'],
            'settings' => ['settings_tenant_id_foreign', 'settings_tenant_key_unique'],
            'blacklist_entries' => ['blacklist_entries_tenant_id_foreign', 'blacklist_unique_identifier'],
        ] as $tableName => [$foreignKey, $uniqueIndex]) {
            if (!Schema::hasColumn($tableName, 'tenant_id')) {
                continue;
            }
            Schema::table($tableName, function (Blueprint $table) use ($sqlite, $foreignKey, $uniqueIndex) {
                // SQLite rebuilds the table for a column-based dropForeign, which
                // is what lets the subsequent native "drop column" succeed.
                $table->dropForeign($sqlite ? ['tenant_id'] : $foreignKey);
                $table->dropUnique($uniqueIndex);
                $table->dropColumn('tenant_id');
            });
        }

        // One installation has exactly one value per setting key.
        Schema::table('settings', fn (Blueprint $table) => $table->unique('key'));
        Schema::table('business_settings', fn (Blueprint $table) => $table->unique('key'));
        Schema::table('blacklist_entries', function (Blueprint $table) {
            $table->unique(['identifier_type', 'identifier_value'], 'blacklist_unique_identifier');
        });

        // Tables with a plain tenant_id foreign key.
        foreach ([
            'bookings', 'services', 'categories', 'schedules',
            'worker_availability', 'cities', 'customers',
        ] as $tableName) {
            if (Schema::hasColumn($tableName, 'tenant_id')) {
                Schema::table($tableName, fn (Blueprint $table) => $table->dropConstrainedForeignId('tenant_id'));
            }
        }

        if (Schema::hasColumn('slot_holds', 'tenant_id')) {
            Schema::table('slot_holds', function (Blueprint $table) {
                $table->dropIndex('slot_holds_tenant_id_index');
                $table->dropColumn('tenant_id');
            });
        }

        if (Schema::hasTable('integrations') && Schema::hasColumn('integrations', 'tenant_id')) {
            Schema::table('integrations', function (Blueprint $table) {
                $table->dropUnique(['tenant_id', 'provider']);
                $table->dropColumn('tenant_id');
                $table->unique('provider');
            });
        }

        // A worker must survive the removal of their location; the original
        // schema cascaded the delete.
        if (!$sqlite) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropForeign(['city_id']);
                $table->foreign('city_id')->references('id')->on('cities')->nullOnDelete();
            });
        }

        Schema::dropIfExists('tenant_user');
        Schema::dropIfExists('tenants');
    }

    /**
     * Keep the primary tenant's data, remove the other tenants' demo records and
     * collapse duplicated setting keys so `Setting::get()` / `BusinessSetting::get()`
     * resolve to the business' own values.
     */
    private function consolidateData(): void
    {
        $tenants = DB::table('tenants')->orderBy('id')->get();
        if ($tenants->isEmpty()) {
            return;
        }

        $primaryId = $this->primaryTenantId($tenants);
        $otherIds = $tenants->pluck('id')->map(fn ($id) => (int) $id)->reject(fn ($id) => $id === $primaryId)->values()->all();

        $this->note(sprintf(
            'Single-tenant conversion: keeping tenant #%d (%s)%s.',
            $primaryId,
            $tenants->firstWhere('id', $primaryId)->slug ?? '?',
            $otherIds ? ', removing tenant(s) #'.implode(', #', $otherIds) : ''
        ));

        if ($otherIds) {
            // Accounts that only ever belonged to the removed tenants are demo accounts.
            $primaryMembers = DB::table('tenant_user')->where('tenant_id', $primaryId)->pluck('user_id')->map(fn ($id) => (int) $id);
            $demoUsers = DB::table('tenant_user')->whereIn('tenant_id', $otherIds)->pluck('user_id')
                ->map(fn ($id) => (int) $id)->unique()->diff($primaryMembers)->values();

            if (Schema::hasColumn('users', 'is_global_admin')) {
                $globalAdmins = DB::table('users')->where('is_global_admin', true)->pluck('id')->map(fn ($id) => (int) $id);
                $demoUsers = $demoUsers->diff($globalAdmins)->values();
            }

            foreach (['bookings', 'schedules', 'worker_availability'] as $tableName) {
                DB::table($tableName)->whereIn('user_id', $demoUsers)->delete();
            }
            foreach (['service_user', 'category_user'] as $pivot) {
                if (Schema::hasTable($pivot)) {
                    DB::table($pivot)->whereIn('user_id', $demoUsers)->delete();
                }
            }
            DB::table('users')->whereIn('id', $demoUsers)->delete();

            // Locations: detach staff first so the cascading FK cannot take workers with it.
            $demoCities = DB::table('cities')->whereIn('tenant_id', $otherIds)->pluck('id');
            DB::table('users')->whereIn('city_id', $demoCities)->update(['city_id' => null]);

            foreach ([
                'bookings', 'slot_holds', 'schedules', 'worker_availability', 'blacklist_entries',
                'customers', 'integrations', 'services', 'categories', 'cities',
                'business_settings', 'settings',
            ] as $tableName) {
                if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'tenant_id')) {
                    DB::table($tableName)->whereIn('tenant_id', $otherIds)->delete();
                }
            }

            // Pivot rows whose parent vanished (databases without FK enforcement).
            if (Schema::hasTable('category_city')) {
                DB::table('category_city')->whereNotIn('category_id', DB::table('categories')->select('id'))->delete();
                DB::table('category_city')->whereNotIn('city_id', DB::table('cities')->select('id'))->delete();
            }
            if (Schema::hasTable('category_service')) {
                DB::table('category_service')->whereNotIn('category_id', DB::table('categories')->select('id'))->delete();
                DB::table('category_service')->whereNotIn('service_id', DB::table('services')->select('id'))->delete();
            }
        }

        // The primary tenant's settings win over the shared (tenant-less) defaults;
        // any remaining duplicate keeps its most recently updated row.
        foreach (['settings', 'business_settings'] as $tableName) {
            $primaryKeys = DB::table($tableName)->where('tenant_id', $primaryId)->pluck('key');
            DB::table($tableName)->whereNull('tenant_id')->whereIn('key', $primaryKeys)->delete();

            $duplicates = DB::table($tableName)->select('key')->groupBy('key')->havingRaw('COUNT(*) > 1')->pluck('key');
            foreach ($duplicates as $key) {
                $keep = DB::table($tableName)->where('key', $key)->orderByDesc('updated_at')->orderByDesc('id')->value('id');
                DB::table($tableName)->where('key', $key)->where('id', '!=', $keep)->delete();
            }
        }

        if (Schema::hasTable('blacklist_entries')) {
            $duplicates = DB::table('blacklist_entries')
                ->select('identifier_type', 'identifier_value')
                ->groupBy('identifier_type', 'identifier_value')
                ->havingRaw('COUNT(*) > 1')
                ->get();
            foreach ($duplicates as $row) {
                $keep = DB::table('blacklist_entries')
                    ->where('identifier_type', $row->identifier_type)
                    ->where('identifier_value', $row->identifier_value)
                    ->orderByDesc('updated_at')->orderByDesc('id')->value('id');
                DB::table('blacklist_entries')
                    ->where('identifier_type', $row->identifier_type)
                    ->where('identifier_value', $row->identifier_value)
                    ->where('id', '!=', $keep)->delete();
            }
        }
    }

    /**
     * The business we keep: an explicit PRIMARY_TENANT_SLUG, otherwise the
     * tenant carrying the most real content (services, bookings, settings).
     */
    private function primaryTenantId(Collection $tenants): int
    {
        if ($tenants->count() === 1) {
            return (int) $tenants->first()->id;
        }

        $preferred = env('PRIMARY_TENANT_SLUG');
        if ($preferred && ($match = $tenants->firstWhere('slug', $preferred))) {
            return (int) $match->id;
        }

        $scores = $tenants->mapWithKeys(fn ($tenant) => [(int) $tenant->id =>
            DB::table('services')->where('tenant_id', $tenant->id)->count() * 3
            + DB::table('bookings')->where('tenant_id', $tenant->id)->count() * 2
            + DB::table('business_settings')->where('tenant_id', $tenant->id)->count(),
        ]);
        $best = $scores->max();

        return (int) $scores->filter(fn ($score) => $score === $best)->keys()->sort()->first();
    }

    private function note(string $message): void
    {
        if (app()->runningInConsole() && !app()->runningUnitTests()) {
            fwrite(STDOUT, "  {$message}\n");
        }
    }

    /**
     * A downgrade cannot faithfully restore a multi-tenant ownership model.
     * Restoring empty structural columns would risk implying safe isolation,
     * therefore this migration is intentionally irreversible.
     */
    public function down(): void
    {
        throw new LogicException('The single-tenant conversion is irreversible. Restore a database backup to roll it back.');
    }
};
