<?php

namespace App\Services\PayPal;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

/**
 * Thin client for the PayPal REST API (Subscriptions + webhook verification).
 */
class PayPalClient
{
    public function baseUrl(): string
    {
        return config('paypal.mode') === 'live' ? 'https://api-m.paypal.com' : 'https://api-m.sandbox.paypal.com';
    }

    public function sdkHost(): string
    {
        return config('paypal.mode') === 'live' ? 'https://www.paypal.com' : 'https://www.sandbox.paypal.com';
    }

    public function isConfigured(): bool
    {
        return config('paypal.client_id') !== '' && config('paypal.secret') !== '';
    }

    /** @throws RequestException */
    public function accessToken(): string
    {
        return Cache::remember('paypal:access_token:'.config('paypal.mode'), now()->addHours(7), function () {
            $response = Http::asForm()
                ->withBasicAuth((string) config('paypal.client_id'), (string) config('paypal.secret'))
                ->timeout(15)
                ->post($this->baseUrl().'/v1/oauth2/token', ['grant_type' => 'client_credentials'])
                ->throw();

            return (string) $response->json('access_token');
        });
    }

    protected function http(): PendingRequest
    {
        return Http::withToken($this->accessToken())->acceptJson()->asJson()->timeout(20)->retry(2, 300);
    }

    /** @return array<string, mixed> */
    public function subscription(string $id): array
    {
        return $this->http()->get($this->baseUrl().'/v1/billing/subscriptions/'.$id)->throw()->json();
    }

    public function cancelSubscription(string $id, string $reason = 'Cancelled by customer'): void
    {
        $this->http()->post($this->baseUrl().'/v1/billing/subscriptions/'.$id.'/cancel', ['reason' => $reason])->throw();
    }

    /** @return array<string, mixed> */
    public function createProduct(string $name): array
    {
        return $this->http()->post($this->baseUrl().'/v1/catalogs/products', [
            'name' => $name,
            'description' => 'Online booking system subscription',
            'type' => 'SERVICE',
            'category' => 'SOFTWARE',
        ])->throw()->json();
    }

    /** @return array<string, mixed> */
    public function createPlan(string $productId, string $name, string $currency, float|int $amount, string $interval): array
    {
        return $this->http()->post($this->baseUrl().'/v1/billing/plans', [
            'product_id' => $productId,
            'name' => $name,
            'status' => 'ACTIVE',
            'billing_cycles' => [[
                'frequency' => ['interval_unit' => $interval, 'interval_count' => 1],
                'tenure_type' => 'REGULAR',
                'sequence' => 1,
                'total_cycles' => 0,
                'pricing_scheme' => ['fixed_price' => ['value' => number_format((float) $amount, 2, '.', ''), 'currency_code' => $currency]],
            ]],
            'payment_preferences' => [
                'auto_bill_outstanding' => true,
                'setup_fee_failure_action' => 'CONTINUE',
                'payment_failure_threshold' => 2,
            ],
        ])->throw()->json();
    }

    /**
     * Verifies a webhook's signature with PayPal.
     *
     * @param  array<string, string>  $headers  lower-cased header name => value
     * @param  array<string, mixed>  $event
     */
    public function verifyWebhookSignature(array $headers, array $event): bool
    {
        $webhookId = (string) config('paypal.webhook_id');
        if ($webhookId === '') {
            return false;
        }

        $response = $this->http()->post($this->baseUrl().'/v1/notifications/verify-webhook-signature', [
            'transmission_id' => $headers['paypal-transmission-id'] ?? '',
            'transmission_time' => $headers['paypal-transmission-time'] ?? '',
            'cert_url' => $headers['paypal-cert-url'] ?? '',
            'auth_algo' => $headers['paypal-auth-algo'] ?? '',
            'transmission_sig' => $headers['paypal-transmission-sig'] ?? '',
            'webhook_id' => $webhookId,
            'webhook_event' => $event,
        ]);

        return $response->successful() && $response->json('verification_status') === 'SUCCESS';
    }
}
