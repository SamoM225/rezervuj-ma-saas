<?php

return [
    /*
    | PayPal REST credentials (Developer Dashboard → Apps & Credentials).
    | mode: sandbox | live
    */
    'mode' => env('PAYPAL_MODE', 'sandbox'),
    'client_id' => env('PAYPAL_CLIENT_ID', ''),
    'secret' => env('PAYPAL_SECRET', ''),

    /*
    | Webhook created in the PayPal dashboard for <site>/webhooks/paypal with
    | events: BILLING.SUBSCRIPTION.* and PAYMENT.SALE.COMPLETED.
    */
    'webhook_id' => env('PAYPAL_WEBHOOK_ID', ''),

    /*
    | Billing plan IDs (created once with `php artisan paypal:setup-plans`).
    | key => [plan id env, currency, amount, interval]
    */
    'plans' => [
        'eur_monthly' => ['id' => env('PAYPAL_PLAN_EUR_MONTHLY', ''), 'currency' => 'EUR', 'amount' => 5, 'interval' => 'MONTH'],
        'eur_yearly' => ['id' => env('PAYPAL_PLAN_EUR_YEARLY', ''), 'currency' => 'EUR', 'amount' => 50, 'interval' => 'YEAR'],
        'usd_monthly' => ['id' => env('PAYPAL_PLAN_USD_MONTHLY', ''), 'currency' => 'USD', 'amount' => 5, 'interval' => 'MONTH'],
        'usd_yearly' => ['id' => env('PAYPAL_PLAN_USD_YEARLY', ''), 'currency' => 'USD', 'amount' => 50, 'interval' => 'YEAR'],
    ],

    /*
    | Days of access kept after a missed renewal before the tenant drops to Free.
    */
    'grace_days' => 3,

    'product_name' => env('PAYPAL_PRODUCT_NAME', 'rezervuj-ma Pro'),
];
