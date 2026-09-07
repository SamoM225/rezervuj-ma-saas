<?php

namespace App\Http\Controllers;

use App\Mail\VerificationCodeMail;
use App\Models\BusinessSetting;
use App\Models\User;
use App\Support\Totp;
use App\Support\VerificationCodes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Staff login for a tenant. Every login is confirmed with a second factor:
 * the user's authenticator app (TOTP) when enrolled, otherwise a 6-digit code
 * sent by e-mail. A device can be trusted for 30 days to skip the e-mail code.
 */
class AuthController extends Controller
{
    private const MAX_LOGIN_ATTEMPTS = 5;

    private const TRUST_COOKIE = 'otp_trust';

    private const TRUST_DAYS = 30;

    private function throttleKey(Request $request): string
    {
        return Str::lower((string) $request->input('email')).'|'.$request->ip();
    }

    public function login(Request $request)
    {
        $request->validate(['email' => 'required|email', 'password' => 'required|string']);
        $key = $this->throttleKey($request);

        if (RateLimiter::tooManyAttempts($key, self::MAX_LOGIN_ATTEMPTS)) {
            return back()->withErrors(['email' => __('auth.throttle', ['seconds' => RateLimiter::availableIn($key)])])->onlyInput('email');
        }

        if (! Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            RateLimiter::hit($key, 60);
            throw ValidationException::withMessages(['email' => __('auth.failed')]);
        }

        RateLimiter::clear($key);
        /** @var User $user */
        $user = Auth::user();

        if ($user->hasTwoFactorEnabled()) {
            return $this->startSecondFactor($request, $user, 'totp');
        }
        if ($user->email_otp_enabled && ! $this->isTrustedDevice($request, $user)) {
            $this->sendLoginCode($request, $user);

            return $this->startSecondFactor($request, $user, 'email');
        }

        return $this->completeWebLogin($request, $user);
    }

    private function startSecondFactor(Request $request, User $user, string $method)
    {
        Auth::logout();
        $request->session()->put('2fa:user:id', $user->id);
        $request->session()->put('2fa:method', $method);
        $request->session()->put('2fa:remember', $request->boolean('remember'));

        return redirect()->route('2fa.challenge');
    }

    private function completeWebLogin(Request $request, User $user)
    {
        $request->session()->regenerate();

        return match ($user->role) {
            'admin', 'superadmin' => redirect()->intended(route('admin.dashboard', [], false)),
            'worker' => redirect()->intended(route('worker.dashboard', [], false)),
            default => redirect()->intended(route('home', [], false)),
        };
    }

    public function showTwoFactorChallenge(Request $request)
    {
        if (! $request->session()->has('2fa:user:id')) {
            return redirect()->route('login');
        }

        return view('auth.two-factor-challenge', ['method' => $request->session()->get('2fa:method', 'totp')]);
    }

    public function verifyTwoFactor(Request $request)
    {
        $request->validate(['code' => 'required|string|max:64']);
        $user = User::find($request->session()->get('2fa:user:id'));
        $method = (string) $request->session()->get('2fa:method', 'totp');

        if (! $user) {
            $request->session()->forget(['2fa:user:id', '2fa:method', '2fa:remember']);

            return redirect()->route('login');
        }

        $code = trim((string) $request->input('code'));
        $valid = $method === 'email'
            ? VerificationCodes::verify($user->email, VerificationCodes::PURPOSE_LOGIN, $code)
            : $this->verifyTotpOrRecovery($user, $code);

        if (! $valid) {
            return back()->withErrors(['code' => __('auth.otp.invalid')]);
        }

        $remember = (bool) $request->session()->pull('2fa:remember', false);
        $request->session()->forget(['2fa:user:id', '2fa:method']);
        Auth::login($user, $remember);

        if ($method === 'email' && $request->boolean('trust_device')) {
            cookie()->queue(self::TRUST_COOKIE, $this->trustValue($user), self::TRUST_DAYS * 24 * 60, null, null, null, true, false, 'lax');
        }

        return $this->completeWebLogin($request, $user);
    }

    /** Re-send the e-mail code during the challenge (rate limited in routes). */
    public function resendOtp(Request $request)
    {
        $user = User::find($request->session()->get('2fa:user:id'));
        if (! $user || $request->session()->get('2fa:method') !== 'email') {
            return redirect()->route('login');
        }
        $this->sendLoginCode($request, $user);

        return back()->with('status', __('auth.verify.resent'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    private function verifyTotpOrRecovery(User $user, string $code): bool
    {
        if (! $user->hasTwoFactorEnabled()) {
            return false;
        }
        if (Totp::verify($user->two_factor_secret, $code)) {
            return true;
        }
        $codes = $user->two_factor_recovery_codes ?? [];
        $entered = strtoupper(str_replace([' ', '-'], '', $code));
        foreach ($codes as $index => $recoveryCode) {
            if (hash_equals(strtoupper(str_replace('-', '', $recoveryCode)), $entered)) {
                unset($codes[$index]);
                $user->two_factor_recovery_codes = array_values($codes);
                $user->save();

                return true;
            }
        }

        return false;
    }

    private function sendLoginCode(Request $request, User $user): void
    {
        $code = VerificationCodes::issue($user->email, VerificationCodes::PURPOSE_LOGIN, $user->id, $request->ip());
        $business = (string) BusinessSetting::get('business_name', config('app.name'));
        Mail::to($user->email)->send(new VerificationCodeMail($code, VerificationCodes::PURPOSE_LOGIN, app()->getLocale(), $business));
    }

    /** Cookie value is bound to the user and their current password hash. */
    private function trustValue(User $user): string
    {
        return $user->id.'.'.hash_hmac('sha256', 'otp-trust|'.$user->id.'|'.$user->password, (string) config('app.key'));
    }

    private function isTrustedDevice(Request $request, User $user): bool
    {
        $cookie = (string) $request->cookie(self::TRUST_COOKIE, '');

        return $cookie !== '' && hash_equals($this->trustValue($user), $cookie);
    }
}
