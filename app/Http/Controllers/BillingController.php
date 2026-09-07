<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Services\PayPal\PayPalClient;
use App\Services\PayPal\SubscriptionSync;
use App\Support\PlanLimits;
use App\Support\Tenancy;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Tenant billing: Free vs Pro, PayPal subscription buttons, cancellation.
 */
class BillingController extends Controller
{
    public function index(Request $request, PayPalClient $paypal): View
    {
        $tenant = Tenancy::current();
        $currency = strtoupper((string) $request->query('currency', $tenant->currency === 'USD' ? 'USD' : 'EUR'));
        $currency = in_array($currency, ['EUR', 'USD'], true) ? $currency : 'EUR';
        $prefix = strtolower($currency);

        return view('admin.billing', [
            'tenant' => $tenant,
            'isPro' => $tenant->isPro(),
            'subscription' => Subscription::query()->where('tenant_id', $tenant->id)->latest('id')->first(),
            'used' => PlanLimits::bookingsThisMonth($tenant),
            'limit' => config('tenancy.plans.free.bookings_per_month'),
            'currency' => $currency,
            'plans' => [
                'monthly' => config("paypal.plans.{$prefix}_monthly"),
                'yearly' => config("paypal.plans.{$prefix}_yearly"),
            ],
            'planKeys' => ['monthly' => "{$prefix}_monthly", 'yearly' => "{$prefix}_yearly"],
            'billingEnabled' => (bool) config('billing.enabled'),
            'paypalConfigured' => config('billing.enabled') && $paypal->isConfigured() && config("paypal.plans.{$prefix}_monthly.id") !== '',
            'sdkHost' => $paypal->sdkHost(),
            'clientId' => (string) config('paypal.client_id'),
        ]);
    }

    /** Called by the PayPal buttons' onApprove with the new subscription id. */
    public function activate(Request $request, PayPalClient $paypal, SubscriptionSync $sync): JsonResponse
    {
        abort_unless(config('billing.enabled'), 403);

        $data = $request->validate([
            'subscription_id' => ['required', 'string', 'max:64', 'regex:/^[A-Z0-9-]+$/i'],
            'plan_key' => ['required', Rule::in(array_keys(config('paypal.plans')))],
        ]);
        $tenant = Tenancy::current();

        try {
            $resource = $paypal->subscription($data['subscription_id']);
        } catch (RequestException) {
            return response()->json(['message' => 'PayPal nepotvrdil predplatné. Skúste to o chvíľu znova.'], 502);
        }

        // Never let one tenant claim another tenant's subscription.
        if (($resource['id'] ?? null) !== $data['subscription_id']) {
            return response()->json(['message' => 'PayPal vrátil iné predplatné, než bolo požadované.'], 422);
        }
        $owner = Subscription::query()->where('provider_id', $data['subscription_id'])->first();
        if ($owner && (int) $owner->tenant_id !== (int) $tenant->id) {
            abort(403);
        }
        $expectedPlan = config("paypal.plans.{$data['plan_key']}.id");
        if ($expectedPlan !== '' && ($resource['plan_id'] ?? null) !== $expectedPlan) {
            return response()->json(['message' => 'Predplatné nezodpovedá zvolenému plánu.'], 422);
        }

        $sync->apply($tenant, $resource, $data['plan_key']);

        return response()->json(['ok' => true, 'pro' => $tenant->fresh()->isPro()]);
    }

    public function cancel(PayPalClient $paypal, SubscriptionSync $sync): RedirectResponse
    {
        $tenant = Tenancy::current();
        $subscription = Subscription::query()->where('tenant_id', $tenant->id)->whereIn('status', ['ACTIVE', 'APPROVED', 'SUSPENDED'])->latest('id')->first();
        if (! $subscription) {
            return back()->withErrors(['billing' => __('ui.nemate_aktivne_predplatne')]);
        }

        try {
            $paypal->cancelSubscription($subscription->provider_id);
            $sync->apply($tenant, $paypal->subscription($subscription->provider_id), $subscription->plan_key);
        } catch (RequestException) {
            return back()->withErrors(['billing' => __('ui.zrusenie_sa_nepodarilo_skuste_to_znova')]);
        }

        return back()->with('success', __('ui.predplatne_je_zrusene_pro_funkcie_ostavaju'));
    }
}
