<?php

namespace App\Services;

use App\Models\BlacklistEntry;
use Illuminate\Support\Facades\Log;

/**
 * Centralized Blacklist checking service.
 *
 * Returns a neutral, non-revealing message whenever a blacklisted
 * identifier is detected so that the blocked user never learns
 * they are on the blacklist.
 */
class BlacklistService
{
    /**
     * The neutral message shown to blocked users.
     * Intentionally vague – the user must NOT know they are blacklisted.
     */
    public const NEUTRAL_MESSAGE = 'Momentálne sa nám nepodarilo spracovať vašu požiadavku. Skúste to prosím neskôr.';

    /**
     * Check whether an email/phone is blocked.
     *
     * @return array{blocked: bool, entry: ?BlacklistEntry, can_override: bool, severity: ?string}
     */
    public static function check(?string $email = null, ?string $phone = null): array
    {
        $entry = BlacklistEntry::checkBlacklist($email, $phone);

        if (!$entry || !$entry->blocksBookings()) {
            return [
                'blocked'      => false,
                'entry'        => $entry, // may be a warning-level entry
                'can_override' => false,
                'severity'     => $entry?->severity,
            ];
        }

        // Increment violation counter
        $entry->incrementViolation();

        Log::warning('BlacklistService: identifier blocked', [
            'email'        => $email,
            'phone'        => $phone,
            'blacklist_id' => $entry->id,
            'severity'     => $entry->severity,
        ]);

        return [
            'blocked'      => true,
            'entry'        => $entry,
            'can_override' => $entry->canBeOverridden(),
            'severity'     => $entry->severity,
        ];
    }

    /**
     * Return a JSON response suitable for API consumers.
     * Never reveals blacklist status – always uses the neutral message.
     */
    public static function blockedJsonResponse(): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'message' => self::NEUTRAL_MESSAGE,
        ], 422);
    }

    /**
     * Return a redirect-back response for web forms.
     */
    public static function blockedRedirectResponse(): \Illuminate\Http\RedirectResponse
    {
        return back()
            ->withErrors(['email' => self::NEUTRAL_MESSAGE])
            ->withInput();
    }

    /**
     * Convenience: resolve the appropriate blocked response depending on the request type.
     */
    public static function blockedResponse(\Illuminate\Http\Request $request)
    {
        if ($request->wantsJson() || $request->is('api/*')) {
            return self::blockedJsonResponse();
        }

        return self::blockedRedirectResponse();
    }
}
