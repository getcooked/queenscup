<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerPortalTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_login_page_offers_sign_in_and_registration()
    {
        $this->get('/orders')
            ->assertOk()
            ->assertSee('Create Account')
            ->assertSee('signinForm', false)
            ->assertSee('registerForm', false)
            ->assertSee('verifyForm', false)
            ->assertSee('Confirm Password');
    }

    public function test_the_old_guest_name_and_email_entry_is_gone()
    {
        $html = $this->get('/orders')->assertOk()->getContent();

        // Ordering no longer starts from a bare name and Gmail address.
        $this->assertStringNotContainsString('id="guestName"', $html);
        $this->assertStringNotContainsString('id="guestEmail"', $html);
        $this->assertStringNotContainsString('id="guestOtp"', $html);
    }

    public function test_the_customer_gets_a_full_side_navigation()
    {
        $html = $this->get('/orders')->assertOk()->getContent();

        // Menu, active reservations, history and profile.
        $this->assertStringContainsString('data-page="pos"', $html);
        $this->assertStringContainsString('data-page="orders"', $html);
        $this->assertStringContainsString('data-page="history"', $html);
        $this->assertStringContainsString('data-page="profile"', $html);
        $this->assertStringContainsString('page-history', $html);
        $this->assertStringContainsString('Reservation History', $html);
    }

    public function test_the_page_wires_the_account_endpoints()
    {
        $this->get('/orders')
            ->assertOk()
            ->assertSee('handleCustomerRegister', false)
            ->assertSee('handleCustomerVerify', false)
            ->assertSee('handleCustomerSignIn', false)
            ->assertSee('customer\/register', false)
            ->assertSee('customer\/verify', false);
    }
}
