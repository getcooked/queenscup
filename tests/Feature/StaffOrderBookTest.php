<?php

namespace Tests\Feature;

use App\Models\Inventory;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StaffOrderBookTest extends TestCase
{
    use RefreshDatabase;

    private Inventory $drink;

    protected function setUp(): void
    {
        parent::setUp();

        $this->drink = Inventory::create([
            'name' => 'Wintermelon Milktea',
            'category' => 'Milktea',
            'regular_price' => 100,
            'large_price' => 120,
            'stock' => 200,
        ]);

        $staff = User::factory()->create(['role' => 'admin']);
        $this->withSession(['staff_user_id' => $staff->id]);
    }

    public function test_the_order_book_lists_both_till_sales_and_reservations()
    {
        $this->postJson('/staff/pos/sales', [
            'service_type' => 'dine_in',
            'payment_method' => 'cash',
            'items' => [['inventory_id' => $this->drink->id, 'quantity' => 1]],
        ])->assertCreated();

        $this->postJson('/api/v1/reservations', [
            'service_type' => 'take_out',
            'customer_name' => 'Ana Reyes',
            'items' => [['inventory_id' => $this->drink->id, 'quantity' => 2]],
        ])->assertCreated();

        $sources = collect($this->getJson('/staff/reservations')->assertOk()->json('data'))
            ->pluck('source')
            ->sort()
            ->values()
            ->all();

        // Unlike the reservation queue, the book holds everything.
        $this->assertSame(['pos', 'web'], $sources);
    }

    public function test_an_unpaid_reservation_still_appears_in_the_book()
    {
        $this->postJson('/api/v1/reservations', [
            'service_type' => 'dine_in',
            'customer_name' => 'Ana',
            'items' => [['inventory_id' => $this->drink->id, 'quantity' => 1]],
        ])->assertCreated();

        $this->getJson('/staff/reservations')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.payment_status', 'unpaid');
    }

    public function test_the_orders_page_reads_the_book_from_the_server()
    {
        $this->get('/orders')
            ->assertOk()
            ->assertSee('STAFF_ORDER_URL', false)
            ->assertSee('loadStaffOrders', false)
            ->assertSee('patchServerOrder', false)
            // A channel column so till sales are distinguishable.
            ->assertSee('Channel', false);
    }

    public function test_the_book_advances_an_order_through_the_server()
    {
        $reference = $this->postJson('/api/v1/reservations', [
            'service_type' => 'dine_in',
            'customer_name' => 'Ana',
            'items' => [['inventory_id' => $this->drink->id, 'quantity' => 1]],
        ])->assertCreated()->json('reference');

        $id = Reservation::where('reference', $reference)->value('id');

        // The buttons in the book call exactly these.
        $this->patchJson("/staff/reservations/{$id}/status", ['status' => 'preparing'])->assertOk();
        $this->patchJson("/staff/reservations/{$id}/status", ['status' => 'ready'])->assertOk();
        $this->patchJson("/staff/reservations/{$id}/payment", ['payment_method' => 'cash'])->assertOk();

        $order = Reservation::find($id);
        $this->assertSame('ready', $order->status);
        $this->assertSame('paid', $order->payment_status);
    }
}
