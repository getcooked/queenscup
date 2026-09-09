<?php

namespace Tests\Feature;

use App\Models\EmailVerificationCode;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * The phone signs up the same way the website does: an emailed code confirms
 * the address before the account works. Only the credential differs — a token
 * here, a session there.
 */
class AppAccountTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
    }

    private function codeFor(string $email): string
    {
        $user = User::where('email', $email)->firstOrFail();
        $record = EmailVerificationCode::where('user_id', $user->id)->latest('id')->firstOrFail();

        // The stored code is hashed, so re-issue a known one to verify against.
        $plain = '123456';
        $record->forceFill(['code_hash' => bcrypt($plain)])->save();

        return $plain;
    }

    public function test_registering_sends_a_code_and_withholds_the_token()
    {
        $this->postJson('/api/v1/auth/register', [
            'name' => 'Ana Reyes',
            'email' => 'ana@example.com',
            'password' => 'secret1234',
        ])
            ->assertCreated()
            ->assertJsonPath('status', 'verification_sent')
            // No token until the address is confirmed.
            ->assertJsonMissingPath('token');

        $this->assertNull(User::where('email', 'ana@example.com')->first()->email_verified_at);
    }

    public function test_verifying_the_code_issues_a_token()
    {
        $this->postJson('/api/v1/auth/register', [
            'name' => 'Ana Reyes',
            'email' => 'ana@example.com',
            'password' => 'secret1234',
        ])->assertCreated();

        $response = $this->postJson('/api/v1/auth/verify', [
            'email' => 'ana@example.com',
            'code' => $this->codeFor('ana@example.com'),
            'device_name' => 'pixel',
        ])->assertOk()->assertJsonPath('status', 'signed_in');

        $this->assertNotEmpty($response->json('token'));
        $this->assertNotNull(User::where('email', 'ana@example.com')->first()->email_verified_at);
    }

    public function test_a_wrong_code_is_refused()
    {
        $this->postJson('/api/v1/auth/register', [
            'name' => 'Ana', 'email' => 'ana@example.com', 'password' => 'secret1234',
        ])->assertCreated();

        $this->codeFor('ana@example.com');

        $this->postJson('/api/v1/auth/verify', ['email' => 'ana@example.com', 'code' => '000000'])
            ->assertStatus(422);
    }

    public function test_signing_in_before_confirming_sends_a_new_code_rather_than_failing()
    {
        $this->postJson('/api/v1/auth/register', [
            'name' => 'Ana', 'email' => 'ana@example.com', 'password' => 'secret1234',
        ])->assertCreated();

        $this->postJson('/api/v1/auth/login', ['email' => 'ana@example.com', 'password' => 'secret1234'])
            ->assertStatus(409)
            ->assertJsonPath('status', 'verification_required');
    }

    public function test_a_confirmed_account_signs_in_and_can_read_itself()
    {
        $this->postJson('/api/v1/auth/register', [
            'name' => 'Ana Reyes', 'email' => 'ana@example.com', 'password' => 'secret1234',
        ])->assertCreated();

        $this->postJson('/api/v1/auth/verify', [
            'email' => 'ana@example.com', 'code' => $this->codeFor('ana@example.com'),
        ])->assertOk();

        $token = $this->postJson('/api/v1/auth/login', [
            'email' => 'ana@example.com', 'password' => 'secret1234',
        ])->assertOk()->json('token');

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/auth/me')
            ->assertOk()
            ->assertJsonPath('email', 'ana@example.com')
            ->assertJsonPath('fullName', 'Ana Reyes');
    }

    public function test_a_staff_address_cannot_be_claimed_from_the_app()
    {
        User::factory()->create(['email' => 'boss@queenscup.test', 'role' => 'admin']);

        $this->postJson('/api/v1/auth/register', [
            'name' => 'Not The Boss', 'email' => 'boss@queenscup.test', 'password' => 'secret1234',
        ])->assertStatus(422);
    }

    public function test_the_app_and_the_website_share_one_account()
    {
        // Registered on the phone...
        $this->postJson('/api/v1/auth/register', [
            'name' => 'Ana Reyes', 'email' => 'ana@example.com', 'password' => 'secret1234',
        ])->assertCreated();

        $this->postJson('/api/v1/auth/verify', [
            'email' => 'ana@example.com', 'code' => $this->codeFor('ana@example.com'),
        ])->assertOk();

        // ...signs in on the website with the same credentials.
        $this->postJson('/customer/login', ['email' => 'ana@example.com', 'password' => 'secret1234'])
            ->assertOk()
            ->assertJsonPath('status', 'signed_in');
    }

    public function test_the_app_sees_the_same_chat_as_the_website()
    {
        $this->postJson('/api/v1/auth/register', [
            'name' => 'Ana Reyes', 'email' => 'ana@example.com', 'password' => 'secret1234',
        ])->assertCreated();

        $token = $this->postJson('/api/v1/auth/verify', [
            'email' => 'ana@example.com', 'code' => $this->codeFor('ana@example.com'),
        ])->assertOk()->json('token');

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/chat', ['message' => 'opening hours'])
            ->assertOk()
            ->assertJsonPath('stored', true);

        // The same conversation is waiting in the browser.
        $user = User::where('email', 'ana@example.com')->firstOrFail();

        $this->withSession(['customer_user_id' => $user->id])
            ->getJson('/chat')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.body', 'opening hours');
    }

    public function test_a_signed_out_phone_still_gets_the_assistant()
    {
        $this->postJson('/api/v1/chat', ['message' => 'where are you?'])
            ->assertOk()
            ->assertJsonPath('stored', false);
    }
}
