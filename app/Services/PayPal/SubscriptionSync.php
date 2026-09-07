<?php

namespace App\Services\PayPal;

use App\Models\Subscription;
use App\Models\Tenant;
use Carbon\CarbonImmutable;

/**
 * Applies a PayPal subscription resource to our records: upserts the
 * subscription row and derives the tenant's plan and `pro_until`.
 *
 * Rules: ACTIVE/APPROVED → Pro until next billing time (+ grace days).
 * CANCELLED → stays Pro until the already paid period ends, then Free.
 * SUSPENDED/EXPIRED → Pro only while `pro_until` is still in the future.
 */
class SubscriptionSync
{
    /**
     * @param  array<string, mixed>  $resource  PayPal subscription JSON
     */
    public function apply(Tenant $tenant, array $resource, ?string $planKey = null): Subscription
    {
        $planKey ??= $this->planKeyFor((string) ($resource['plan_id'] ?? '')) ?? 'eur_monthly';
        $plan = config("paypal.plans.{$planKey}");
        $status = strtoupper((string) ($resource['status'] ?? 'APPROVAL_PENDING'));

        $nextBilling = $resource['billing_info']['next_billing_time'] ?? null;
        $lastPayment = $resource['billing_info']['last_payment']['time'] ?? null;
        $periodEnd = $this->periodEnd($nextBilling, $lastPayment, $planKey);

        $subscription = Subscription::query()->updateOrCreate(
            ['provider' => 'paypal', 'provider_id' => (string) $resource['id']],
            [
                'tenant_id' => $tenant->id,
                'plan_key' => $planKey,
                'status' => $status,
                'currency' => $plan['currency'] ?? 'EUR',
                'amount' => $plan['amount'] ?? 0,
                'current_period_end' => $periodEnd,
                'cancelled_at' => $status === 'CANCELLED' ? ($subscription?->cancelled_at ?? now()) : null,
                'raw' => $resource,
            ]
        );

        $grace = (int) config('paypal.grace_days', 3);
        if (in_array($status, ['ACTIVE', 'APPROVED'], true) && $periodEnd) {
            $tenant->forceFill(['plan' => Tenant::PLAN_PRO, 'pro_until' => $periodEnd->addDays($grace)])->save();
        } elseif (in_array($status, ['CANCELLED', 'SUSPENDED', 'EXPIRED'], true)) {
            // PayPal's paid period is authoritative; access ends with it (+ grace).
            // isPro() turns false by itself once pro_until passes.
            if ($periodEnd) {
                $tenant->forceFill(['plan' => Tenant::PLAN_PRO, 'pro_until' => $periodEnd->addDays($grace)])->save();
            } elseif ($tenant->pro_until === null) {
                $tenant->forceFill(['pro_until' => now()])->save();
            }
            if (! $tenant->isPro()) {
                $tenant->forceFill(['plan' => Tenant::PLAN_FREE])->save();
            }
        }

        return $subscription;
    }

    public function planKeyFor(string $paypalPlanId): ?string
    {
        foreach (config('paypal.plans', []) as $key => $plan) {
            if ($paypalPlanId !== '' && ($plan['id'] ?? null) === $paypalPlanId) {
                return $key;
            }
        }

        return null;
    }

    private function periodEnd(?string $nextBilling, ?string $lastPayment, string $planKey): ?CarbonImmutable
    {
        if ($nextBilling) {
            return CarbonImmutable::parse($nextBilling);
        }
        if ($lastPayment) {
            $interval = config("paypal.plans.{$planKey}.interval", 'MONTH');

            return CarbonImmutable::parse($lastPayment)->add($interval === 'YEAR' ? '1 year' : '1 month');
        }

        return null;
    }
}
