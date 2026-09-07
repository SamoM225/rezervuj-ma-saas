<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Turns the single-business schema into a multi-tenant one (fresh installs
 * only: the SaaS database starts empty, so no backfill is needed).
 *
 * Every business-owned table gets a `tenant_id`; uniqueness that used to be
 * global becomes per tenant.
 */
return new class extends Migration
{
    /** Tables that belong to exactly one tenant. */
    private const TENANT_TABLES = [
        'users', 'cities', 'categories', 'services', 'bookings', 'schedules',
        'worker_availability', 'settings', 'business_settings', 'blacklist_entries',
    ];

    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 60)->unique();
            $table->string('name');
            $table->string('category', 40)->default('other');
            $table->char('country', 2)->default('SK');
            $table->string('city')->nullable();
            $table->string('address')->nullable();
            $table->string('phone', 40)->nullable();
            $table->string('email')->nullable();
            $table->text('description')->nullable();
            $table->string('locale', 5)->default('sk');
            $table->string('timezone', 64)->default('Europe/Bratislava');
            $table->char('currency', 3)->default('EUR');
            $table->string('plan', 20)->default('free');
            $table->string('status', 20)->default('active'); // active | suspended
            $table->boolean('is_public')->default(true);   // listed in directory + indexable
            $table->unsignedBigInteger('owner_user_id')->nullable();
            $table->timestamp('pro_until')->nullable();
            $table->timestamps();
            $table->index(['status', 'is_public']);
            $table->index(['country', 'category']);
        });

        // SQLite (tests) cannot add a NOT NULL column in one step, so: add nullable,
        // drop the pre-seeded single-business rows (theme/feature defaults live in
        // code now), tighten to NOT NULL, then add the foreign key.
        foreach (self::TENANT_TABLES as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->unsignedBigInteger('tenant_id')->nullable()->after('id');
            });
            DB::table($tableName)->whereNull('tenant_id')->delete();
            Schema::table($tableName, function (Blueprint $table) {
                $table->unsignedBigInteger('tenant_id')->nullable(false)->change();
            });
            Schema::table($tableName, function (Blueprint $table) {
                $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();
            });
        }

        // Uniqueness moves from "one installation" to "one tenant".
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique('users_email_unique');
            $table->unique(['tenant_id', 'email']);
        });
        Schema::table('cities', function (Blueprint $table) {
            $table->dropUnique('cities_name_unique');
            $table->unique(['tenant_id', 'name']);
        });
        Schema::table('settings', function (Blueprint $table) {
            $table->dropUnique('settings_key_unique');
            $table->unique(['tenant_id', 'key']);
        });
        Schema::table('business_settings', function (Blueprint $table) {
            $table->dropUnique('business_settings_key_unique');
            $table->unique(['tenant_id', 'key']);
        });
        Schema::table('blacklist_entries', function (Blueprint $table) {
            $table->dropUnique('blacklist_unique_identifier');
            $table->unique(['tenant_id', 'identifier_type', 'identifier_value'], 'blacklist_unique_identifier');
        });

        // Hot paths: calendar feeds and availability per worker/day, customer lookups,
        // monthly plan-limit counts.
        Schema::table('bookings', function (Blueprint $table) {
            $table->index(['tenant_id', 'user_id', 'date']);
            $table->index(['tenant_id', 'customer_email']);
            $table->index(['tenant_id', 'created_at']);
        });
        Schema::table('schedules', function (Blueprint $table) {
            $table->index(['tenant_id', 'user_id', 'date']);
        });

        // Dormant tables from the prototype era; nothing reads them.
        Schema::dropIfExists('integration_mappings');
        Schema::dropIfExists('integration_sync_logs');
        Schema::dropIfExists('integrations');
        Schema::dropIfExists('customers');
    }

    public function down(): void
    {
        // Reversing would drop populated tenant data; use a forward migration instead.
        throw new LogicException('add_multi_tenancy cannot be rolled back.');
    }
};
