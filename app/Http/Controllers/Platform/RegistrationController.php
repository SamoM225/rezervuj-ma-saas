<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Mail\VerificationCodeMail;
use App\Models\BusinessSetting;
use App\Models\City;
use App\Models\Tenant;
use App\Models\User;
use App\Support\Locales;
use App\Support\Tenancy;
use App\Support\VerificationCodes;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Self-service signup of a business (tenant) with its first admin account.
 * Flow: form → tenant + owner created (unverified) → 6-digit e-mail code →
 * verified, logged in, redirected to the tenant admin.
 */
class RegistrationController extends Controller
{
    public const COUNTRIES = ['SK', 'CZ', 'PL', 'HU', 'AT', 'DE', 'GB', 'IE', 'NL', 'BE', 'FR', 'ES', 'IT', 'PT', 'SI', 'HR', 'RO', 'BG', 'US', 'CA', 'AU', 'OTHER'];

    /** Documents the tenant must accept (checkbox name => stored document key). */
    public const REQUIRED_DECLARATIONS = [
        'accept_terms' => 'terms',
        'accept_dpa' => 'dpa',
        'accept_controller' => 'controller_declaration',
        'accept_accuracy' => 'accuracy',
        'accept_age' => 'age',
    ];

    public function create(): View
    {
        return view('platform.register', [
            'categories' => array_keys(config('tenancy.categories')),
            'countries' => self::COUNTRIES,
            'locales' => config('tenancy.locales'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $rules = [
            'business_name' => ['required', 'string', 'min:2', 'max:80'],
            'slug' => ['required', 'string', 'min:3', 'max:50', 'regex:/^'.config('tenancy.slug_pattern').'$/', Rule::unique('tenants', 'slug')],
            'category' => ['required', Rule::in(array_keys(config('tenancy.categories')))],
            'country' => ['required', Rule::in(self::COUNTRIES)],
            'city' => ['nullable', 'string', 'max:80'],
            'locale' => ['required', Rule::in(config('tenancy.locales'))],
            'email' => ['required', 'email:rfc', 'max:255'],
            'password' => ['required', 'string', 'min:10', 'max:255', 'confirmed'],
            'accept_marketing' => ['nullable', 'boolean'],
        ];
        foreach (array_keys(self::REQUIRED_DECLARATIONS) as $checkbox) {
            $rules[$checkbox] = ['accepted'];
        }
        $data = $request->validate($rules);

        if (Tenant::isReservedSlug($data['slug'])) {
            return back()->withErrors(['slug' => __('auth.register.slug_reserved')])->withInput();
        }

        $email = mb_strtolower(trim($data['email']));

        [$tenant, $owner] = DB::transaction(function () use ($data, $email, $request) {
            $tenant = Tenant::create([
                'slug' => $data['slug'],
                'name' => $data['business_name'],
                'category' => $data['category'],
                'country' => $data['country'] === 'OTHER' ? 'XX' : $data['country'],
                'city' => $data['city'] ?? null,
                'email' => $email,
                'locale' => $data['locale'],
                'timezone' => self::timezoneFor($data['country']),
                'currency' => self::currencyFor($data['country']),
                'plan' => Tenant::PLAN_FREE,
                'status' => Tenant::STATUS_ACTIVE,
                // Stays unlisted until the e-mail (2FA) code is confirmed, so an
                // unverified sign-up cannot appear in the public directory.
                'is_public' => false,
            ]);

            $owner = Tenancy::runAs($tenant, function () use ($tenant, $data, $email) {
                $owner = User::create([
                    'name' => $data['business_name'],
                    'email' => $email,
                    'password' => Hash::make($data['password']),
                    'role' => 'superadmin',
                    'calendar_color' => '#C19A3E',
                ]);
                $tenant->forceFill(['owner_user_id' => $owner->id])->save();

                BusinessSetting::set('business_name', $tenant->name, 'string');
                BusinessSetting::set('support_email', $email, 'string');
                BusinessSetting::set('default_language', $data['locale'], 'string');
                BusinessSetting::set('available_languages', [$data['locale']], 'json');
                BusinessSetting::set('allow_online_booking', true, 'boolean');
                BusinessSetting::set('send_email_notifications', true, 'boolean');
                if (! empty($data['city'])) {
                    City::create(['name' => $data['city'], 'address' => '']);
                }

                return $owner;
            });

            $accepted = self::REQUIRED_DECLARATIONS;
            if ($request->boolean('accept_marketing')) {
                $accepted['accept_marketing'] = 'marketing';
            }
            $rows = [];
            foreach ($accepted as $document) {
                $rows[] = [
                    'tenant_id' => $tenant->id,
                    'user_id' => $owner->id,
                    'email' => $email,
                    'document' => $document,
                    'version' => (string) config("legal.versions.{$document}", '1'),
                    'ip' => $request->ip(),
                    'user_agent' => Str::limit((string) $request->userAgent(), 250, ''),
                    'accepted_at' => now(),
                ];
            }
            DB::table('legal_acceptances')->insert($rows);

            return [$tenant, $owner];
        });

        $this->sendSignupCode($owner, $tenant, $request);
        $request->session()->put('signup', ['user_id' => $owner->id, 'tenant_id' => $tenant->id]);

        return redirect(Locales::route('register.verify'));
    }

    public function verify(Request $request): View|RedirectResponse
    {
        $owner = $this->pendingOwner($request);
        if (! $owner) {
            return redirect(Locales::route('register'));
        }

        return view('platform.verify', ['email' => $owner->email]);
    }

    public function confirm(Request $request): RedirectResponse
    {
        $owner = $this->pendingOwner($request);
        if (! $owner) {
            return redirect(Locales::route('register'));
        }
        $request->validate(['code' => ['required', 'string', 'max:12']]);

        if (! VerificationCodes::verify($owner->email, VerificationCodes::PURPOSE_SIGNUP, (string) $request->input('code'))) {
            return back()->withErrors(['code' => __('auth.verify.invalid')]);
        }

        $owner->forceFill(['email_verified_at' => now()])->save();
        $tenant = Tenant::findOrFail($request->session()->get('signup.tenant_id'));
        // E-mail confirmed — the business may now be listed publicly.
        $tenant->forceFill(['is_public' => true])->save();
        $request->session()->forget('signup');

        Auth::login($owner);
        $request->session()->regenerate();

        return redirect()->route('admin.dashboard', ['tenant' => $tenant->slug])->with('success', __('auth.verify.welcome'));
    }

    public function resend(Request $request): RedirectResponse
    {
        $owner = $this->pendingOwner($request);
        if (! $owner) {
            return redirect(Locales::route('register'));
        }
        $tenant = Tenant::findOrFail($request->session()->get('signup.tenant_id'));
        $this->sendSignupCode($owner, $tenant, $request);

        return back()->with('status', __('auth.verify.resent'));
    }

    private function pendingOwner(Request $request): ?User
    {
        $userId = $request->session()->get('signup.user_id');

        return $userId ? User::acrossTenants()->whereNull('email_verified_at')->find($userId) : null;
    }

    private function sendSignupCode(User $owner, Tenant $tenant, Request $request): void
    {
        $code = VerificationCodes::issue($owner->email, VerificationCodes::PURPOSE_SIGNUP, $owner->id, $request->ip());
        Mail::to($owner->email)->send(new VerificationCodeMail($code, VerificationCodes::PURPOSE_SIGNUP, $tenant->locale, config('app.name')));
    }

    public static function timezoneFor(string $country): string
    {
        return match ($country) {
            'CZ' => 'Europe/Prague', 'PL' => 'Europe/Warsaw', 'HU' => 'Europe/Budapest', 'AT' => 'Europe/Vienna',
            'DE' => 'Europe/Berlin', 'GB' => 'Europe/London', 'IE' => 'Europe/Dublin', 'NL' => 'Europe/Amsterdam',
            'BE' => 'Europe/Brussels', 'FR' => 'Europe/Paris', 'ES' => 'Europe/Madrid', 'IT' => 'Europe/Rome',
            'PT' => 'Europe/Lisbon', 'SI' => 'Europe/Ljubljana', 'HR' => 'Europe/Zagreb', 'RO' => 'Europe/Bucharest',
            'BG' => 'Europe/Sofia', 'US' => 'America/New_York', 'CA' => 'America/Toronto', 'AU' => 'Australia/Sydney',
            default => 'Europe/Bratislava',
        };
    }

    public static function currencyFor(string $country): string
    {
        return match ($country) {
            'CZ' => 'CZK', 'PL' => 'PLN', 'HU' => 'HUF', 'GB' => 'GBP', 'RO' => 'RON', 'BG' => 'BGN',
            'US' => 'USD', 'CA' => 'CAD', 'AU' => 'AUD',
            default => 'EUR',
        };
    }
}
