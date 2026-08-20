<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Inventory;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityLogTest extends TestCase
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
            'stock' => 100,
        ]);
    }

    private function staff(string $role = 'admin'): User
    {
        $staff = User::factory()->create(['role' => $role, 'name' => 'Ana Reyes']);
        $this->withSession(['staff_user_id' => $staff->id]);

        return $staff;
    }

    private function reserve(): Reservation
    {
        $reference = $this->postJson('/api/v1/reservations', [
            'service_type' => 'dine_in',
            'customer_name' => 'Walk-in',
            'items' => [['inventory_id' => $this->drink->id, 'quantity' => 1]],
        ])->assertCreated()->json('reference');

        return Reservation::where('reference', $reference)->firstOrFail();
    }

    public function test_moving_an_order_along_is_recorded_with_both_ends_of_the_move()
    {
        $staff = $this->staff();
        $reservation = $this->reserve();

        $this->patchJson("/staff/reservations/{$reservation->id}/status", ['status' => 'preparing'])->assertOk();

        $log = ActivityLog::where('action', 'order.status')->firstOrFail();

        $this->assertSame($staff->id, $log->user_id);
        $this->assertSame('Ana Reyes', $log->actor_name);
        $this->assertSame($reservation->reference, $log->subject_id);
        $this->assertSame('pending', $log->properties['from']);
        $this->assertSame('preparing', $log->properties['to']);
    }

    public function test_taking_payment_is_recorded_with_the_method_and_amount()
    {
        $this->staff();
        $reservation = $this->reserve();

        $this->patchJson("/staff/reservations/{$reservation->id}/payment", ['payment_method' => 'gcash'])->assertOk();

        $log = ActivityLog::where('action', 'order.payment')->firstOrFail();

        $this->assertSame('gcash', $log->properties['method']);
        $this->assertEquals(79, $log->properties['total']);
        $this->assertStringContainsString('GCASH', $log->description);
    }

    public function test_a_till_sale_is_recorded()
    {
        $this->staff('cashier');

        $this->postJson('/staff/pos/sales', [
            'service_type' => 'take_out',
            'payment_method' => 'cash',
            'items' => [['inventory_id' => $this->drink->id, 'quantity' => 2]],
        ])->assertCreated();

        $log = ActivityLog::where('action', 'sale.recorded')->firstOrFail();

        $this->assertSame('cash', $log->properties['method']);
        $this->assertSame(2, $log->properties['cups']);
        $this->assertSame('cashier', $log->actor_role);
    }

    public function test_inventory_changes_are_recorded()
    {
        $this->staff();

        $this->post('/inventory', [
            'name' => 'Sakura Pomelo',
            'category' => 'Fruit Tea',
            'regular_price' => 92,
            'large_price' => 112,
            'stock' => 30,
        ])->assertRedirect();

        $this->assertDatabaseHas('activity_logs', ['action' => 'inventory.created']);

        $item = Inventory::where('name', 'Sakura Pomelo')->firstOrFail();

        $this->delete("/inventory/{$item->id}")->assertRedirect();

        $removed = ActivityLog::where('action', 'inventory.deleted')->firstOrFail();

        // The name is captured before the row goes, so the line still reads.
        $this->assertStringContainsString('Sakura Pomelo', $removed->description);
    }

    public function test_signing_in_and_out_is_recorded()
    {
        $staff = User::factory()->create([
            'role' => 'admin',
            'email' => 'ana@queenscup.test',
            'password' => bcrypt('secret1234'),
        ]);

        $this->postJson('/staff-login', ['email' => $staff->email, 'password' => 'secret1234'])->assertOk();
        $this->assertDatabaseHas('activity_logs', ['action' => 'staff.login', 'user_id' => $staff->id]);

        $this->post('/staff-logout');
        $this->assertDatabaseHas('activity_logs', ['action' => 'staff.logout', 'user_id' => $staff->id]);
    }

    public function test_the_log_survives_the_person_who_made_it_being_removed()
    {
        $staff = $this->staff();
        $reservation = $this->reserve();

        $this->patchJson("/staff/reservations/{$reservation->id}/status", ['status' => 'preparing'])->assertOk();

        $staff->delete();

        $log = ActivityLog::where('action', 'order.status')->firstOrFail();

        $this->assertNull($log->user_id);
        // The copied name is why the line still says who did it.
        $this->assertSame('Ana Reyes', $log->actor_name);
    }

    public function test_a_logging_failure_never_takes_the_action_down_with_it()
    {
        // A broken log must not cost a sale. Whether the driver rejects the
        // over-long action or quietly accepts it, the call must come back
        // rather than blow up the work that triggered it.
        $result = ActivityLog::record(str_repeat('x', 200), 'should not throw');

        $this->assertTrue($result === null || $result instanceof ActivityLog);
    }

    public function test_the_page_lists_activity_and_can_be_filtered()
    {
        $this->staff();
        $reservation = $this->reserve();

        $this->patchJson("/staff/reservations/{$reservation->id}/status", ['status' => 'preparing'])->assertOk();
        $this->patchJson("/staff/reservations/{$reservation->id}/payment", ['payment_method' => 'cash'])->assertOk();

        $this->get('/activity')
            ->assertOk()
            ->assertSee('Activity')
            ->assertSee('Ana Reyes')
            ->assertSee($reservation->reference);

        // Narrowed to payments only.
        $this->get('/activity?action=order.payment')
            ->assertOk()
            ->assertSee('Payment recorded')
            ->assertDontSee('from pending to preparing');
    }

    public function test_the_page_is_admin_only()
    {
        $this->staff('cashier');
        // A cashier is redirected to their own screen rather than shown a page
        // naming what everyone else did.
        $this->get('/activity')->assertRedirect(route('point-of-sales.index'));
        $this->getJson('/activity')->assertForbidden();

        $this->flushSession();
        $this->get('/activity')->assertRedirect();
    }
}
