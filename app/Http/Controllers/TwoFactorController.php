<?php

namespace App\Http\Controllers;

use App\Models\BusinessSetting;
use App\Support\Totp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * Optional two-factor authentication (TOTP / authenticator app) management.
 */
class TwoFactorController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();

        $setupSecret = $request->session()->get('2fa:setup_secret');
        $otpauthUri = null;
        if ($setupSecret) {
            $issuer = BusinessSetting::get('business_name', config('app.name', 'rezervuj-ma'));
            $otpauthUri = Totp::otpauthUri($setupSecret, $user->email, $issuer);
        }

        return view('account.two-factor', [
            'enabled' => $user->hasTwoFactorEnabled(),
            'setupSecret' => $setupSecret,
            'otpauthUri' => $otpauthUri,
            'recoveryCodes' => $request->session()->get('2fa:recovery_codes'),
        ]);
    }

    /** Begin enrollment: generate a secret kept in the session until confirmed. */
    public function enable(Request $request)
    {
        if ($request->user()->hasTwoFactorEnabled()) {
            return back();
        }
        $request->session()->put('2fa:setup_secret', Totp::generateSecret());

        return redirect()->route('account.2fa.show');
    }

    /** Confirm the code, then persist the secret + recovery codes. */
    public function confirm(Request $request)
    {
        $request->validate(['code' => 'required|string']);

        $secret = $request->session()->get('2fa:setup_secret');
        if (! $secret) {
            return redirect()->route('account.2fa.show');
        }

        if (! Totp::verify($secret, $request->input('code'))) {
            return back()->withErrors(['code' => __('ui.neplatny_overovaci_kod_skuste_to_znova')]);
        }

        $recoveryCodes = Totp::recoveryCodes();

        $user = $request->user();
        $user->two_factor_secret = $secret;
        $user->two_factor_recovery_codes = $recoveryCodes;
        $user->two_factor_confirmed_at = now();
        $user->save();

        $request->session()->forget('2fa:setup_secret');
        // Show the recovery codes once.
        $request->session()->flash('2fa:recovery_codes', $recoveryCodes);

        return redirect()->route('account.2fa.show')
            ->with('success', __('ui.dvojfaktorove_overenie_bolo_zapnute'));
    }

    /** Disable 2FA (requires the current password). */
    public function disable(Request $request)
    {
        $request->validate(['password' => 'required|string']);

        $user = $request->user();
        if (! Hash::check($request->input('password'), $user->password)) {
            return back()->withErrors(['password' => __('ui.nespravne_heslo')]);
        }

        $user->two_factor_secret = null;
        $user->two_factor_recovery_codes = null;
        $user->two_factor_confirmed_at = null;
        $user->save();

        $request->session()->forget(['2fa:setup_secret', '2fa:recovery_codes']);

        return redirect()->route('account.2fa.show')
            ->with('success', __('ui.dvojfaktorove_overenie_bolo_vypnute'));
    }
}
