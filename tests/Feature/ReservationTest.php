<?php

namespace Tests\Feature;

use App\Models\Inventory;
use App\Models\Reservation;
use App\Models\User;
use App\Services\ReservationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ReservationTest extends TestCase
{
    use RefreshDatabase;

    private function drink(array $overrides = []): Inventory
    {
        return Inventory::create(array_merge([
            'name' => 'Wintermelon Milktea',
            'category' => 'Milktea',
            'regular_price' => 79.00,
            'large_price' => 99.00,
            'stock' => 50,
        ], $overrides));
    }

    public function test_take_out_adds_five_pesos_for_every_cup()
    {
        $drink = $this->drink();

        $response = $this->postJson('/api/v1/reservations/quote', [
            'service_type' => 'take_out',
            'items' => [
                ['inventory_id' => $drink->id, 'size' => 'regular', 'quantity' => 3],
            ],
        ]);

        // 3 x 79.00 = 237.00 plus 3 cups x 5.00 = 15.00.
        $response->assertOk()
            ->assertJsonPath('cup_count', 3)
            ->assertJsonPath('subtotal', 237)
            ->assertJsonPath('takeout_fee', 15)
            ->assertJsonPath('total', 252);
    }

    public function test_the_surcharge_counts_cups_not_line_items()
    {
        $regular = $this->drink();
        $large = $this->drink(['name' => 'Brown Sugar', 'regular_price' => 85.00, 'large_price' => 105.00]);

        $response = $this->postJson('/api/v1/reservations/quote', [
            'service_type' => 'take_out',
            'items' => [
                ['inventory_id' => $regular->id, 'size' => 'regular', 'quantity' => 2],
                ['inventory_id' => $large->id, 'size' => 'large', 'quantity' => 4],
            ],
        ]);

        // Two lines, but six cups, so the fee is 6 x 5.00 = 30.00.
        $response->assertOk()
            ->assertJsonPath('cup_count', 6)
            ->assertJsonPath('takeout_fee', 30)
            ->assertJsonPath('total', 158 + 420 + 30);
    }

    public function test_dine_in_is_never_charged_the_cup_fee()
    {
        $drink = $this->drink();

        $this->postJson('/api/v1/reservations/quote', [
            'service_type' => 'dine_in',
            'items' => [['inventory_id' => $drink->id, 'size' => 'large', 'quantity' => 4]],
        ])
            ->assertOk()
            ->assertJsonPath('takeout_fee', 0)
            ->assertJsonPath('total', 396);
    }

    public function test_prices_come_from_the_catalogue_not_the_request()
    {
        $drink = $this->drink();

        $this->postJson('/api/v1/reservations', [
            'service_type' => 'dine_in',
            'customer_name' => 'Bargain Hunter',
            'items' => [[
                'inventory_id' => $drink->id,
                'size' => 'regular',
                'quantity' => 1,
                // Both of these are ignored; the server prices every line itself.
                'unit_price' => 1,
                'line_total' => 1,
            ]],
        ])
            ->assertCreated()
            ->assertJsonPath('total', 79)
            ->assertJsonPath('items.0.unit_price', 79);
    }

    public function test_a_reservation_can_be_tracked_by_its_reference()
    {
        $drink = $this->drink();

        $reference = $this->postJson('/api/v1/reservations', [
            'service_type' => 'take_out',
            'customer_name' => 'Jay',
            'items' => [['inventory_id' => $drink->id, 'quantity' => 1]],
        ])->assertCreated()->json('reference');

        $this->assertMatchesRegularExpression('/^QC-[2-9A-HJ-NP-Z]{6}$/', $reference);

        $this->getJson("/api/v1/reservations/{$reference}")
            ->assertOk()
            ->assertJsonPath('status', 'pending')
            ->assertJsonPath('status_label', 'Reservation received')
            ->assertJsonPath('customer_name', 'Jay');
    }

    public function test_tracking_an_unknown_reference_reports_not_found()
    {
        $this->getJson('/api/v1/reservations/QC-ZZZZZZ')->assertNotFound();
    }

    public function test_staff_move_a_reservation_through_the_counter_workflow()
    {
        $staff = User::factory()->create(['role' => 'cashier']);
        $drink = $this->drink();

        $reservation = app(ReservationService::class)->create([
            'customer_name' => 'Ana',
            'service_type' => 'dine_in',
            'items' => [['inventory_id' => $drink->id, 'quantity' => 1]],
        ]);

        $this->withSession(['staff_user_id' => $staff->id]);

        foreach (['preparing', 'ready', 'completed'] as $status) {
            $this->patchJson("/staff/reservations/{$reservation->id}/status", ['status' => $status])
                ->assertOk()
                ->assertJsonPath('status', $status);
        }

        $reservation->refresh();
        $this->assertNotNull($reservation->ready_at, 'ready_at should be stamped when the drink is ready');
        $this->assertNotNull($reservation->completed_at);
    }

    public function test_a_completed_reservation_cannot_be_reopened()
    {
        $drink = $this->drink();
        $service = app(ReservationService::class);

        $reservation = $service->create([
            'customer_name' => 'Ana',
            'service_type' => 'dine_in',
            'items' => [['inventory_id' => $drink->id, 'quantity' => 1]],
        ]);

        $service->transition($reservation, 'preparing');
        $service->transition($reservation, 'ready');
        $service->transition($reservation, 'completed');

        $this->expectException(ValidationException::class);
        $service->transition($reservation, 'preparing');
    }

    public function test_status_cannot_skip_straight_from_pending_to_ready()
    {
        $drink = $this->drink();
        $service = app(ReservationService::class);

        $reservation = $service->create([
            'customer_name' => 'Ana',
            'service_type' => 'dine_in',
            'items' => [['inventory_id' => $drink->id, 'quantity' => 1]],
        ]);

        $this->expectException(ValidationException::class);
        $service->transition($reservation, 'ready');
    }

    public function test_staff_record_which_payment_method_the_customer_used()
    {
        $staff = User::factory()->create(['role' => 'cashier']);
        $drink = $this->drink();

        $reservation = app(ReservationService::class)->create([
            'customer_name' => 'Ana',
            'service_type' => 'take_out',
            'items' => [['inventory_id' => $drink->id, 'quantity' => 2]],
        ]);

        $this->assertSame('unpaid', $reservation->payment_status);

        $this->withSession(['staff_user_id' => $staff->id])
            ->patchJson("/staff/reservations/{$reservation->id}/payment", ['payment_method' => 'gcash'])
            ->assertOk()
            ->assertJsonPath('payment_status', 'paid')
            ->assertJsonPath('payment_method', 'gcash');

        $reservation->refresh();
        $this->assertSame($staff->id, $reservation->paid_by);
        $this->assertNotNull($reservation->paid_at);
    }

    public function test_only_cash_gcash_and_paymaya_are_accepted()
    {
        $staff = User::factory()->create(['role' => 'admin']);
        $drink = $this->drink();

        $reservation = app(ReservationService::class)->create([
            'customer_name' => 'Ana',
            'service_type' => 'dine_in',
            'items' => [['inventory_id' => $drink->id, 'quantity' => 1]],
        ]);

        $this->withSession(['staff_user_id' => $staff->id])
            ->patchJson("/staff/reservations/{$reservation->id}/payment", ['payment_method' => 'bitcoin'])
            ->assertStatus(422);
    }

    public function test_customers_cannot_reach_the_counter_endpoints()
    {
        $drink = $this->drink();

        $reservation = app(ReservationService::class)->create([
            'customer_name' => 'Ana',
            'service_type' => 'dine_in',
            'items' => [['inventory_id' => $drink->id, 'quantity' => 1]],
        ]);

        $this->patchJson("/staff/reservations/{$reservation->id}/status", ['status' => 'ready'])
            ->assertUnauthorized();
    }

    public function test_a_customer_may_cancel_only_before_preparation_starts()
    {
        $drink = $this->drink();
        $service = app(ReservationService::class);

        $reservation = $service->create([
            'customer_name' => 'Ana',
            'service_type' => 'dine_in',
            'items' => [['inventory_id' => $drink->id, 'quantity' => 1]],
        ]);

        $service->transition($reservation, 'preparing');

        $this->postJson("/api/v1/reservations/{$reservation->reference}/cancel")
            ->assertStatus(422);

        $this->assertSame('preparing', $reservation->fresh()->status);
    }

    public function test_an_empty_basket_is_rejected()
    {
        $this->postJson('/api/v1/reservations', [
            'service_type' => 'dine_in',
            'customer_name' => 'Ana',
            'items' => [],
        ])->assertStatus(422);
    }

    public function test_the_menu_endpoint_publishes_the_cup_fee_so_clients_never_hardcode_it()
    {
        $this->drink();

        $this->getJson('/api/v1/products')
            ->assertOk()
            ->assertJsonPath('takeout_fee_per_cup', 5)
            ->assertJsonPath('data.0.name', 'Wintermelon Milktea')
            ->assertJsonPath('data.0.regular_price', 79);
    }
}
