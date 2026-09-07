<?php

namespace App\Http\Controllers;

use App\Models\BusinessSetting;
use App\Support\EmailLayoutBuilder;
use App\Support\PlanLimits;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EmailTemplateController extends Controller
{
    /**
     * Show the email template editor.
     */
    public function index()
    {
        $configJson = BusinessSetting::get('email_template_config');
        $config = is_string($configJson) ? json_decode($configJson, true) : $configJson;

        if (! $config || ! is_array($config)) {
            $config = EmailLayoutBuilder::defaultConfig();
        }

        // Merge with defaults to ensure new keys are always present
        $defaults = EmailLayoutBuilder::defaultConfig();
        $config = array_replace_recursive($defaults, $config);

        return view('admin.email-templates.index', [
            'config' => $config,
            'configJson' => json_encode($config, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
        ]);
    }

    /**
     * Save the email template configuration.
     */
    public function update(Request $request)
    {
        if (! PlanLimits::currentAllows('custom_branding')) {
            return back()->with('error', __('admin.pro.email_templates'));
        }

        $request->validate([
            'config' => 'required|string',
        ]);

        $config = json_decode($request->input('config'), true);

        if (! $config || ! is_array($config)) {
            return back()->withErrors(['config' => __('ui.neplatna_konfiguracia_json')]);
        }

        // Validate color format
        if (! empty($config['colors'])) {
            foreach ($config['colors'] as $key => $color) {
                if (! preg_match('/^#[0-9a-fA-F]{6}$/', $color)) {
                    return back()->withErrors(['config' => "Neplatná farba pre '{$key}': {$color}"]);
                }
            }
        }

        BusinessSetting::set('email_template_config', $config, 'json', 'Konfigurácia e-mailovej šablóny');

        return back()->with('success', __('ui.e_mailova_sablona_bola_uspesne_ulozena'));
    }

    /**
     * Render a preview of the email template.
     */
    public function preview(Request $request)
    {
        $configJson = $request->input('config');
        $config = is_string($configJson) ? json_decode($configJson, true) : null;

        if (! $config || ! is_array($config)) {
            $config = EmailLayoutBuilder::defaultConfig();
        }

        $type = $request->input('type', 'confirmed');

        $businessName = BusinessSetting::get('business_name', config('app.name'));
        $businessAddress = BusinessSetting::get('business_address', 'SNP 14/A, Šurany');
        $supportEmail = BusinessSetting::get('support_email', 'info@example.com');
        $supportPhone = BusinessSetting::get('support_phone', '+421 900 123 456');

        $logoPath = BusinessSetting::get('business_logo_path');
        $logoUrl = $logoPath ? url(Storage::url($logoPath)) : null;

        $builder = new EmailLayoutBuilder(
            businessName: $businessName,
            businessAddress: $businessAddress,
            supportEmail: $supportEmail,
            supportPhone: $supportPhone,
            accentColor: BusinessSetting::get('email_accent_color', '#D98AA8'),
            logoUrl: $logoUrl,
        );
        $builder->applyConfig($config);

        // Use configured texts or defaults
        $greeting = $builder->configText($type, 'greeting', 'Dobrý deň, Ján Novák!');
        $introText = $builder->configText($type, 'intro', match ($type) {
            'confirmed' => "Vaša rezervácia v {$businessName} bola potvrdená.",
            'cancelled' => 'Vaša rezervácia bola zrušená.',
            'pending' => 'Vaša rezervácia bola prijatá a čaká na potvrdenie.',
            'reminder' => 'Pripomíname vám vašu blížiacu sa rezerváciu.',
            default => '',
        });

        // Replace placeholders in texts
        $placeholders = [
            '{{customer_name}}' => 'Ján Novák',
            '{{business_name}}' => $businessName,
            '{{service_name}}' => 'Strihanie + Umytie',
            '{{worker_name}}' => 'Mária Kováčová',
            '{{booking_date}}' => now()->addDays(3)->format('d.m.Y'),
            '{{booking_start_time}}' => '14:00',
            '{{booking_end_time}}' => '15:00',
            '{{booking_price}}' => '25,00 €',
        ];

        $greeting = str_replace(array_keys($placeholders), array_values($placeholders), $greeting);
        $introText = str_replace(array_keys($placeholders), array_values($placeholders), $introText);

        $details = [
            'Dátum a čas:' => $placeholders['{{booking_date}}'].', '.$placeholders['{{booking_start_time}}'],
            'Rezervácia na:' => 'Ján Novák',
            'Služba:' => 'Strihanie + Umytie',
            'Pracovník:' => 'Mária Kováčová',
            'Cena:' => '25,00 €',
        ];

        $calendarUrls = [
            'google' => '#',
            'apple' => '#',
            'outlook' => '#',
        ];

        $guestDetails = [
            'name' => 'Ján Novák',
            'phone' => '+421 900 111 222',
            'email' => 'jan@example.com',
        ];

        $cancelUrl = '#cancel-demo';

        $html = $builder->build(
            greeting: $greeting,
            introText: $introText,
            details: $details,
            calendarUrls: $calendarUrls,
            guestDetails: $guestDetails,
            cancelUrl: $cancelUrl,
            type: $type,
        );

        return response($html)->header('Content-Type', 'text/html');
    }

    /**
     * Reset configuration to defaults.
     */
    public function reset()
    {
        if (! PlanLimits::currentAllows('custom_branding')) {
            return back()->with('error', __('admin.pro.email_templates'));
        }

        $defaults = EmailLayoutBuilder::defaultConfig();
        BusinessSetting::set('email_template_config', $defaults, 'json', 'Konfigurácia e-mailovej šablóny');

        return back()->with('success', __('ui.e_mailova_sablona_bola_resetovana_na'));
    }
}
