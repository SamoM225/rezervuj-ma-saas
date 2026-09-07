<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates blacklist_entries table for sophisticated customer blacklisting
     */
    public function up(): void
    {
        Schema::create('blacklist_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            
            // Identifier - can be email or phone (not name - multiple people can have same name)
            $table->enum('identifier_type', ['email', 'phone'])->index();
            $table->string('identifier_value')->index();
            
            // Severity levels
            // - warning: Just a note, booking still allowed
            // - soft_ban: Booking blocked but can be overridden by admin
            // - hard_ban: Booking completely blocked
            $table->enum('severity', ['warning', 'soft_ban', 'hard_ban'])->default('soft_ban');
            
            // Reason categories for quick filtering
            $table->enum('reason_category', [
                'no_show',           // Neprišiel na rezerváciu
                'late_cancellation', // Zrušil neskoro
                'repeated_cancellation', // Opakované rušenie
                'bad_behavior',      // Nevhodné správanie
                'payment_issue',     // Problém s platbou
                'fraud',             // Podvod
                'spam',              // Spam/Falošné rezervácie
                'other'              // Iné
            ])->default('other');
            
            // Detailed reason / notes
            $table->text('reason')->nullable();
            $table->text('internal_notes')->nullable();
            
            // Expiration for temporary bans (null = permanent)
            $table->timestamp('expires_at')->nullable();
            
            // Status
            $table->boolean('is_active')->default(true);
            
            // Audit trail
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            
            // Statistics
            $table->unsignedInteger('violation_count')->default(1);
            $table->timestamp('last_violation_at')->nullable();
            
            $table->timestamps();
            
            // Composite unique constraint - same identifier can't be blacklisted twice for same tenant
            $table->unique(['tenant_id', 'identifier_type', 'identifier_value'], 'blacklist_unique_identifier');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blacklist_entries');
    }
};


