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
    public function test_the_customer_sidebar_is_not_hidden_by_script()
    {
        $html = $this->get('/orders')->assertOk()->getContent();

        // The stylesheet swaps the sidebar for the bottom bar below 769px.
        // Forcing either inline defeated that and left desktop customers with
        // no side navigation at all.
        $this->assertStringNotContainsString(
            "appSidebar.style.display=isCustomerOrGuest()?'none':''",
            $html
        );
        $this->assertStringNotContainsString(
            "mobileNav.style.display=isCustomerOrGuest()?'grid':'none'",
            $html
        );
        $this->assertStringContainsString("appSidebar.style.display=''", $html);

        // Both breakpoint rules must still be in place.
        $this->assertStringContainsString('@media(max-width:768px){.sidebar{display:none}', $html);
        $this->assertStringContainsString('.customer-mobile .customer-mobile-nav{display:none!important}', $html);
    }
    public function test_the_customer_counters_hide_when_there_is_nothing_to_count()
    {
        $html = $this->get('/orders')->assertOk()->getContent();

        // Both start hidden and are only shown once a count is above zero.
        $this->assertStringContainsString('id="pendingOrdersBadge" style="display:none"', $html);
        $this->assertStringContainsString('id="historyCountBadge" style="display:none"', $html);
        $this->assertStringContainsString('updateCustomerNavBadges', $html);
    }

    public function test_history_gets_its_own_counter()
    {
        $html = $this->get('/orders')->assertOk()->getContent();

        // History had no indicator at all before.
        $this->assertStringContainsString('historyCountBadge', $html);
        $this->assertStringContainsString('nav-badge muted', $html);
    }
    public function test_every_money_field_is_written_not_just_the_visible_ones()
    {
        $html = $this->get('/orders')->assertOk()->getContent();

        // chkSubtotal, chkDiscount and cartTotal were in the markup but never
        // written, so the checkout modal opened showing a zero subtotal.
        foreach (['chkSubtotal', 'chkDiscount', 'chkFee', 'chkTotal', 'cartSubtotal', 'cartTotal', 'cartFee'] as $field) {
            $this->assertStringContainsString("write('{$field}'", $html, $field);
        }

        // The writes no longer hang off whether the cart panel is on screen.
        $this->assertStringNotContainsString("if(cs&&cs.style.display!=='none')", $html);
    }

    public function test_the_customer_basket_shows_the_take_out_surcharge()
    {
        $html = $this->get('/orders')->assertOk()->getContent();

        // The cart panel had no surcharge line, so its total could not match
        // what the server charges for take out.
        $this->assertStringContainsString('id="cartFeeRow"', $html);
        $this->assertStringContainsString('id="cartFeeLabel"', $html);
    }

    public function test_the_basket_is_priced_the_way_the_server_prices_it()
    {
        $html = $this->get('/orders')->assertOk()->getContent();

        // Each line is rounded to centavos before being summed, matching
        // ReservationService::quote() rather than summing raw floats.
        $this->assertStringContainsString('subtotal += money2(line.price * line.qty)', $html);
        $this->assertStringContainsString('function money2(', $html);
    }
    public function test_both_login_pages_offer_a_way_home()
    {
        // A visitor who lands on a login screen should not be stuck there.
        $this->get('/orders')
            ->assertOk()
            ->assertSee('Back to home')
            ->assertSee('login-home', false);

        $this->get('/staff-login')
            ->assertOk()
            ->assertSee('Home')
            ->assertSee('Customer sign in');
    }

    public function test_the_home_links_point_at_the_landing_page()
    {
        $root = url('/');

        $this->assertStringContainsString(
            'href="'.$root.'"',
            $this->get('/orders')->assertOk()->getContent()
        );

        $this->assertStringContainsString(
            'href="'.$root.'"',
            $this->get('/staff-login')->assertOk()->getContent()
        );
    }
}
