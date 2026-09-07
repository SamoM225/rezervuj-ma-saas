<?php

return [
    /*
    | Secret token for the public scheduler endpoint (/scheduler-run/{token}).
    | An external cron service (cron-job.org, Websupport cron panel, …) calls
    | that URL to run Laravel's scheduler — no system cron required.
    | Leave empty to disable the endpoint (returns 404).
    */
    'scheduler_token' => env('SCHEDULER_TOKEN'),

    /*
    | Zero-config fallback: on a fraction of normal web requests, run the daily
    | GDPR data-retention purge if it hasn't run today. [chance, total] → here
    | ~2% of requests trigger the (cheap, once-per-day-guarded) check, so old
    | data is deleted even if no external cron is ever set up.
    */
    'lottery' => [
        (int) env('MAINTENANCE_LOTTERY_CHANCE', 2),
        (int) env('MAINTENANCE_LOTTERY_TOTAL', 100),
    ],
];
