<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\BusinessSetting;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration adds all feature toggle settings to business_settings table.
     * Each feature has a master toggle that can be enabled/disabled by the tenant owner.
     */
    public function up(): void
    {
        // Feature settings to add
        $featureSettings = [
            // Multi-language (FREE) - PRIORITA #1
            [
                'key' => 'default_language',
                'value' => 'sk',
                'type' => 'string',
                'description' => 'Východzí jazyk'
            ],
            [
                'key' => 'available_languages',
                'value' => json_encode(['sk']),
                'type' => 'json',
                'description' => 'Dostupné jazyky'
            ],
            [
                'key' => 'language_switcher_enabled',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Zobraziť language switcher'
            ],
            [
                'key' => 'language_switcher_position',
                'value' => 'header',
                'type' => 'string',
                'description' => 'Pozícia switchera (header/footer/both)'
            ],
            
            // API Access (FREE) - Pre migráciu z iných systémov
            [
                'key' => 'api_access_enabled',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Povoliť API prístup'
            ],
            
            // Reminders (FREE)
            [
                'key' => 'reminders_enabled',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Master toggle pre automatické pripomienky'
            ],
            [
                'key' => 'reminder_24h_enabled',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Povoliť 24h pripomienku'
            ],
            [
                'key' => 'reminder_2h_enabled',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Povoliť 2h pripomienku'
            ],
            
            // Customer Portal (PRO)
            [
                'key' => 'customer_portal_enabled',
                'value' => '0',
                'type' => 'boolean',
                'description' => 'Povoliť zákaznícky portál'
            ],
            [
                'key' => 'customer_portal_token_expiry_days',
                'value' => '30',
                'type' => 'integer',
                'description' => 'Platnosť tokenu (dní)'
            ],
            [
                'key' => 'customer_can_cancel_bookings',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Zákazníci môžu zrušiť rezervácie'
            ],
            [
                'key' => 'customer_can_reschedule_bookings',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Zákazníci môžu zmeniť termín'
            ],
            [
                'key' => 'cancellation_deadline_hours',
                'value' => '24',
                'type' => 'integer',
                'description' => 'Deadline pre zrušenie (hodiny pred rezerváciou)'
            ],
            
            // Recurring Bookings (PRO)
            [
                'key' => 'recurring_bookings_enabled',
                'value' => '0',
                'type' => 'boolean',
                'description' => 'Povoliť opakované rezervácie'
            ],
            [
                'key' => 'recurring_max_occurrences',
                'value' => '52',
                'type' => 'integer',
                'description' => 'Maximálny počet opakovaní'
            ],
            [
                'key' => 'recurring_allow_customer_creation',
                'value' => '0',
                'type' => 'boolean',
                'description' => 'Zákazníci môžu vytvárať opakované rezervácie'
            ],
            
            // Waitlist (PRO)
            [
                'key' => 'waitlist_enabled',
                'value' => '0',
                'type' => 'boolean',
                'description' => 'Povoliť čakaciu listu'
            ],
            [
                'key' => 'waitlist_max_entries_per_customer',
                'value' => '3',
                'type' => 'integer',
                'description' => 'Max počet waitlist entries na zákazníka'
            ],
            [
                'key' => 'waitlist_notification_expiry_hours',
                'value' => '2',
                'type' => 'integer',
                'description' => 'Expiračný čas notifikácie (hodiny)'
            ],
            [
                'key' => 'waitlist_auto_notify_enabled',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Automatické notifikácie pri zrušení'
            ],
            
            // Analytics (PRO)
            [
                'key' => 'analytics_enabled',
                'value' => '0',
                'type' => 'boolean',
                'description' => 'Povoliť Analytics Dashboard'
            ],
            [
                'key' => 'analytics_retention_days',
                'value' => '365',
                'type' => 'integer',
                'description' => 'Uchovávať dáta (dní)'
            ],
            [
                'key' => 'analytics_show_revenue',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Zobraziť Revenue grafy'
            ],
            [
                'key' => 'analytics_show_worker_performance',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Zobraziť Worker Performance'
            ],
            [
                'key' => 'analytics_email_reports_enabled',
                'value' => '0',
                'type' => 'boolean',
                'description' => 'Automatické Email Reporty'
            ],
            [
                'key' => 'analytics_email_report_frequency',
                'value' => '',
                'type' => 'string',
                'description' => 'Frekvencia reportov (daily/weekly/monthly)'
            ],
            
            // Online Payments (PRO)
            [
                'key' => 'online_payments_enabled',
                'value' => '0',
                'type' => 'boolean',
                'description' => 'Povoliť online platby'
            ],
            [
                'key' => 'payment_required',
                'value' => '0',
                'type' => 'boolean',
                'description' => 'Platba je povinná'
            ],
            [
                'key' => 'payment_deposit_percent',
                'value' => '0',
                'type' => 'integer',
                'description' => 'Percento zálohy (0-100)'
            ],
            [
                'key' => 'payment_deposit_minimum',
                'value' => '0',
                'type' => 'decimal',
                'description' => 'Minimálna záloha (EUR)'
            ],
            [
                'key' => 'refund_on_cancellation',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Automatický refund pri zrušení'
            ],
            [
                'key' => 'refund_deadline_hours',
                'value' => '24',
                'type' => 'integer',
                'description' => 'Deadline pre refund (hodiny)'
            ],
            
            // Reviews (FREE)
            [
                'key' => 'reviews_enabled',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Povoliť recenzie a hodnotenia'
            ],
            [
                'key' => 'reviews_require_moderation',
                'value' => '0',
                'type' => 'boolean',
                'description' => 'Vyžadovať moderáciu recenzií'
            ],
            [
                'key' => 'reviews_auto_publish_hours',
                'value' => '0',
                'type' => 'integer',
                'description' => 'Auto-publish po (hodinách)'
            ],
            [
                'key' => 'reviews_min_rating',
                'value' => '1',
                'type' => 'integer',
                'description' => 'Minimálne hodnotenie'
            ],
            [
                'key' => 'reviews_allow_anonymous',
                'value' => '0',
                'type' => 'boolean',
                'description' => 'Povoliť anonymné recenzie'
            ],
            [
                'key' => 'reviews_email_reminder_enabled',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Odoslať email reminder'
            ],
            [
                'key' => 'reviews_email_reminder_days',
                'value' => '1',
                'type' => 'integer',
                'description' => 'Odoslať reminder po (dňoch)'
            ],
            
            // Booking Templates (PRO)
            [
                'key' => 'booking_templates_enabled',
                'value' => '0',
                'type' => 'boolean',
                'description' => 'Povoliť Booking Templates'
            ],
        ];
        
        // A single installation has one global settings namespace.
        foreach ($featureSettings as $setting) {
            BusinessSetting::updateOrCreate(
                ['key' => $setting['key']],
                ['value' => $setting['value'], 'type' => $setting['type']]
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Optionally remove feature settings
        // For safety, we'll leave them in place but you can delete if needed
    }
};
