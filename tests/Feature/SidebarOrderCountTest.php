<?php

namespace Tests\Feature;

use App\Models\Inventory;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The sidebar order badge.
 *
 * It used to count `qc_orders` out of the browser's local storage, which the
 * counter no longer writes to, so it sat at a permanent zero while the Orders
 * screen — which fetches — showed the real number. It now reads the same two
 * statuses from the server that the Orders screen counts.
 */
class SidebarOrderCountTest extends TestCase
{
    use RefreshDatabase;

    private Inventory $drink;

    protected function setUp(): void
    {
        parent::setUp();

        $this->drink = Inventory::create([
            'name' => 'Wintermelon Milktea',
            'category' => 'Milktea',
            'regular_price' => 79,
            'large_price' => 99,
            'stock' => 200,
        ]);
    }

    private function asStaff(): User
    {
        $staff = User::factory()->create(['role' => 'admin']);
        $this->withSession(['staff_user_id' => $staff->id]);

        return $staff;
    }

    private function reserve(string $status, string $branch = 'kotapark'): Reservation
    {
        $reference = $this->postJson('/api/v1/reservations', [
            'service_type' => 'dine_in',
            'customer_name' => 'Ana Reyes',
            'branch' => $branch,
            'items' => [['inventory_id' => $this->drink->id, 'quantity' => 1]],
        ])->assertCreated()->json('reference');

        $reservation = Reservation::where('reference', $reference)->firstOrFail();
        $reservation->forceFill(['status' => $status])->save();

        return $reservation;
    }

    public function test_it_counts_the_orders_still_in_front_of_the_counter()
    {
        $this->asStaff();

        $this->reserve('pending');
        $this->reserve('preparing');
        $this->reserve('completed');
        $this->reserve('cancelled');

        $this->getJson('/staff/reservations/counts')
            ->assertOk()
            ->assertJsonPath('active', 2);
    }

    public function test_it_counts_orders_still_awaiting_payment()
    {
        $this->asStaff();

        $this->reserve('pending');
        $this->reserve('preparing');
        // Finished business is not money still owed at the counter.
        $this->reserve('completed');
        $this->reserve('cancelled');

        $this->getJson('/staff/reservations/counts')
            ->assertOk()
            ->assertJsonPath('cash_pending', 2);
    }

    public function test_a_paid_order_is_not_awaiting_payment()
    {
        $this->asStaff();
        $reservation = $this->reserve('preparing');

        $this->patchJson("/staff/reservations/{$reservation->id}/payment", ['payment_method' => 'gcash'])
            ->assertOk();

        $this->getJson('/staff/reservations/counts')
            ->assertOk()
            ->assertJsonPath('active', 1)
            ->assertJsonPath('cash_pending', 0);
    }

    public function test_it_can_be_narrowed_to_one_branch()
    {
        $this->asStaff();

        $this->reserve('pending', 'kotapark');
        $this->reserve('pending', 'mcc');

        $this->getJson('/staff/reservations/counts?branch=kotapark')
            ->assertOk()
            ->assertJsonPath('active', 1);

        $this->getJson('/staff/reservations/counts')
            ->assertOk()
            ->assertJsonPath('active', 2);
    }

    public function test_the_counts_are_staff_only()
    {
        $this->getJson('/staff/reservations/counts')->assertUnauthorized();
    }

    public function test_the_sidebar_script_asks_the_server_rather_than_local_storage()
    {
        $script = file_get_contents(public_path('js/admin-sidebar.js'));

        $this->assertStringContainsString('/staff/reservations/counts', $script);

        // Counting the browser copy is what produced the permanent zero.
        $this->assertStringNotContainsString("localStorage.getItem('qc_orders')", $script);
    }
}
