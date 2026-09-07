<?php

namespace App\Http\Middleware;

use App\Services\BlacklistService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware that silently blocks requests from blacklisted identifiers.
 *
 * Attach to any route that accepts `email` or `phone` in the request body
 * (e.g. login, registration, booking creation).
 *
 * The user receives a neutral message and never learns they are on the blacklist.
 */
class CheckBlacklist
{
    public function handle(Request $request, Closure $next): Response
    {
        $email = $request->input('email') ?? $request->input('customer_email');
        $phone = $request->input('phone') ?? $request->input('customer_phone');

        // Nothing to check
        if (!$email && !$phone) {
            return $next($request);
        }

        $result = BlacklistService::check($email, $phone);

        if ($result['blocked']) {
            return BlacklistService::blockedResponse($request);
        }

        return $next($request);
    }
}
