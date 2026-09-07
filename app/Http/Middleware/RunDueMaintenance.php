<?php

namespace App\Http\Middleware;

use App\Support\ScheduledMaintenance;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Zero-config "poor-man's cron": on a small fraction of requests, run the
 * daily data-retention purge after the response has been sent, so it never
 * adds latency to the user's request. Works without any system cron.
 */
class RunDueMaintenance
{
    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }

    public function terminate(Request $request, Response $response): void
    {
        try {
            [$chance, $total] = config('maintenance.lottery', [2, 100]);
            if ((int) $total > 0 && random_int(1, (int) $total) <= (int) $chance) {
                ScheduledMaintenance::purgeIfDue();
            }
        } catch (\Throwable $e) {
            // Maintenance must never affect the request lifecycle.
        }
    }
}
