<?php

namespace Tests\Feature;

use App\Models\Inventory;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PointOfSaleTest extends TestCase
{
    use RefreshDatabase;

    private function drink(array $overrides = []): Inventory
    {
        return Inventory::create(array_merge([
            'name' => 'Wintermelon Milktea',
            'category' => 'Milktea',
            'regular_price' => 79.00,
            'large_price' => 99.00,
            'stock' => 10,
        ], $overrides));
    }

    private function cashier(): User
    {
        $staff = User::factory()->create(['role' => 'cashier']);
        $this->withSession(['staff_user_id' => $staff->id]);

        return $staff;
    }

    public function test_a_walk_in_sale_records_several_items_on_one_receipt()
    {
        $this->cashier();
        $milktea = $this->drink();
        $fruit = $this->drink(['name' => 'Mulberry Lime', 'regular_price' => 85.00, 'large_price' => 105.00]);

        $response = $this->postJson('/staff/pos/sales', [
            'service_type' => 'dine_in',
            'payment_method' => 'cash',
            'items' => [
                ['inventory_id' => $milktea->id, 'size' => 'regular', 'quantity' => 2],
                ['inventory_id' => $fruit->id, 'size' => 'large', 'quantity' => 1],
            ],
        ]);

        // The old point_of_sales table could not do this: receipt_no was unique,
        // so one receipt could only ever hold a single item.
        $response->assertCreated()
            ->assertJsonPath('total', 263)
            ->assertJsonCount(2, 'items');
    }

    public function test_a_sale_is_completed_and_paid_the_moment_it_is_rung_up()
    {
        $cashier = $this->cashier();
        $drink = $this->drink();

        $reference = $this->postJson('/staff/pos/sales', [
            'service_type' => 'dine_in',
            'payment_method' => 'gcash',
            'items' => [['inventory_id' => $drink->id, 'quantity' => 1]],
        ])->assertCreated()->json('reference');

        $sale = Reservation::where('reference', $reference)->firstOrFail();

        $this->assertSame('completed', $sale->status);
        $this->assertSame('paid', $sale->payment_status);
        $this->assertSame('gcash', $sale->payment_method);
        $this->assertSame('pos', $sale->source);
        $this->assertSame($cashier->id, $sale->paid_by);
        $this->assertNotNull($sale->completed_at);
    }

    public function test_selling_takes_the_drinks_out_of_stock()
    {
        $this->cashier();
        $drink = $this->drink(['stock' => 10]);

        $this->postJson('/staff/pos/sales', [
            'service_type' => 'dine_in',
            'payment_method' => 'cash',
            'items' => [['inventory_id' => $drink->id, 'quantity' => 3]],
        ])->assertCreated();

        $this->assertSame(7, $drink->fresh()->stock);
    }

    public function test_a_sale_cannot_oversell_the_shelf()
    {
        $this->cashier();
        $drink = $this->drink(['stock' => 2]);

        $this->postJson('/staff/pos/sales', [
            'service_type' => 'dine_in',
            'payment_method' => 'cash',
            'items' => [['inventory_id' => $drink->id, 'quantity' => 5]],
        ])->assertStatus(422);

        // The whole sale is rolled back, so nothing is half recorded.
        $this->assertSame(2, $drink->fresh()->stock);
        $this->assertSame(0, Reservation::count());
    }

    public function test_take_out_is_charged_per_cup_at_the_counter_too()
    {
        $this->cashier();
        $drink = $this->drink();

        $this->postJson('/staff/pos/sales', [
            'service_type' => 'take_out',
            'payment_method' => 'cash',
            'items' => [['inventory_id' => $drink->id, 'quantity' => 4]],
        ])
            ->assertCreated()
            ->assertJsonPath('subtotal', 316)
            ->assertJsonPath('takeout_fee', 20)
            ->assertJsonPath('total', 336);
    }

    public function test_the_till_cannot_invent_its_own_prices()
    {
        $this->cashier();
        $drink = $this->drink();

        $this->postJson('/staff/pos/sales', [
            'service_type' => 'dine_in',
            'payment_method' => 'cash',
            'items' => [[
                'inventory_id' => $drink->id,
                'quantity' => 1,
                'unit_price' => 1,
                'line_total' => 1,
            ]],
        ])
            ->assertCreated()
            ->assertJsonPath('total', 79);
    }

    public function test_only_the_three_accepted_payment_methods_ring_up()
    {
        $this->cashier();
        $drink = $this->drink();

        $this->postJson('/staff/pos/sales', [
            'service_type' => 'dine_in',
            'payment_method' => 'cheque',
            'items' => [['inventory_id' => $drink->id, 'quantity' => 1]],
        ])->assertStatus(422);
    }

    public function test_the_till_is_staff_only()
    {
        $drink = $this->drink();

        $this->postJson('/staff/pos/sales', [
            'service_type' => 'dine_in',
            'payment_method' => 'cash',
            'items' => [['inventory_id' => $drink->id, 'quantity' => 1]],
        ])->assertUnauthorized();
    }

    public function test_walk_in_sales_do_not_clutter_the_reservation_queue()
    {
        $this->cashier();
        $drink = $this->drink();

        $this->postJson('/staff/pos/sales', [
            'service_type' => 'dine_in',
            'payment_method' => 'cash',
            'items' => [['inventory_id' => $drink->id, 'quantity' => 1]],
        ])->assertCreated();

        // A completed sale is not work in progress, so the counter's active
        // queue stays clear of it.
        $this->getJson('/staff/reservations?active=1')
            ->assertOk()
            ->assertJsonCount(0, 'data');
    }

    public function test_the_pos_page_is_a_working_till_not_a_price_list()
    {
        $this->cashier();
        $this->drink();

        $this->get('/pos')
            ->assertOk()
            ->assertSee('Complete sale')
            ->assertSee('Cash received')
            ->assertSee('Take out')
            ->assertSee('GCash')
            ->assertSee('PayMaya');
    }
}
