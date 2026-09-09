<?php

namespace Tests\Feature;

use App\Models\Inventory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LandingPageTest extends TestCase
{
    use RefreshDatabase;

    private function drink(array $overrides = []): Inventory
    {
        return Inventory::create(array_merge([
            'name' => 'Wintermelon Milktea',
            'category' => 'Milktea',
            'regular_price' => 79.00,
            'large_price' => 99.00,
            'stock' => 20,
        ], $overrides));
    }

    public function test_the_landing_page_shows_the_menu_to_a_visitor_who_is_not_signed_in()
    {
        $this->drink();
        $this->drink(['name' => 'Mulberry Lime', 'category' => 'Fruit Tea', 'regular_price' => 85.00]);

        $this->get('/')
            ->assertOk()
            ->assertSee('Wintermelon Milktea')
            ->assertSee('Mulberry Lime')
            ->assertSee('Fruit Tea')
            // Both cup sizes are priced on the card.
            ->assertSee('₱79')
            ->assertSee('₱99');
    }

    public function test_the_landing_page_offers_the_app_download_and_a_way_to_reserve()
    {
        $this->drink();

        $response = $this->get('/')->assertOk();

        $response->assertSee('Download for Android', false);
        $response->assertSee(url('/orders'), false);
    }

    public function test_the_landing_page_publishes_the_cup_fee_rather_than_hardcoding_it()
    {
        $this->drink();

        config(['queenscup.takeout_fee_per_cup' => 7.00]);

        $this->get('/')
            ->assertOk()
            ->assertSee('Take out adds ₱7 per cup');
    }

    public function test_the_landing_page_survives_an_empty_catalogue()
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('menu is being updated');
    }

    public function test_the_reservation_app_still_lives_on_orders()
    {
        // Moving the landing page onto / must not take the customer app with it.
        $this->get('/orders')->assertOk();
    }

    public function test_the_manage_reservations_screen_is_staff_only()
    {
        $this->get('/reservations')->assertRedirect('/staff-login');
    }

    public function test_staff_can_open_the_manage_reservations_screen()
    {
        $staff = User::factory()->create(['role' => 'cashier']);

        $this->withSession(['staff_user_id' => $staff->id])
            ->get('/reservations')
            ->assertOk()
            ->assertSee('Reservations')
            ->assertSee('Record payment')
            // It must reach the counter endpoints, not the customer API.
            ->assertSee('staff/reservations', false);
    }

    public function test_every_admin_page_links_to_manage_reservations()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->withSession(['staff_user_id' => $admin->id]);

        foreach (['/dashboard', '/inventory', '/reports', '/settings', '/pos', '/profile'] as $uri) {
            $this->get($uri)
                ->assertOk()
                ->assertSee(url('/reservations'), false);
        }
    }
}
