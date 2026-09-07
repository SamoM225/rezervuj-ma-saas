<?php

namespace App\Http\Controllers;

use App\Models\BusinessSetting;
use App\Models\City;
use App\Support\Locales;
use App\Support\Tenancy;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;

/**
 * The two legal pages every booking page links to: the tenant's privacy
 * notice (Art. 13 GDPR, the tenant is the controller) and its booking terms.
 * Both are generated from the tenant's data in the visitor's language.
 */
class TenantLegalController extends Controller
{
    public function privacy(): View
    {
        $tenant = Tenancy::current();

        return view('tenant.legal', [
            'tenant' => $tenant,
            'title' => __('customer.legal.privacy_title'),
            'body' => $this->template('tenant-privacy-template'),
            'otherUrl' => route('terms', ['tenant' => $tenant->slug]),
            'otherLabel' => __('customer.legal.terms_link'),
            'version' => (string) config('gdpr.policy_version', '1.0'),
            'effective' => $this->effectiveDate(),
            'retentionDays' => (int) config('gdpr.retention_days', 730),
            'minAge' => (int) config('legal.min_age', 16),
            'authority' => config("tenancy.authorities.{$tenant->country}", __('customer.legal.authority_default')),
            'siteUrl' => rtrim(config('app.url'), '/'),
            'links' => [
                'privacy' => Locales::route('legal.show', ['doc' => 'privacy']),
                'cookies' => Locales::route('legal.show', ['doc' => 'cookies']),
            ],
        ]);
    }

    public function terms(): View
    {
        $tenant = Tenancy::current();

        return view('tenant.legal', [
            'tenant' => $tenant,
            'title' => __('customer.legal.terms_title'),
            'body' => $this->template('tenant-terms-template'),
            'otherUrl' => route('privacy', ['tenant' => $tenant->slug]),
            'otherLabel' => __('customer.legal.privacy_link'),
            'effective' => $this->effectiveDate(),
            'cancelHours' => (int) BusinessSetting::get('min_cancel_hours', 0),
            'advanceHours' => (int) BusinessSetting::get('booking_advance_hours', 2),
            'requireConfirmation' => (bool) BusinessSetting::get('require_confirmation', false),
            'locations' => City::query()->orderBy('name')->get(['name', 'address']),
            'adrBody' => config("tenancy.adr_bodies.{$tenant->country}", __('customer.legal.adr_default')),
            'privacyUrl' => route('privacy', ['tenant' => $tenant->slug]),
        ]);
    }

    /** Localized template with a Slovak fallback, like the platform legal hub. */
    private function template(string $name): string
    {
        $view = "legal.{$name}.".app()->getLocale();

        return view()->exists($view) ? $view : "legal.{$name}.sk";
    }

    private function effectiveDate(): string
    {
        $date = BusinessSetting::get('privacy_effective_from') ?: Tenancy::current()?->created_at;

        return $date ? Carbon::parse($date)->locale(app()->getLocale())->isoFormat('LL') : '';
    }
}
