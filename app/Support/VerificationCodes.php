<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Six-digit one-time codes delivered by e-mail (signup verification, login
 * second factor). Codes are stored hashed, expire after TTL_MINUTES and allow
 * a handful of attempts before they are burnt.
 */
final class VerificationCodes
{
    public const TTL_MINUTES = 15;

    public const MAX_ATTEMPTS = 5;

    public const PURPOSE_SIGNUP = 'signup';

    public const PURPOSE_LOGIN = 'login';

    /** Issues a new code and invalidates previous unused codes for the same e-mail/purpose. */
    public static function issue(string $email, string $purpose, ?int $userId = null, ?string $ip = null): string
    {
        $email = mb_strtolower(trim($email));
        DB::table('verification_codes')->where('email', $email)->where('purpose', $purpose)->where('verified', false)->delete();

        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        DB::table('verification_codes')->insert([
            'email' => $email,
            'purpose' => $purpose,
            'user_id' => $userId,
            'ip_address' => $ip,
            'code_hash' => Hash::make($code),
            'expires_at' => now()->addMinutes(self::TTL_MINUTES),
            'attempts_left' => self::MAX_ATTEMPTS,
            'verified' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $code;
    }

    /** Checks a code; consumes it on success, burns an attempt on failure. */
    public static function verify(string $email, string $purpose, string $code): bool
    {
        $email = mb_strtolower(trim($email));
        $code = preg_replace('/\D+/', '', $code) ?? '';
        if (strlen($code) !== 6) {
            return false;
        }

        $row = DB::table('verification_codes')
            ->where('email', $email)->where('purpose', $purpose)->where('verified', false)
            ->where('expires_at', '>', now())
            ->orderByDesc('id')
            ->first();

        if (! $row || $row->attempts_left <= 0) {
            return false;
        }

        if (! Hash::check($code, $row->code_hash)) {
            DB::table('verification_codes')->where('id', $row->id)->decrement('attempts_left');

            return false;
        }

        DB::table('verification_codes')->where('id', $row->id)->update(['verified' => true, 'updated_at' => now()]);

        return true;
    }

    /** Housekeeping for the scheduler: drop expired or used codes. */
    public static function prune(): int
    {
        return DB::table('verification_codes')
            ->where(fn ($q) => $q->where('expires_at', '<', now()->subDay())->orWhere('verified', true))
            ->delete();
    }
}
