<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates tenants table and adds tenant_id to all relevant tables
     */
    public function up(): void
    {
        // Create tenants table
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('domain')->nullable()->unique();
            $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status')->default('active');
            $table->string('stripe_customer_id')->nullable()->index();
            $table->string('subscription_plan')->nullable()->index();
            $table->string('subscription_status')->default('free')->index();
            $table->timestamp('subscription_synced_at')->nullable();
            $table->timestamps();
        });

        // Create tenant_user pivot table
        Schema::create('tenant_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('role')->default('member');
            $table->timestamps();
            $table->unique(['tenant_id', 'user_id']);
        });

        // Add current_tenant_id to users table
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('current_tenant_id')->nullable()->after('is_global_admin')->constrained('tenants')->nullOnDelete();
        });

        // Add tenant_id to all relevant tables
        $tables = [
            'bookings',
            'services',
            'categories',
            'schedules',
            'worker_availability',
            'cities',
            'business_settings',
            'settings',
        ];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->foreignId('tenant_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
            });
        }

        // Update unique indexes for settings tables to be unique per tenant
        Schema::table('business_settings', function (Blueprint $table) {
            $table->unique(['tenant_id', 'key'], 'business_settings_tenant_key_unique');
        });

        Schema::table('settings', function (Blueprint $table) {
            $table->unique(['tenant_id', 'key'], 'settings_tenant_key_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove tenant_id from all tables
        $tables = [
            'bookings',
            'services',
            'categories',
            'schedules',
            'worker_availability',
            'cities',
            'business_settings',
            'settings',
        ];

        Schema::table('settings', function (Blueprint $table) {
            $table->dropUnique('settings_tenant_key_unique');
        });

        Schema::table('business_settings', function (Blueprint $table) {
            $table->dropUnique('business_settings_tenant_key_unique');
        });

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropConstrainedForeignId('tenant_id');
            });
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('current_tenant_id');
        });

        Schema::dropIfExists('tenant_user');
        Schema::dropIfExists('tenants');
    }
};
