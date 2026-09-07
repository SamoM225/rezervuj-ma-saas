<?php

return [
    /*
    | Version of the privacy policy a customer consented to (stored per booking).
    | Bump this when the policy text changes.
    */
    'policy_version' => env('GDPR_POLICY_VERSION', '1.0'),

    /*
    | How long booking + customer data is kept before it is automatically
    | deleted by the `bookings:purge-expired` command (run daily by the
    | scheduler). Default: 730 days (2 years).
    */
    'retention_days' => (int) env('GDPR_RETENTION_DAYS', 730),
];
