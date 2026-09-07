<?php

namespace Tests\Feature;

use App\Models\Subscription;
use App\Models\Tenant;
use App\Models\User;
use App\Support\Tenancy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class BillingTest extends TestCase
{
    use RefreshDatabase;

    private Tenant $tenant;

    private User $owner;

    protected function setUp(): void
    {
        parent::setUp();
        config([
            'billing.enabled' => true,
            'paypal.mode' => 'sandbox', 'paypal.client_id' => 'cid', 'paypal.secret' => 'sec', 'paypal.webhook_id' => 'WH-1',
            'paypal.plans.eur_monthly.id' => 'P-EUR-M', 'paypal.plans.eur_yearly.id' => 'P-EUR-Y',
            'paypal.plans.usd_monthly.id' => 'P-USD-M', 'paypal.plans.usd_yearly.id' => 'P-USD-Y',
        ]);
        $this->tenant = Tenant::factory()->create(['slug' => 'salon-pay']);
        Tenancy::set($this->tenant);
        $this->owner = User::factory()->create(['role' => 'superadmin', 'email_otp_enabled' => false]);
        Tenancy::forget();
    }

    public function test_billing_page_shows_usage_and_paypal_buttons_for_a_free_tenant(): void
    {
        $this->actingAs($this->owner)->get('/salon-pay/admin/billing')
            ->assertOk()
            ->assertSee('0 / 50', false)
            ->assertSee('data-plan="P-EUR-M"', false)
            ->assertSee('sandbox.paypal.com/sdk/js', false);

        $this->actingAs($this->owner)->get('/salon-pay/admin/billing?currency=USD')->assertOk()->assertSee('data-plan="P-USD-M"', false);
    }

    public function test_approved_subscription_activates_pro_until_next_billing_plus_grace(): void
    {
        Http::fake([
            '*/v1/oauth2/token' => Http::response(['access_token' => 'tok', 'expires_in' => 3600]),
            '*/v1/billing/subscriptions/I-ABC' => Http::response($this->paypalSubscription('ACTIVE', 'P-EUR-M', '2026-10-04T10:00:00Z')),
        ]);

        $this->actingAs($this->owner)
            ->postJson('/salon-pay/admin/billing/activate', ['subscription_id' => 'I-ABC', 'plan_key' => 'eur_monthly'])
            ->assertOk()->assertJsonPath('pro', true);

        $tenant = $this->tenant->fresh();
        $this->assertTrue($tenant->isPro());
        $this->assertSame('2026-10-07 10:00:00', $tenant->pro_until->toDateTimeString());
        $this->assertDatabaseHas('subscriptions', ['tenant_id' => $tenant->id, 'provider_id' => 'I-ABC', 'status' => 'ACTIVE', 'plan_key' => 'eur_monthly', 'currency' => 'EUR']);
    }

    public function test_activation_rejects_a_subscription_for_a_different_plan_or_another_tenant(): void
    {
        Http::fake([
            '*/v1/oauth2/token' => Http::response(['access_token' => 'tok']),
            '*/v1/billing/subscriptions/I-OTHER' => Http::response($this->paypalSubscription('ACTIVE', 'P-USD-Y', id: 'I-OTHER')),
        ]);
        $this->actingAs($this->owner)
            ->postJson('/salon-pay/admin/billing/activate', ['subscription_id' => 'I-OTHER', 'plan_key' => 'eur_monthly'])
            ->assertStatus(422);

        $other = Tenant::factory()->create();
        Subscription::create(['tenant_id' => $other->id, 'provider_id' => 'I-OTHER', 'plan_key' => 'usd_yearly', 'status' => 'ACTIVE', 'currency' => 'USD', 'amount' => 50]);
        $this->actingAs($this->owner)
            ->postJson('/salon-pay/admin/billing/activate', ['subscription_id' => 'I-OTHER', 'plan_key' => 'usd_yearly'])
            ->assertForbidden();
        $this->assertFalse($this->tenant->fresh()->isPro());
    }

    public function test_webhook_is_verified_idempotent_and_keeps_pro_until_the_paid_period_ends_after_cancellation(): void
    {
        Subscription::create(['tenant_id' => $this->tenant->id, 'provider_id' => 'I-ABC', 'plan_key' => 'eur_monthly', 'status' => 'ACTIVE', 'currency' => 'EUR', 'amount' => 5]);
        $this->tenant->forceFill(['plan' => Tenant::PLAN_PRO, 'pro_until' => now()->addDays(20)])->save();

        Http::fake([
            '*/v1/oauth2/token' => Http::response(['access_token' => 'tok']),
            '*/v1/notifications/verify-webhook-signature' => Http::sequence()
                ->push(['verification_status' => 'FAILURE'])
                ->push(['verification_status' => 'SUCCESS'])
                ->push(['verification_status' => 'SUCCESS']),
            '*/v1/billing/subscriptions/I-ABC' => Http::response($this->paypalSubscription('CANCELLED', 'P-EUR-M', null, now()->subDays(10)->toIso8601ZuluString())),
        ]);

        $event = ['id' => 'WH-EVT-1', 'event_type' => 'BILLING.SUBSCRIPTION.CANCELLED', 'resource' => ['id' => 'I-ABC', 'status' => 'CANCELLED']];
        $headers = ['PAYPAL-TRANSMISSION-ID' => 't', 'PAYPAL-TRANSMISSION-TIME' => 'x', 'PAYPAL-CERT-URL' => 'u', 'PAYPAL-AUTH-ALGO' => 'a', 'PAYPAL-TRANSMISSION-SIG' => 's'];

        $this->postJson('/webhooks/paypal', $event, $headers)->assertStatus(400);
        $this->postJson('/webhooks/paypal', $event, $headers)->assertOk();
        $this->postJson('/webhooks/paypal', $event, $headers)->assertOk()->assertSee('duplicate');

        $this->assertDatabaseCount('payment_events', 1);
        $tenant = $this->tenant->fresh();
        $this->assertSame('CANCELLED', Subscription::first()->status);
        $this->assertTrue($tenant->isPro(), 'access continues until the paid period ends');
        $this->assertTrue($tenant->pro_until->between(now()->addDays(22), now()->addDays(24)), 'period end = last payment + 1 month + grace');

        $this->travel(30)->days();
        $this->assertFalse($tenant->fresh()->isPro());
    }

    public function test_owner_can_cancel_from_the_billing_page(): void
    {
        Subscription::create(['tenant_id' => $this->tenant->id, 'provider_id' => 'I-ABC', 'plan_key' => 'eur_monthly', 'status' => 'ACTIVE', 'currency' => 'EUR', 'amount' => 5]);
        $this->tenant->forceFill(['plan' => Tenant::PLAN_PRO, 'pro_until' => now()->addDays(20)])->save();
        Http::fake([
            '*/v1/oauth2/token' => Http::response(['access_token' => 'tok']),
            '*/v1/billing/subscriptions/I-ABC/cancel' => Http::response('', 204),
            '*/v1/billing/subscriptions/I-ABC' => Http::response($this->paypalSubscription('CANCELLED', 'P-EUR-M', null, now()->subDays(10)->toIso8601ZuluString())),
        ]);

        $this->actingAs($this->owner)->post('/salon-pay/admin/billing/cancel')->assertRedirect()->assertSessionHas('success');
        Http::assertSent(fn ($request) => str_ends_with($request->url(), '/I-ABC/cancel'));
        $this->assertSame('CANCELLED', Subscription::first()->status);
    }

    public function test_billing_page_shows_coming_soon_and_no_paypal_when_billing_disabled(): void
    {
        config(['billing.enabled' => false]);

        $this->actingAs($this->owner)->get('/salon-pay/admin/billing')
            ->assertOk()
            ->assertDontSee('data-plan="P-EUR-M"', false)
            ->assertDontSee('sandbox.paypal.com/sdk/js', false);
    }

    public function test_activation_is_closed_when_billing_disabled(): void
    {
        config(['billing.enabled' => false]);

        $this->actingAs($this->owner)
            ->postJson('/salon-pay/admin/billing/activate', ['subscription_id' => 'I-ABC', 'plan_key' => 'eur_monthly'])
            ->assertStatus(403);
    }

    /** @return array<string, mixed> */
    private function paypalSubscription(string $status, string $planId, ?string $nextBilling = '2026-10-04T10:00:00Z', ?string $lastPayment = null, string $id = 'I-ABC'): array
    {
        $billing = [];
        if ($nextBilling) {
            $billing['next_billing_time'] = $nextBilling;
        }
        if ($lastPayment) {
            $billing['last_payment'] = ['time' => $lastPayment, 'amount' => ['value' => '5.00', 'currency_code' => 'EUR']];
        }

        return ['id' => $id, 'status' => $status, 'plan_id' => $planId, 'custom_id' => 'salon-pay', 'billing_info' => $billing];
    }
}
