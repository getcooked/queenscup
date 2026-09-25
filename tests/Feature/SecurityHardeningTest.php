<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityHardeningTest extends TestCase
{
    use RefreshDatabase;

    public function test_pages_use_security_headers_and_unique_script_nonces(): void
    {
        $first = $this->get('/staff-login')->assertOk()
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('X-Frame-Options', 'DENY')
            ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin')
            ->assertHeader('Cross-Origin-Opener-Policy', 'same-origin')
            ->assertHeader('Cross-Origin-Resource-Policy', 'same-origin')
            ->assertHeader('X-Permitted-Cross-Domain-Policies', 'none');

        $policy = $first->headers->get('Content-Security-Policy');
        $this->assertStringContainsString("object-src 'none'", $policy);
        $this->assertStringContainsString("frame-ancestors 'none'", $policy);
        $this->assertStringContainsString("frame-src 'none'", $policy);
        $this->assertStringNotContainsString('upgrade-insecure-requests', $policy);
        preg_match("/nonce-([^']+)/", $policy, $match);
        $first->assertSee('nonce="'.$match[1].'"', false);
        $this->assertStringContainsString('no-store', $first->headers->get('Cache-Control'));

        $second = $this->get('/staff-login');
        $this->assertNotSame($policy, $second->headers->get('Content-Security-Policy'));
    }

    public function test_hsts_is_only_sent_over_https(): void
    {
        $this->get('http://localhost/staff-login')->assertHeaderMissing('Strict-Transport-Security');
        $secure = $this->get('https://localhost/staff-login')->assertHeader('Strict-Transport-Security', 'max-age=31536000');
        $this->assertStringContainsString('upgrade-insecure-requests', $secure->headers->get('Content-Security-Policy'));
    }

    public function test_reservation_reference_lookup_is_rate_limited(): void
    {
        for ($attempt = 0; $attempt < 20; $attempt++) {
            $this->getJson('/api/v1/reservations/QC-GUESS'.$attempt)->assertNotFound();
        }
        $this->getJson('/api/v1/reservations/QC-GUESS')->assertStatus(429);
    }

    public function test_verification_cannot_sign_in_an_already_verified_customer(): void
    {
        $user = User::factory()->create(['role' => 'customer', 'email_verified_at' => now()]);
        $payload = ['email' => $user->email, 'code' => '000000'];

        $this->postJson('/customer/verify', $payload)
            ->assertUnprocessable()->assertSessionMissing('customer_user_id');
        $this->postJson('/api/v1/auth/verify', $payload)->assertUnprocessable();
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_customer_login_is_rate_limited(): void
    {
        for ($attempt = 0; $attempt < 10; $attempt++) {
            $this->postJson('/customer/login', [])->assertUnprocessable();
        }
        $this->postJson('/customer/login', [])->assertStatus(429)
            ->assertHeader('X-Content-Type-Options', 'nosniff');
    }
}
