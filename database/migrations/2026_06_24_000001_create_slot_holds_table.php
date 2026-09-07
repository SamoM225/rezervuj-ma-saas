<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Temporary slot holds: when a customer picks a time slot it is reserved
     * for a few minutes (default 5) so nobody else can grab it while they
     * finish the booking. Rows are filtered/cleaned by `expires_at`.
     */
    public function up(): void
    {
        Schema::create('slot_holds', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable()->index();
            $table->foreignId('worker_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('service_id')->nullable()->constrained('services')->nullOnDelete();
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('session_token', 100)->index();
            $table->timestamp('expires_at')->index();
            $table->timestamps();

            $table->index(['worker_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('slot_holds');
    }
};
