<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates settings and business_settings tables with default values
     */
    public function up(): void
    {
        // Create settings table
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key');
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Create business_settings table
        Schema::create('business_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key');
            $table->text('value');
            $table->string('type')->default('string');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Insert default business settings
        $now = now();
        DB::table('business_settings')->insert([
            [
                'key' => 'booking_advance_days',
                'value' => '30',
                'type' => 'integer',
                'description' => 'Koľko dní dopredu si môžu zákazníci rezervovať termíny',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'key' => 'booking_advance_weeks',
                'value' => '4',
                'type' => 'integer',
                'description' => 'Koľko týždňov dopredu si môžu zákazníci rezervovať termíny',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'key' => 'booking_advance_months',
                'value' => '1',
                'type' => 'integer',
                'description' => 'Koľko mesiacov dopredu si môžu zákazníci rezervovať termíny',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'key' => 'default_booking_duration',
                'value' => '60',
                'type' => 'integer',
                'description' => 'Predvolené trvanie rezervácie v minútach',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'key' => 'show_pending_status',
                'value' => '0',
                'type' => 'boolean',
                'description' => 'Show pending booking status in admin dashboard',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'key' => 'service_buffer_minutes',
                'value' => '15',
                'type' => 'integer',
                'description' => 'Predvolená pauza medzi procedúrami v minútach, ak ju služba nešpecifikuje',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'key' => 'max_daily_slots_per_customer',
                'value' => '0',
                'type' => 'integer',
                'description' => 'Maximálny počet rezervácií na zákazníka a deň (0 = bez limitu)',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'key' => 'min_cancel_hours',
                'value' => '6',
                'type' => 'integer',
                'description' => 'Minimálny počet hodín pred začiatkom rezervácie, kedy je možné zrušenie',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'key' => 'business_holidays',
                'value' => json_encode([]),
                'type' => 'json',
                'description' => 'Zoznam dátumov, kedy je prevádzka úplne zatvorená',
                'created_at' => $now,
                'updated_at' => $now
            ],
        ]);

        // Insert default settings
        DB::table('settings')->insert([
            [
                'key' => 'booking_bg_color',
                'value' => '#ffffff',
                'created_at' => $now,
                'updated_at' => $now,
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_settings');
        Schema::dropIfExists('settings');
    }
};
