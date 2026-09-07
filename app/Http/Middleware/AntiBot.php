<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Layered anti-bot protection for public forms:
 *   1. Classic hidden-field honeypot ("website") — humans never fill it.
 *   2. Time honeypot — an encrypted render timestamp; submissions that are too
 *      fast (or have a missing/forged/stale token) are rejected.
 *   3. Behavioural honeypot — a JS-maintained counter of real interaction
 *      events (mouse move, key press, focus, tap). Headless bots that don't run
 *      JS / don't interact never reach the threshold.
 *
 * Toggle and thresholds live in config/antibot.php.
 */
class AntiBot
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!config('antibot.enabled', true)) {
            return $next($request);
        }

        $reason = $this->detect($request);
        if ($reason !== null) {
            Log::warning('AntiBot: blocked submission', [
                'reason' => $reason,
                'ip' => $request->ip(),
                'path' => $request->path(),
            ]);
            return $this->reject($request, $reason);
        }

        return $next($request);
    }

    private function detect(Request $request): ?string
    {
        // 1) Classic hidden-field honeypot.
        if (filled($request->input('website'))) {
            return 'honeypot';
        }

        // 2) Time honeypot — decrypt the render timestamp (tamper-proof).
        try {
            $ts = (int) Crypt::decryptString((string) $request->input('bk_ts'));
        } catch (\Throwable $e) {
            return 'missing_token';
        }

        $elapsed = now()->timestamp - $ts;
        if ($elapsed < (int) config('antibot.min_seconds', 3)) {
            return 'too_fast';
        }
        if ($elapsed > (int) config('antibot.max_seconds', 21600)) {
            return 'stale';
        }

        // 3) Behavioural honeypot — JS executed and the user interacted.
        $interactions = (int) $request->input('bk_js', 0);
        if ($interactions < (int) config('antibot.min_interactions', 2)) {
            return 'no_interaction';
        }

        return null;
    }

    private function reject(Request $request, string $reason): Response
    {
        $message = $reason === 'stale'
            ? 'Formulár vypršal. Obnovte prosím stránku a skúste rezerváciu znova.'
            : 'Vašu požiadavku sa nepodarilo spracovať. Skúste to prosím znova.';

        if ($request->expectsJson()) {
            return response()->json(['message' => $message], 422);
        }

        return back()->withErrors(['error' => $message])->withInput();
    }
}
