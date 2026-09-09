<?php

namespace Tests\Feature;

use App\Models\Inventory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardRecentSalesTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_dashboard_shows_a_recent_sales_panel()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->withSession(['staff_user_id' => $admin->id]);

        $this->get('/dashboard')
            ->assertOk()
            ->assertSee('Recent Sales')
            ->assertSee('recentSalesTable', false)
            ->assertSee('renderRecentSales', false)
            // It reads the till log rather than local storage.
            ->assertSee('staff\/pos\/sales', false);
    }

    public function test_recent_sales_reads_the_till_log_endpoint()
    {
        $cashier = User::factory()->create(['role' => 'cashier']);
        $this->withSession(['staff_user_id' => $cashier->id]);

        $drink = Inventory::create([
            'name' => 'Wintermelon Milktea',
            'category' => 'Milktea',
            'regular_price' => 79,
            'large_price' => 99,
            'stock' => 40,
        ]);

        $sale = $this->postJson('/staff/pos/sales', [
            'service_type' => 'take_out',
            'payment_method' => 'gcash',
            'items' => [['inventory_id' => $drink->id, 'quantity' => 2]],
        ])->assertCreated()->json();

        // Exactly what the dashboard panel fetches on load.
        $this->getJson('/staff/pos/sales')
            ->assertOk()
            ->assertJsonPath('data.0.reference', $sale['reference'])
            ->assertJsonPath('data.0.payment_method', 'gcash')
            ->assertJsonPath('data.0.service_type', 'take_out')
            // 2 x 79 plus the 5 peso per cup take-out surcharge.
            ->assertJsonPath('totals.revenue', 168);
    }

    public function test_the_dashboard_still_fits_one_screen()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->withSession(['staff_user_id' => $admin->id]);

        // The page is a fixed three band grid that must not scroll.
        $this->get('/dashboard')
            ->assertOk()
            ->assertSee('grid-template-rows: auto minmax(0, 1.06fr) minmax(0, 1fr)', false)
            ->assertSee('.dash-bottom', false)
            ->assertSee('overflow: hidden', false);
    }
}
