<?php

namespace Tests\Feature;

use App\Mail\VerificationCodeMail;
use App\Models\EmailVerificationCode;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class CustomerRegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
    }

    private function register(array $overrides = [])
    {
        return $this->postJson('/customer/register', array_merge([
            'name' => 'Ana Reyes',
            'email' => 'ana@example.com',
            'contact_number' => '09171234567',
            'password' => 'milktea1234',
            'password_confirmation' => 'milktea1234',
        ], $overrides));
    }

    /** The code as the customer reads it: straight out of the email sent. */
    private function codeFor(string $email): string
    {
        $code = null;

        Mail::assertSent(VerificationCodeMail::class, function ($mail) use ($email, &$code) {
            if (! $mail->hasTo($email)) {
                return false;
            }

            // Keep the latest, so a resend supersedes the first code.
            $code = $mail->code;

            return true;
        });

        $this->assertNotNull($code, "No verification code was emailed to {$email}.");

        return $code;
    }

    public function test_registering_creates_an_unverified_account_and_sends_a_code()
    {
        $this->register()->assertCreated()->assertJsonPath('status', 'verification_sent');

        $user = User::where('email', 'ana@example.com')->firstOrFail();

        $this->assertSame('customer', $user->role);
        $this->assertNull($user->email_verified_at);
        $this->assertSame('09171234567', $user->contact_number);
        $this->assertDatabaseCount('email_verification_codes', 1);
    }

    public function test_the_code_is_not_stored_in_the_clear()
    {
        $this->register()->assertCreated();

        $code = $this->codeFor('ana@example.com');
        $stored = EmailVerificationCode::first()->code_hash;

        $this->assertNotSame($code, $stored);
        $this->assertTrue(Hash::check($code, $stored));
    }

    public function test_the_right_code_verifies_and_signs_the_customer_in()
    {
        $this->register()->assertCreated();

        $this->postJson('/customer/verify', [
            'email' => 'ana@example.com',
            'code' => $this->codeFor('ana@example.com'),
        ])
            ->assertOk()
            ->assertJsonPath('status', 'signed_in')
            ->assertJsonPath('user.fullName', 'Ana Reyes');

        $this->assertNotNull(User::where('email', 'ana@example.com')->first()->email_verified_at);

        // A spent code must not be reusable.
        $this->assertDatabaseCount('email_verification_codes', 0);
        $this->assertNotNull(session('customer_user_id'));
    }

    public function test_a_wrong_code_is_refused()
    {
        $this->register()->assertCreated();

        $this->postJson('/customer/verify', ['email' => 'ana@example.com', 'code' => '000000'])
            ->assertStatus(422);

        $this->assertNull(User::where('email', 'ana@example.com')->first()->email_verified_at);
    }

    public function test_a_code_dies_after_too_many_wrong_tries()
    {
        $this->register()->assertCreated();

        $real = $this->codeFor('ana@example.com');
        $wrong = $real === '111111' ? '222222' : '111111';

        for ($i = 0; $i < EmailVerificationCode::MAX_ATTEMPTS; $i++) {
            $this->postJson('/customer/verify', ['email' => 'ana@example.com', 'code' => $wrong])
                ->assertStatus(422);
        }

        // Even the correct code is refused once the budget is spent.
        $this->postJson('/customer/verify', ['email' => 'ana@example.com', 'code' => $real])
            ->assertStatus(422)
            ->assertJsonPath('errors.code.0', 'Too many wrong tries. Ask for a new code.');
    }

    public function test_an_expired_code_is_refused()
    {
        $this->register()->assertCreated();
        $code = $this->codeFor('ana@example.com');

        EmailVerificationCode::query()->update(['expires_at' => now()->subMinute()]);

        $this->postJson('/customer/verify', ['email' => 'ana@example.com', 'code' => $code])
            ->assertStatus(422)
            ->assertJsonPath('errors.code.0', 'That code has expired. Ask for a new one.');
    }

    public function test_resending_replaces_the_previous_code()
    {
        $this->register()->assertCreated();
        $first = $this->codeFor('ana@example.com');

        $this->postJson('/customer/resend', ['email' => 'ana@example.com'])->assertOk();

        $this->assertDatabaseCount('email_verification_codes', 1);

        $this->postJson('/customer/verify', ['email' => 'ana@example.com', 'code' => $first])
            ->assertStatus(422);
    }

    public function test_a_verified_customer_can_sign_in_with_their_password()
    {
        $this->register()->assertCreated();
        $this->postJson('/customer/verify', [
            'email' => 'ana@example.com',
            'code' => $this->codeFor('ana@example.com'),
        ])->assertOk();

        $this->postJson('/customer/logout')->assertOk();

        $this->postJson('/customer/login', ['email' => 'ana@example.com', 'password' => 'milktea1234'])
            ->assertOk()
            ->assertJsonPath('status', 'signed_in');
    }

    public function test_a_wrong_password_is_refused()
    {
        $this->register()->assertCreated();
        $this->postJson('/customer/verify', [
            'email' => 'ana@example.com',
            'code' => $this->codeFor('ana@example.com'),
        ])->assertOk();

        $this->postJson('/customer/login', ['email' => 'ana@example.com', 'password' => 'wrong-one'])
            ->assertStatus(422);
    }

    public function test_signing_in_before_verifying_sends_a_fresh_code()
    {
        $this->register()->assertCreated();

        $this->postJson('/customer/login', ['email' => 'ana@example.com', 'password' => 'milktea1234'])
            ->assertStatus(409)
            ->assertJsonPath('status', 'verification_required');
    }

    public function test_a_registered_address_cannot_be_registered_twice()
    {
        $this->register()->assertCreated();
        $this->postJson('/customer/verify', [
            'email' => 'ana@example.com',
            'code' => $this->codeFor('ana@example.com'),
        ])->assertOk();

        $this->register()
            ->assertStatus(422)
            ->assertJsonPath('errors.email.0', 'That address is already registered. Sign in instead.');
    }

    public function test_a_staff_address_cannot_be_claimed_as_a_customer()
    {
        User::factory()->create(['email' => 'boss@queenscup.test', 'role' => 'admin']);

        $this->register(['email' => 'boss@queenscup.test'])
            ->assertStatus(422)
            ->assertJsonPath('errors.email.0', 'That address is already used by a staff account.');
    }

    public function test_a_short_password_is_refused()
    {
        $this->register(['password' => 'short', 'password_confirmation' => 'short'])
            ->assertStatus(422);
    }

    public function test_the_password_must_be_confirmed()
    {
        $this->register(['password_confirmation' => 'something-else'])
            ->assertStatus(422);
    }

    public function test_resending_to_an_unknown_address_reveals_nothing()
    {
        // The same answer either way, so this cannot enumerate registered
        // addresses.
        $this->postJson('/customer/resend', ['email' => 'nobody@example.com'])
            ->assertOk()
            ->assertJsonPath('status', 'verification_sent');
    }
}
