<?php

namespace Tests\Feature;

use App\Models\Inventory;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CombinedSalesReportTest extends TestCase
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
            'stock' => 500,
        ]);
    }

    /** A walk-in sale rung up at the till. */
    private function tillSale(int $quantity = 1): void
    {
        $this->postJson('/staff/pos/sales', [
            'service_type' => 'dine_in',
            'payment_method' => 'cash',
            'items' => [['inventory_id' => $this->drink->id, 'quantity' => $quantity]],
        ])->assertCreated();
    }

    /** An app reservation, later paid for at the counter. */
    private function paidReservation(int $quantity = 1): string
    {
        $reference = $this->postJson('/api/v1/reservations', [
            'service_type' => 'dine_in',
            'customer_name' => 'Ana Reyes',
            'items' => [['inventory_id' => $this->drink->id, 'quantity' => $quantity]],
        ])->assertCreated()->json('reference');

        $id = Reservation::where('reference', $reference)->value('id');
        $this->patchJson("/staff/reservations/{$id}/payment", ['payment_method' => 'gcash'])->assertOk();

        return $reference;
    }

    private function asStaff(): User
    {
        $staff = User::factory()->create(['role' => 'admin']);
        $this->withSession(['staff_user_id' => $staff->id]);

        return $staff;
    }

    public function test_the_log_combines_both_channels_by_default()
    {
        $this->asStaff();
        $this->tillSale(2);
        $this->paidReservation(1);

        $this->getJson('/staff/pos/sales')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('totals.count', 2)
            ->assertJsonPath('totals.revenue', 300)
            ->assertJsonPath('totals.pos', 200)
            ->assertJsonPath('totals.reservation', 100);
    }

    public function test_the_log_can_be_filtered_to_the_till()
    {
        $this->asStaff();
        $this->tillSale(2);
        $this->paidReservation(1);

        $this->getJson('/staff/pos/sales?source=pos')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.channel', 'Point of Sale')
            ->assertJsonPath('totals.revenue', 200);
    }

    public function test_the_log_can_be_filtered_to_reservations()
    {
        $this->asStaff();
        $this->tillSale(2);
        $this->paidReservation(1);

        $this->getJson('/staff/pos/sales?source=reservation')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.channel', 'Reservation')
            ->assertJsonPath('totals.revenue', 100);
    }

    public function test_unpaid_reservations_are_not_counted_as_sales()
    {
        $this->asStaff();

        // Reserved but never paid for: not revenue.
        $this->postJson('/api/v1/reservations', [
            'service_type' => 'dine_in',
            'customer_name' => 'Ana',
            'items' => [['inventory_id' => $this->drink->id, 'quantity' => 1]],
        ])->assertCreated();

        $this->getJson('/staff/pos/sales')
            ->assertOk()
            ->assertJsonCount(0, 'data')
            ->assertJsonPath('totals.revenue', 0);
    }

    public function test_an_unknown_source_filter_is_rejected()
    {
        $this->asStaff();

        $this->getJson('/staff/pos/sales?source=carrier-pigeon')->assertStatus(422);
    }

    public function test_the_dashboard_feed_returns_both_channels_with_categories()
    {
        $this->asStaff();
        $this->tillSale(2);
        $this->paidReservation(1);

        $response = $this->getJson('/staff/dashboard/sales')->assertOk();

        $this->assertCount(2, $response->json('data'));
        $this->assertSame('Milktea', $response->json('data.0.items.0.category'));

        // The charts add up qty x price, so both must be present.
        $this->assertNotNull($response->json('data.0.items.0.qty'));
        $this->assertNotNull($response->json('data.0.items.0.price'));
        $this->assertNotNull($response->json('data.0.at'));
    }

    public function test_the_dashboard_feed_excludes_cancelled_and_unpaid()
    {
        $this->asStaff();

        $this->postJson('/api/v1/reservations', [
            'service_type' => 'dine_in',
            'customer_name' => 'Ana',
            'items' => [['inventory_id' => $this->drink->id, 'quantity' => 1]],
        ])->assertCreated();

        $this->getJson('/staff/dashboard/sales')->assertOk()->assertJsonCount(0, 'data');
    }

    public function test_the_reports_page_offers_the_channel_filter()
    {
        $this->asStaff();

        $this->get('/reports')
            ->assertOk()
            ->assertSee('Sales Log')
            ->assertSee('posLogSource', false)
            ->assertSee('Point of Sale')
            ->assertSee('Reservations')
            ->assertSee('From Counter')
            ->assertSee('From Reservations');
    }
}
