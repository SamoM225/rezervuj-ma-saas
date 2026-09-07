<?php

return [
    /*
    | Versions of the legal documents a tenant accepts at signup. Bump a version
    | when the text changes materially; acceptances are logged per version.
    */
    'versions' => [
        'terms' => env('LEGAL_TERMS_VERSION', '2026-09'),
        'dpa' => env('LEGAL_DPA_VERSION', '2026-09'),
        'privacy' => env('LEGAL_PRIVACY_VERSION', '2026-09'),
        'controller_declaration' => '2026-09',
        'accuracy' => '2026-09',
        'age' => '2026-09',
        'marketing' => '2026-09',
    ],

    /*
    | Platform operator shown in the legal documents. While the service is a
    | free, pre-launch tool run by a private individual in the EU, we publish
    | only the service name and a working contact e-mail — no personal name or
    | address. Fill OPERATOR_NAME/ADDRESS later if the operator is registered.
    */
    'operator' => [
        'name' => env('OPERATOR_NAME', 'rezervuj-ma.online'),
        'email' => env('OPERATOR_EMAIL', 'majerciks1004@gmail.com'),
        'address' => env('OPERATOR_ADDRESS', ''),
        'country' => env('OPERATOR_COUNTRY', 'SK'),
    ],

    /* Minimum age to use a booking page on one's own (GDPR Art. 8 leaves 13–16 to Member States). */
    'min_age' => (int) env('LEGAL_MIN_AGE', 16),
];
