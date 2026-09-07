<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('integrations', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->nullable();
            $table->string('provider'); // bookio, reservio, etc.
            $table->string('api_key')->nullable();
            $table->string('api_secret')->nullable();
            $table->string('facility_id')->nullable();
            $table->boolean('is_active')->default(false);
            $table->boolean('sync_services')->default(true);
            $table->boolean('sync_workers')->default(true);
            $table->boolean('sync_reservations')->default(true);
            $table->string('sync_direction')->default('both'); // import, export, both
            $table->json('settings')->nullable();
            $table->json('field_mapping')->nullable();
            $table->timestamp('last_sync_at')->nullable();
            $table->string('last_sync_status')->nullable();
            $table->text('last_sync_error')->nullable();
            $table->timestamps();
            
            $table->unique(['tenant_id', 'provider']);
            $table->index('provider');
            $table->index('is_active');
        });
        
        // Table for tracking synced items
        Schema::create('integration_sync_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('integration_id')->constrained()->onDelete('cascade');
            $table->string('entity_type'); // service, worker, reservation
            $table->string('local_id')->nullable();
            $table->string('remote_id')->nullable();
            $table->string('action'); // created, updated, deleted, synced
            $table->string('direction'); // import, export
            $table->json('data')->nullable();
            $table->string('status'); // success, failed
            $table->text('error_message')->nullable();
            $table->timestamps();
            
            $table->index(['integration_id', 'entity_type']);
            $table->index(['local_id', 'remote_id']);
        });
        
        // Mapping table for synced entities
        Schema::create('integration_mappings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('integration_id')->constrained()->onDelete('cascade');
            $table->string('entity_type'); // service, worker, reservation
            $table->string('local_id');
            $table->string('remote_id');
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->unique(['integration_id', 'entity_type', 'local_id']);
            $table->unique(['integration_id', 'entity_type', 'remote_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('integration_mappings');
        Schema::dropIfExists('integration_sync_logs');
        Schema::dropIfExists('integrations');
    }
};
