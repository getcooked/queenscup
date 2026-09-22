<?php

namespace Tests\Feature;

use App\Models\Inventory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerOrderingAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_browser_customer_must_sign_in_before_reserving(): void
    {
        $drink = Inventory::create([
            'name' => 'Wintermelon Milktea', 'category' => 'Milktea',
            'regular_price' => 79, 'large_price' => 99, 'stock' => 10,
        ]);

        $this->postJson('/customer/reservations', [
            'service_type' => 'dine_in',
            'items' => [['inventory_id' => $drink->id, 'quantity' => 1]],
        ])->assertUnauthorized();
    }

    public function test_a_signed_in_customer_reservation_uses_their_account_details(): void
    {
        $drink = Inventory::create([
            'name' => 'Wintermelon Milktea', 'category' => 'Milktea',
            'regular_price' => 79, 'large_price' => 99, 'stock' => 10,
        ]);
        $customer = User::factory()->create([
            'role' => 'customer',
            'name' => 'Ana Reyes',
            'email' => 'ana@example.com',
            'email_verified_at' => now(),
        ]);

        $this->withSession(['customer_user_id' => $customer->id])
            ->postJson('/customer/reservations', [
                'service_type' => 'dine_in',
                'branch' => 'mcc',
                'customer_name' => 'Spoofed Name',
                'items' => [['inventory_id' => $drink->id, 'quantity' => 1]],
            ])
            ->assertCreated()
            ->assertJsonPath('customer_name', 'Ana Reyes')
            ->assertJsonPath('branch', 'mcc');
    }
}
