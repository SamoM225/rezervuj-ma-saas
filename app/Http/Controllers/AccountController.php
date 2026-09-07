<?php

namespace App\Http\Controllers;

use App\Models\BlacklistEntry;
use App\Models\Booking;
use App\Models\BusinessSetting;
use App\Models\Category;
use App\Models\City;
use App\Models\Schedule;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Tenant;
use App\Models\User;
use App\Models\WorkerAvailability;
use App\Support\Locales;
use App\Support\Tenancy;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * The tenant's own account: full data export (Data Act / DPA clause 12) and
 * account deletion. Owner (superadmin) only.
 */
class AccountController extends Controller
{
    /** Everything the tenant owns, as one JSON file. */
    public function export(): StreamedResponse
    {
        $tenant = Tenancy::current();

        $payload = [
            'exported_at' => now()->toIso8601String(),
            'tenant' => $tenant->only(['slug', 'name', 'category', 'country', 'city', 'address', 'phone', 'email', 'description', 'locale', 'timezone', 'currency', 'plan', 'created_at']),
            'business_settings' => BusinessSetting::query()->get(['key', 'value', 'type'])->keyBy('key')->map(fn ($row) => BusinessSetting::get($row->key)),
            'appearance' => Setting::query()->get(['key', 'value'])->pluck('value', 'key'),
            'locations' => City::query()->get(['id', 'name', 'address', 'created_at']),
            'categories' => Category::query()->with('cities:id')->get()->map(fn ($c) => ['id' => $c->id, 'name' => $c->name, 'description' => $c->description, 'locations' => $c->cities->pluck('id')]),
            'services' => Service::query()->get(['id', 'category_id', 'name', 'description', 'duration', 'break_time', 'price', 'is_active', 'created_at']),
            'team' => User::query()->with('services:id')->get()->map(fn ($u) => [
                'id' => $u->id, 'name' => $u->name, 'email' => $u->email, 'phone' => $u->phone ?? null, 'role' => $u->role, 'city_id' => $u->city_id,
                'services' => $u->services->pluck('id'), 'created_at' => $u->created_at,
            ]),
            'availability' => WorkerAvailability::query()->get(),
            'schedules' => Schedule::query()->get(),
            'bookings' => Booking::withTrashed()->get(['id', 'user_id', 'service_id', 'city_id', 'customer_name', 'customer_email', 'customer_phone', 'date', 'start_time', 'end_time', 'notes', 'status', 'locale', 'gdpr_consent_at', 'gdpr_policy_version', 'created_at', 'updated_at', 'deleted_at']),
            'blocked_customers' => BlacklistEntry::query()->get(),
        ];

        $filename = 'rezervuj-ma-'.$tenant->slug.'-'.now()->format('Y-m-d').'.json';

        return response()->streamDownload(function () use ($payload) {
            echo json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }, $filename, ['Content-Type' => 'application/json; charset=utf-8']);
    }

    /**
     * Switch the account off now; data is purged after the 30-day retrieval
     * period by `tenants:purge-deleted`. Requires the owner's password and the
     * slug typed back to avoid accidents.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $tenant = Tenancy::current();
        $data = $request->validate([
            'password' => 'required|string',
            'confirm_slug' => 'required|string',
        ]);

        if (! Hash::check($data['password'], $request->user()->password)) {
            return back()->withErrors(['password' => __('ui.nespravne_heslo')]);
        }
        if (trim($data['confirm_slug']) !== $tenant->slug) {
            return back()->withErrors(['confirm_slug' => __('ui.confirm_slug_mismatch', ['slug' => $tenant->slug])]);
        }

        $tenant->forceFill(['status' => Tenant::STATUS_DELETED, 'deleted_at' => now(), 'is_public' => false])->save();
        Log::info('Tenant account deletion requested', ['tenant_id' => $tenant->id, 'user_id' => $request->user()->id]);

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect(Locales::route('home'))->with('status', __('ui.account_deleted_flash'));
    }
}
