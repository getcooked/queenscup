<?php

namespace Tests\Feature;

use App\Models\Inventory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerReservationPageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Inventory::create([
            'name' => 'Wintermelon Milktea',
            'category' => 'Milktea',
            'regular_price' => 79.00,
            'large_price' => 99.00,
            'stock' => 20,
        ]);
    }

    public function test_the_customer_side_reserves_rather_than_orders()
    {
        $this->get('/orders')
            ->assertOk()
            ->assertSee('Menu &amp; Reserve', false)
            ->assertSee('My Reservations')
            ->assertSee('Confirm reservation');
    }

    public function test_the_customer_checkout_posts_to_the_reservation_api()
    {
        // The basket is sent to the API rather than written to local storage,
        // so the counter and the customer see the same reservation.
        // @json escapes the slashes, so the URL lands in the page as
        // "http:\/\/host\/api\/v1\/reservations".
        $this->get('/orders')
            ->assertOk()
            ->assertSee('submitReservation', false)
            ->assertSee('api\/v1\/reservations', false);
    }

    public function test_the_page_knows_the_take_out_surcharge()
    {
        $fee = (float) config('queenscup.takeout_fee_per_cup', 5.00);

        $this->get('/orders')
            ->assertOk()
            ->assertSee('TAKEOUT_FEE_PER_CUP = '.$fee, false);
    }

    public function test_the_customer_cart_sends_real_inventory_ids()
    {
        $drink = Inventory::first();

        // The reservation API resolves prices from these ids, so the menu the
        // customer sees has to carry the real catalogue id.
        $this->get('/orders')
            ->assertOk()
            ->assertSee('"id":'.$drink->id, false);
    }

    public function test_a_reservation_made_from_the_page_payload_is_accepted()
    {
        $drink = Inventory::first();

        // Exactly the shape submitReservation() sends.
        $this->postJson('/api/v1/reservations', [
            'service_type' => 'take_out',
            'customer_name' => 'Ana Reyes',
            'customer_email' => 'ana@example.com',
            'customer_contact' => '09171234567',
            'branch' => 'kotapark',
            'source' => 'web',
            'items' => [
                ['inventory_id' => $drink->id, 'size' => 'large', 'quantity' => 2],
            ],
        ])
            ->assertCreated()
            ->assertJsonPath('service_type', 'take_out')
            ->assertJsonPath('subtotal', 198)
            ->assertJsonPath('takeout_fee', 10)
            ->assertJsonPath('total', 208)
            ->assertJsonPath('status', 'pending')
            ->assertJsonPath('payment_status', 'unpaid');
    }

    public function test_a_customer_can_track_a_reservation_by_its_reference()
    {
        $drink = Inventory::first();

        $reference = $this->postJson('/api/v1/reservations', [
            'service_type' => 'dine_in',
            'customer_name' => 'Ana Reyes',
            'items' => [['inventory_id' => $drink->id, 'quantity' => 1]],
        ])->assertCreated()->json('reference');

        // This is the lookup the My Reservations list performs on load.
        $this->getJson('/api/v1/reservations/'.$reference)
            ->assertOk()
            ->assertJsonPath('reference', $reference)
            ->assertJsonPath('status', 'pending');
    }
}
