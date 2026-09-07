<?php

return [
    /*
    | Master switch for the paid Pro plan. While false the service runs
    | free-only: the pricing table shows Pro as "coming soon", the tenant
    | billing page hides the PayPal buttons, and the activation endpoint is
    | closed. Flip BILLING_ENABLED=true (and configure PAYPAL_*) to sell Pro.
    */
    'enabled' => (bool) env('BILLING_ENABLED', false),
];
