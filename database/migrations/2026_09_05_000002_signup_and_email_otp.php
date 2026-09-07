<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Self-service signup, e-mail one-time codes at login and a log of accepted
 * legal documents (who accepted which version, when, from where).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('email_verified_at')->nullable()->after('email');
            $table->boolean('email_otp_enabled')->default(true)->after('two_factor_confirmed_at');
        });

        Schema::table('verification_codes', function (Blueprint $table) {
            $table->string('purpose', 20)->default('signup')->after('email')->index();
            $table->unsignedBigInteger('user_id')->nullable()->after('purpose')->index();
        });

        Schema::create('legal_acceptances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->cascadeOnDelete();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('email');
            $table->string('document', 40);   // terms | dpa | privacy | controller_declaration | age | accuracy | marketing
            $table->string('version', 20);
            $table->string('ip', 45)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->timestamp('accepted_at');
            $table->index(['tenant_id', 'document']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('legal_acceptances');
        Schema::table('verification_codes', function (Blueprint $table) {
            $table->dropColumn(['purpose', 'user_id']);
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['email_verified_at', 'email_otp_enabled']);
        });
    }
};
