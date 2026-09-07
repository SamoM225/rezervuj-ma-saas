<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Temporary slot holds moved to the cache store (see config/booking.php). */
    public function up(): void
    {
        Schema::dropIfExists('slot_holds');
    }

    public function down(): void
    {
        Schema::create('slot_holds', function (Blueprint $table) {
            $table->id();
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
};
