<?php

namespace Tests\Feature;

use App\Models\Inventory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PosSalesLogTest extends TestCase
{
    use RefreshDatabase;

    private function ringUp(int $quantity = 1, string $method = 'cash'): array
    {
        $drink = Inventory::firstOrCreate(
            ['name' => 'Wintermelon Milktea'],
            ['category' => 'Milktea', 'regular_price' => 79, 'large_price' => 99, 'stock' => 200]
        );

        return $this->postJson('/staff/pos/sales', [
            'service_type' => 'dine_in',
            'payment_method' => $method,
            'items' => [['inventory_id' => $drink->id, 'quantity' => $quantity]],
        ])->assertCreated()->json();
    }

    public function test_the_log_lists_till_sales_with_their_cashier()
    {
        $cashier = User::factory()->create(['name' => 'Jaylian Bacolod', 'role' => 'cashier']);
        $this->withSession(['staff_user_id' => $cashier->id]);

        $sale = $this->ringUp(2);

        $this->getJson('/staff/pos/sales')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.reference', $sale['reference'])
            ->assertJsonPath('data.0.cashier', 'Jaylian Bacolod')
            ->assertJsonPath('totals.count', 1)
            ->assertJsonPath('totals.revenue', 158)
            ->assertJsonPath('totals.cups', 2);
    }

    public function test_app_reservations_stay_out_of_the_till_log()
    {
        $staff = User::factory()->create(['role' => 'admin']);
        $drink = Inventory::create(['name' => 'Mulberry Lime', 'category' => 'Fruit', 'regular_price' => 85, 'large_price' => 105, 'stock' => 50]);

        // A customer reservation is not a till sale and must not inflate it.
        $this->postJson('/api/v1/reservations', [
            'service_type' => 'dine_in',
            'customer_name' => 'Ana',
            'items' => [['inventory_id' => $drink->id, 'quantity' => 1]],
        ])->assertCreated();

        $this->withSession(['staff_user_id' => $staff->id]);

        $this->getJson('/staff/pos/sales')
            ->assertOk()
            ->assertJsonCount(0, 'data')
            ->assertJsonPath('totals.revenue', 0);
    }

    public function test_the_log_can_be_bound_to_a_period()
    {
        $staff = User::factory()->create(['role' => 'admin']);
        $this->withSession(['staff_user_id' => $staff->id]);

        $this->ringUp();

        $this->getJson('/staff/pos/sales?from='.now()->addDay()->toDateString())
            ->assertOk()
            ->assertJsonCount(0, 'data');

        $this->getJson('/staff/pos/sales?from='.now()->toDateString().'&to='.now()->toDateString())
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function test_the_log_is_staff_only()
    {
        $this->getJson('/staff/pos/sales')->assertUnauthorized();
    }

    public function test_the_reports_page_shows_the_till_log()
    {
        $staff = User::factory()->create(['role' => 'admin']);
        $this->withSession(['staff_user_id' => $staff->id]);

        $this->get('/reports')
            ->assertOk()
            ->assertSee('Point of Sale Log')
            ->assertSee('Till Revenue')
            ->assertSee('loadPosLog', false);
    }
}
