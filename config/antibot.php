<?php

return [
    // Master switch for the anti-bot middleware.
    'enabled' => env('ANTIBOT_ENABLED', true),

    // Time honeypot: a submission faster than this many seconds after the page
    // was rendered is treated as a bot. The full multi-step booking flow takes
    // a human far longer, so this is safe.
    'min_seconds' => (int) env('ANTIBOT_MIN_SECONDS', 3),

    // Reject tokens older than this (stale page) — user is asked to reload.
    'max_seconds' => (int) env('ANTIBOT_MAX_SECONDS', 21600), // 6 hours

    // Behavioural honeypot: minimum number of distinct human interaction events
    // (mouse move, key press, focus, tap…) the page must have recorded.
    'min_interactions' => (int) env('ANTIBOT_MIN_INTERACTIONS', 2),
];
