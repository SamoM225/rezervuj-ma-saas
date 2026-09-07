<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Services\PayPal\PayPalClient;
use App\Services\PayPal\SubscriptionSync;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * PayPal webhooks (BILLING.SUBSCRIPTION.*, PAYMENT.SALE.COMPLETED).
 * Signature-verified, idempotent by event id; the subscription is re-read
 * from PayPal so the local state never depends on event ordering.
 */
class PayPalWebhookController extends Controller
{
    public function __invoke(Request $request, PayPalClient $paypal, SubscriptionSync $sync): Response
    {
        $event = $request->json()->all();
        $eventId = (string) ($event['id'] ?? '');
        $type = (string) ($event['event_type'] ?? '');
        if ($eventId === '' || $type === '') {
            return response('bad request', 400);
        }

        $headers = collect($request->headers->all())->map(fn (array $v) => $v[0] ?? '')->all();
        if (! $paypal->verifyWebhookSignature($headers, $event)) {
            Log::warning('PayPal webhook signature failed', ['event_id' => $eventId, 'type' => $type]);

            return response('invalid signature', 400);
        }

        $subscriptionId = $this->subscriptionIdFrom($event);
        $inserted = DB::table('payment_events')->insertOrIgnore([
            'provider' => 'paypal',
            'event_id' => $eventId,
            'event_type' => $type,
            'subscription_provider_id' => $subscriptionId,
            'payload' => json_encode($event),
            'created_at' => now(),
        ]);
        if ($inserted === 0) {
            return response('duplicate', 200);
        }

        if ($subscriptionId) {
            $local = Subscription::query()->where('provider_id', $subscriptionId)->first();
            $tenant = $local?->tenant ?? $this->tenantFromCustomId($event);
            if ($tenant) {
                try {
                    $sync->apply($tenant, $paypal->subscription($subscriptionId), $local?->plan_key);
                    DB::table('payment_events')->where('event_id', $eventId)->update(['tenant_id' => $tenant->id, 'processed_at' => now()]);
                } catch (RequestException $e) {
                    Log::error('PayPal webhook: subscription fetch failed', ['subscription' => $subscriptionId, 'error' => $e->getMessage()]);

                    return response('retry', 500);
                }
            }
        }

        return response('ok', 200);
    }

    /** @param  array<string, mixed>  $event */
    private function subscriptionIdFrom(array $event): ?string
    {
        $resource = $event['resource'] ?? [];
        if (str_starts_with((string) ($event['event_type'] ?? ''), 'BILLING.SUBSCRIPTION.')) {
            return $resource['id'] ?? null;
        }

        return $resource['billing_agreement_id'] ?? null;
    }

    /** @param  array<string, mixed>  $event */
    private function tenantFromCustomId(array $event): ?Tenant
    {
        $custom = (string) ($event['resource']['custom_id'] ?? '');

        return $custom !== '' ? Tenant::query()->where('slug', $custom)->first() : null;
    }
}
