<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cities', function (Blueprint $table) {
            // Full street address per location (for "add to calendar" + ICS).
            $table->string('address')->nullable()->after('name');
        });

        Schema::table('bookings', function (Blueprint $table) {
            // Which location (city) the booking was made for.
            $table->foreignId('city_id')->nullable()->after('service_id')->constrained('cities')->nullOnDelete();
            // GDPR consent record.
            $table->timestamp('gdpr_consent_at')->nullable()->after('status');
            $table->string('gdpr_policy_version', 20)->nullable()->after('gdpr_consent_at');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropConstrainedForeignId('city_id');
            $table->dropColumn(['gdpr_consent_at', 'gdpr_policy_version']);
        });

        Schema::table('cities', function (Blueprint $table) {
            $table->dropColumn('address');
        });
    }
};
