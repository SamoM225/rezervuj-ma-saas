<?php

namespace App\Http\Controllers;

use App\Models\BusinessSetting;
use App\Support\PlanLimits;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Booking rules of the business: opening hours, how far ahead customers may
 * book, buffers, limits, confirmation flow and languages.
 */
class BusinessSettingController extends Controller
{
    /** key => [type, default] */
    public const FIELDS = [
        'business_start_time' => ['string', '08:00'],
        'business_end_time' => ['string', '18:00'],
        'booking_advance_days' => ['integer', 60],
        'booking_advance_hours' => ['integer', 2],
        'service_buffer_minutes' => ['integer', 0],
        'slot_hold_minutes' => ['integer', 5],
        'min_cancel_hours' => ['integer', 0],
        'reminder_hours_before' => ['integer', 24],
        'max_daily_slots_per_customer' => ['integer', 0],
        'max_bookings_per_worker_per_day' => ['integer', 0],
        'allow_online_booking' => ['boolean', true],
        'require_confirmation' => ['boolean', false],
        'show_pending_status' => ['boolean', false],
        'send_email_notifications' => ['boolean', true],
        'enforce_fixed_start_times' => ['boolean', false],
        'language_switcher_enabled' => ['boolean', false],
        'default_language' => ['string', 'sk'],
        'available_languages' => ['json', ['sk']],
        'business_holidays' => ['json', []],
    ];

    public function index()
    {
        $settings = [];
        foreach (self::FIELDS as $key => [$type, $default]) {
            $settings[$key] = BusinessSetting::get($key, $default);
        }

        return view('admin.business-settings', ['settings' => $settings]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'business_start_time' => 'required|date_format:H:i',
            'business_end_time' => 'required|date_format:H:i|after:business_start_time',
            'booking_advance_days' => 'required|integer|min:1|max:365',
            'booking_advance_hours' => 'required|integer|min:0|max:168',
            'service_buffer_minutes' => 'required|integer|min:0|max:180',
            'slot_hold_minutes' => 'required|integer|min:1|max:60',
            'min_cancel_hours' => 'nullable|integer|min:0|max:240',
            'reminder_hours_before' => 'required|integer|min:1|max:168',
            'max_daily_slots_per_customer' => 'nullable|integer|min:0|max:20',
            'max_bookings_per_worker_per_day' => 'nullable|integer|min:0|max:100',
            'allow_online_booking' => 'boolean',
            'require_confirmation' => 'boolean',
            'show_pending_status' => 'boolean',
            'send_email_notifications' => 'boolean',
            'enforce_fixed_start_times' => 'boolean',
            'language_switcher_enabled' => 'boolean',
            'default_language' => 'required|string|in:sk,en,cs',
            'available_languages' => 'nullable|array',
            'available_languages.*' => 'string|in:sk,en,cs',
            'business_holidays' => 'nullable|string',
        ]);

        $holidays = collect(preg_split('/[\s,;]+/', (string) ($data['business_holidays'] ?? '')))
            ->map(fn ($date) => trim($date))
            ->filter()
            ->unique()
            ->values();

        foreach ($holidays as $date) {
            if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) || Carbon::createFromFormat('Y-m-d', $date)->format('Y-m-d') !== $date) {
                return back()->withErrors([
                    'business_holidays' => "Dátum „{$date}“ nie je platný. Použite formát RRRR-MM-DD, napríklad 2025-12-24.",
                ])->withInput();
            }
        }

        $multilingual = PlanLimits::currentAllows('multilingual_booking_page');
        $languages = $multilingual
            ? collect($data['available_languages'] ?? [])->push($data['default_language'])->unique()->values()->all()
            : [$data['default_language']];

        foreach (self::FIELDS as $key => [$type, $default]) {
            $value = match ($type) {
                'boolean' => $key === 'language_switcher_enabled' && ! $multilingual ? false : $request->boolean($key),
                'integer' => (int) ($data[$key] ?? $default),
                'json' => $key === 'business_holidays' ? $holidays->sort()->values()->all() : $languages,
                default => (string) ($data[$key] ?? $default),
            };
            BusinessSetting::set($key, $value, $type);
        }

        return redirect()->route('admin.business-settings')->with('success', __('ui.nastavenia_rezervacii_boli_ulozene'));
    }
}
