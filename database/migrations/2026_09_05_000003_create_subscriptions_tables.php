<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('provider', 20)->default('paypal');
            $table->string('provider_id', 64)->unique();      // PayPal subscription id (I-XXXX)
            $table->string('plan_key', 32);                   // eur_monthly | eur_yearly | usd_monthly | usd_yearly
            $table->string('status', 32);                     // APPROVAL_PENDING | APPROVED | ACTIVE | SUSPENDED | CANCELLED | EXPIRED
            $table->char('currency', 3);
            $table->decimal('amount', 8, 2);
            $table->timestamp('current_period_end')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->json('raw')->nullable();
            $table->timestamps();
            $table->index(['tenant_id', 'status']);
        });

        Schema::create('payment_events', function (Blueprint $table) {
            $table->id();
            $table->string('provider', 20)->default('paypal');
            $table->string('event_id', 64)->unique();
            $table->string('event_type', 64);
            $table->string('subscription_provider_id', 64)->nullable()->index();
            $table->unsignedBigInteger('tenant_id')->nullable()->index();
            $table->json('payload');
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_events');
        Schema::dropIfExists('subscriptions');
    }
};
