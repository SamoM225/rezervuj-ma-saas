<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BusinessSetting;
use App\Models\Category;
use App\Models\Service;
use App\Models\Setting;
use App\Models\User;
use App\Support\PlanLimits;
use App\Support\Tenancy;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    /** Business identity and public-page appearance: key => [type, default]. */
    public const APPEARANCE = [
        'business_name' => ['string', null],
        'business_owner' => ['string', ''],
        'business_registration' => ['string', ''],
        'embed_allowed_origins' => ['string', ''],
        'business_address' => ['string', ''],
        'support_email' => ['string', ''],
        'support_phone' => ['string', ''],
        'opening_hours_text' => ['string', ''],
        'logo_width' => ['integer', 180],
        'hero_overlay_color' => ['string', '#1f1a14'],
        'hero_overlay_opacity' => ['float', 0.55],
        'show_service_images' => ['boolean', true],
        'show_worker_avatars' => ['boolean', true],
        'privacy_effective_from' => ['string', ''],
    ];

    public function dashboard()
    {
        $today = Carbon::today();
        $base = Booking::query();
        $requireConfirmation = (bool) BusinessSetting::get('require_confirmation', false);
        $showPendingStatus = (bool) BusinessSetting::get('show_pending_status', false);
        $last30 = (clone $base)->whereBetween('date', [$today->copy()->subDays(29), $today]);
        $last30Count = (clone $last30)->count();

        $stats = [
            'total_workers' => User::where('role', 'worker')->count(),
            'total_bookings' => (clone $base)->count(),
            'total_categories' => Category::count(),
            'total_services' => Service::count(),
            'today_bookings' => (clone $base)->whereDate('date', $today)->where('status', '!=', 'cancelled')->count(),
            'pending_bookings' => (clone $base)->where('status', 'pending')->count(),
            'confirmed_bookings' => (clone $base)->where('status', 'confirmed')->count(),
            'completed_bookings' => (clone $base)->where('status', 'completed')->count(),
            'cancelled_bookings' => (clone $base)->where('status', 'cancelled')->count(),
            'this_week_bookings' => (clone $base)->whereBetween('date', [$today->copy()->startOfWeek(), $today->copy()->endOfWeek()])->where('status', '!=', 'cancelled')->count(),
            'upcoming_week_bookings' => (clone $base)->whereBetween('date', [$today, $today->copy()->addDays(6)])->where('status', '!=', 'cancelled')->count(),
            'revenue_last_30' => (clone $base)->whereIn('bookings.status', ['confirmed', 'completed'])->whereBetween('bookings.date', [$today->copy()->subDays(29), $today])->join('services', 'bookings.service_id', '=', 'services.id')->sum('services.price'),
            'cancellation_rate' => $last30Count ? round(((clone $last30)->where('status', 'cancelled')->count() / $last30Count) * 100) : 0,
        ];

        $bookingChartData = collect(range(13, 0))->map(fn (int $offset) => [
            'date' => $today->copy()->subDays($offset)->format('j. n.'),
            'bookings' => Booking::whereDate('date', $today->copy()->subDays($offset))->where('status', '!=', 'cancelled')->count(),
        ])->all();

        $upcoming = Booking::with(['worker:id,name,calendar_color', 'service:id,name,duration'])
            ->where('status', '!=', 'cancelled')
            ->where(fn ($query) => $query
                ->where('date', '>', $today->toDateString())
                ->orWhere(fn ($q) => $q->where('date', $today->toDateString())->where('end_time', '>=', now()->format('H:i:s'))))
            ->orderBy('date')
            ->orderBy('start_time')
            ->limit(8)
            ->get();

        return view('admin.dashboard', [
            'stats' => $stats,
            'upcoming_bookings' => $upcoming,
            'recent_bookings' => Booking::with(['worker:id,name,calendar_color', 'service:id,name'])->latest()->limit(6)->get(),
            'booking_chart_data' => $bookingChartData,
            'top_workers' => User::where('role', 'worker')
                ->withCount(['bookings' => fn ($q) => $q->where('status', '!=', 'cancelled')->whereBetween('date', [$today->copy()->subDays(29), $today])])
                ->orderByDesc('bookings_count')
                ->limit(5)
                ->get(),
            'requireConfirmation' => $requireConfirmation,
            'showPendingStatus' => $showPendingStatus,
        ]);
    }

    public function settings()
    {
        $settings = Setting::getThemeColors();
        foreach (self::APPEARANCE as $key => [$type, $default]) {
            $settings[$key] = BusinessSetting::get($key, $key === 'business_name' ? config('app.name') : $default);
        }
        $settings['business_logo_path'] = BusinessSetting::get('business_logo_path');
        $settings['hero_background_image_path'] = BusinessSetting::get('hero_background_image_path');

        return view('admin.settings', ['settings' => $settings]);
    }

    public function updateSettings(Request $request)
    {
        $data = $request->validate([
            'main_accent' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'text_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'booking_bg_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'business_name' => 'required|string|max:255',
            'business_owner' => 'nullable|string|max:255',
            'business_registration' => 'nullable|string|max:255',
            'embed_allowed_origins' => 'nullable|string|max:1000',
            'business_address' => 'nullable|string|max:500',
            'support_email' => 'nullable|email|max:255',
            'support_phone' => 'nullable|string|max:60',
            'opening_hours_text' => 'nullable|string|max:1000',
            'privacy_effective_from' => 'nullable|string|max:40',
            'logo_width' => 'required|integer|min:60|max:360',
            'hero_overlay_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'hero_overlay_opacity' => 'required|numeric|min:0|max:1',
            'show_service_images' => 'boolean',
            'show_worker_avatars' => 'boolean',
            'business_logo' => 'nullable|image|max:2048',
            'hero_background_image' => 'nullable|image|max:5120',
            'remove_business_logo' => 'nullable|boolean',
            'remove_hero_background_image' => 'nullable|boolean',
        ]);

        $branding = PlanLimits::currentAllows('custom_branding');
        if ($branding) {
            foreach (['main_accent', 'text_color', 'booking_bg_color'] as $key) {
                Setting::set($key, strtolower($data[$key]));
            }
        }
        if (! PlanLimits::currentAllows('embed_widget')) {
            $data['embed_allowed_origins'] = '';
        }

        foreach (self::APPEARANCE as $key => [$type, $default]) {
            $value = match ($type) {
                'boolean' => $request->boolean($key),
                'integer' => (int) ($data[$key] ?? $default),
                'float' => (float) ($data[$key] ?? $default),
                default => trim((string) ($data[$key] ?? '')),
            };
            BusinessSetting::set($key, $value, $type);
        }

        foreach (['business_logo' => ['business_logo_path', 'branding'], 'hero_background_image' => ['hero_background_image_path', 'hero']] as $input => [$key, $folder]) {
            if ($branding && ($request->boolean('remove_'.$input) || $request->hasFile($input))) {
                if ($old = BusinessSetting::get($key)) {
                    Storage::disk('public')->delete($old);
                }
                BusinessSetting::set($key, $request->hasFile($input) ? $request->file($input)->store('tenants/'.Tenancy::id().'/'.$folder, 'public') : '');
            }
        }

        return redirect()->route('admin.settings')->with('success', __('ui.nastavenia_boli_ulozene'));
    }

    public function getThemeColors()
    {
        return response()->json(Setting::getThemeColors());
    }
}
