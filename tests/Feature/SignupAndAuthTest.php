<?php

namespace Tests\Feature;

use App\Mail\VerificationCodeMail;
use App\Models\BusinessSetting;
use App\Models\Tenant;
use App\Models\User;
use App\Support\Tenancy;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class SignupAndAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
        Notification::fake();
    }

    public function test_business_can_register_verify_email_and_land_in_its_admin(): void
    {
        $this->get('/register')->assertOk()->assertSee('name="accept_dpa"', false);

        $this->post('/register', $this->registrationPayload())->assertRedirect('/register/verify');

        $tenant = Tenant::where('slug', 'studio-lea')->firstOrFail();
        $owner = User::acrossTenants()->where('tenant_id', $tenant->id)->where('email', 'lea@example.test')->firstOrFail();
        $this->assertSame('superadmin', $owner->role);
        $this->assertNull($owner->email_verified_at);
        $this->assertSame($owner->id, $tenant->owner_user_id);
        $this->assertSame('Europe/Prague', $tenant->timezone);
        $this->assertSame('CZK', $tenant->currency);
        $this->assertDatabaseCount('legal_acceptances', 5);
        $this->assertDatabaseHas('legal_acceptances', ['tenant_id' => $tenant->id, 'document' => 'dpa', 'version' => config('legal.versions.dpa')]);
        $this->assertDatabaseHas('cities', ['tenant_id' => $tenant->id, 'name' => 'Brno']);

        Tenancy::set($tenant);
        $this->assertSame('Studio Lea', BusinessSetting::get('business_name'));
        $this->assertSame(['cs'], BusinessSetting::get('available_languages'));
        Tenancy::forget();

        $code = $this->sentCode('lea@example.test', 'signup');
        $this->get('/register/verify')->assertOk()->assertSee('lea@example.test');
        $this->post('/register/verify', ['code' => '000000'])->assertSessionHasErrors('code');
        $this->post('/register/verify', ['code' => $code])->assertRedirect('/studio-lea/admin/dashboard');

        $this->assertNotNull($owner->fresh()->email_verified_at);
        $this->assertAuthenticatedAs($owner);
        $this->get('/studio-lea/admin/dashboard')->assertOk();
    }

    public function test_reserved_taken_or_invalid_slugs_are_rejected(): void
    {
        $this->post('/register', $this->registrationPayload(['slug' => 'admin']))->assertSessionHasErrors('slug');
        $this->post('/register', $this->registrationPayload(['slug' => 'Nie Slug!']))->assertSessionHasErrors('slug');
        Tenant::factory()->create(['slug' => 'studio-lea']);
        $this->post('/register', $this->registrationPayload())->assertSessionHasErrors('slug');
        $this->post('/register', $this->registrationPayload(['accept_dpa' => null]))->assertSessionHasErrors('accept_dpa');
        $this->assertSame(1, Tenant::count());
    }

    public function test_login_requires_an_email_code_and_a_trusted_device_skips_it_next_time(): void
    {
        $tenant = Tenant::factory()->create(['slug' => 'salon-x']);
        Tenancy::set($tenant);
        $admin = User::factory()->admin()->create(['email' => 'admin@example.test', 'password' => Hash::make('very-long-safe-password')]);
        Tenancy::forget();

        $this->post('/salon-x/login', ['email' => 'admin@example.test', 'password' => 'very-long-safe-password'])
            ->assertRedirect('/salon-x/2fa/challenge');
        $this->assertGuest();

        $this->get('/salon-x/2fa/challenge')->assertOk()->assertSee('name="trust_device"', false);
        $this->post('/salon-x/2fa/challenge', ['code' => '123456'])->assertSessionHasErrors('code');

        $code = $this->sentCode('admin@example.test', 'login');
        $response = $this->post('/salon-x/2fa/challenge', ['code' => $code, 'trust_device' => 1])->assertRedirect('/salon-x/admin/dashboard');
        $this->assertAuthenticatedAs($admin);

        $trust = collect($response->headers->getCookies())->firstWhere(fn ($cookie) => $cookie->getName() === 'otp_trust');
        $this->assertNotNull($trust, 'trusted-device cookie is issued');

        $this->post('/salon-x/logout');
        $this->assertGuest();

        $this->withUnencryptedCookie('otp_trust', $trust->getValue())
            ->post('/salon-x/login', ['email' => 'admin@example.test', 'password' => 'very-long-safe-password'])
            ->assertRedirect('/salon-x/admin/dashboard');
        $this->assertAuthenticatedAs($admin);
    }

    public function test_password_can_be_reset_from_the_tenant_login_page(): void
    {
        $tenant = Tenant::factory()->create(['slug' => 'salon-y']);
        Tenancy::set($tenant);
        $user = User::factory()->worker()->create(['email' => 'w@example.test']);
        Tenancy::forget();

        $this->get('/salon-y/forgot-password')->assertOk();
        $this->post('/salon-y/forgot-password', ['email' => 'w@example.test'])->assertSessionHas('status');

        $token = null;
        Notification::assertSentTo($user, ResetPassword::class, function (ResetPassword $notification) use (&$token) {
            $token = $notification->token;

            return true;
        });

        $this->get('/salon-y/reset-password/'.$token.'?email=w@example.test')->assertOk();
        $this->post('/salon-y/reset-password', [
            'token' => $token, 'email' => 'w@example.test', 'password' => 'brand-new-password-1', 'password_confirmation' => 'brand-new-password-1',
        ])->assertRedirect('/salon-y/login');

        $this->assertTrue(Hash::check('brand-new-password-1', $user->fresh()->password));
    }

    /** @return array<string, mixed> */
    private function registrationPayload(array $override = []): array
    {
        return array_replace([
            'business_name' => 'Studio Lea', 'slug' => 'studio-lea', 'category' => 'beauty', 'country' => 'CZ', 'city' => 'Brno', 'locale' => 'cs',
            'email' => 'lea@example.test', 'password' => 'very-long-safe-password', 'password_confirmation' => 'very-long-safe-password',
            'accept_terms' => 1, 'accept_dpa' => 1, 'accept_controller' => 1, 'accept_accuracy' => 1, 'accept_age' => 1,
        ], $override);
    }

    private function sentCode(string $email, string $purpose): string
    {
        $code = null;
        Mail::assertSent(VerificationCodeMail::class, function (VerificationCodeMail $mail) use ($email, $purpose, &$code) {
            if ($mail->purpose === $purpose && $mail->hasTo($email)) {
                $code = $mail->code;

                return true;
            }

            return false;
        });
        $this->assertNotNull($code);

        return $code;
    }
}
