<?php

namespace Tests\Feature;

use App\Models\ChatMessage;
use App\Models\Inventory;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChatAssistantTest extends TestCase
{
    use RefreshDatabase;

    private function customer(string $name = 'Ana Reyes'): User
    {
        $user = User::factory()->create(['role' => 'customer', 'name' => $name]);
        $this->withSession(['customer_user_id' => $user->id]);

        return $user;
    }

    public function test_a_visitor_can_chat_without_signing_in()
    {
        // The landing page assistant must work before anyone has an account.
        $this->postJson('/chat', ['message' => 'where are you?'])
            ->assertOk()
            ->assertJsonPath('stored', false)
            ->assertJsonStructure(['body', 'quick_replies']);

        $this->assertSame(0, ChatMessage::count());
    }

    public function test_a_signed_in_customer_has_their_chat_kept()
    {
        $customer = $this->customer();

        $this->postJson('/chat', ['message' => 'opening hours'])
            ->assertOk()
            ->assertJsonPath('stored', true);

        // Both sides of the exchange are recorded against that account.
        $this->assertSame(2, ChatMessage::where('user_id', $customer->id)->count());
        $this->assertSame('customer', ChatMessage::orderBy('id')->first()->author);
        $this->assertSame('bot', ChatMessage::orderBy('id')->skip(1)->first()->author);
    }

    public function test_history_comes_back_on_the_next_visit()
    {
        $this->customer();
        $this->postJson('/chat', ['message' => 'opening hours'])->assertOk();

        $this->getJson('/chat')
            ->assertOk()
            ->assertJsonPath('stored', true)
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.body', 'opening hours');
    }

    public function test_one_customer_never_sees_another_customers_chat()
    {
        $ana = $this->customer('Ana Reyes');
        $this->postJson('/chat', ['message' => 'ana asking about hours'])->assertOk();

        $mark = $this->customer('Mark Villanueva');
        $this->postJson('/chat', ['message' => 'mark asking about hours'])->assertOk();

        $marksChat = $this->getJson('/chat')->assertOk()->json('data');
        $bodies = array_column($marksChat, 'body');

        $this->assertContains('mark asking about hours', $bodies);
        $this->assertNotContains('ana asking about hours', $bodies);
        $this->assertSame(2, ChatMessage::where('user_id', $ana->id)->count());
        $this->assertSame(2, ChatMessage::where('user_id', $mark->id)->count());
    }

    public function test_a_visitor_gets_no_history_at_all()
    {
        $this->getJson('/chat')
            ->assertOk()
            ->assertJsonPath('stored', false)
            ->assertJsonCount(0, 'data');
    }

    public function test_the_bot_answers_from_the_live_menu()
    {
        Inventory::create(['name' => 'Wintermelon Milktea', 'category' => 'Milktea', 'regular_price' => 79, 'large_price' => 99, 'stock' => 20]);
        Inventory::create(['name' => 'Mulberry Lime', 'category' => 'Fruit Tea', 'regular_price' => 85, 'large_price' => 105, 'stock' => 12]);

        $body = $this->postJson('/chat', ['message' => 'what is on the menu?'])->assertOk()->json('body');

        $this->assertStringContainsString('Milktea', $body);
        $this->assertStringContainsString('Fruit Tea', $body);
        $this->assertStringContainsString('2 drinks', $body);
    }

    public function test_the_bot_reports_that_customers_own_reservations()
    {
        $customer = $this->customer();
        $drink = Inventory::create(['name' => 'Wintermelon', 'category' => 'Milktea', 'regular_price' => 100, 'large_price' => 120, 'stock' => 50]);

        $reference = $this->postJson('/api/v1/reservations', [
            'service_type' => 'dine_in',
            'customer_name' => 'Ana Reyes',
            'items' => [['inventory_id' => $drink->id, 'quantity' => 2]],
        ])->assertCreated()->json('reference');

        Reservation::where('reference', $reference)->update(['user_id' => $customer->id]);

        $body = $this->postJson('/chat', ['message' => 'where is my order?'])->assertOk()->json('body');

        $this->assertStringContainsString($reference, $body);
        $this->assertStringContainsString('not started yet', $body);
    }

    public function test_a_visitor_asking_about_orders_is_asked_to_sign_in()
    {
        $body = $this->postJson('/chat', ['message' => 'track my reservation'])->assertOk()->json('body');

        $this->assertStringContainsString('Sign in', $body);
    }

    public function test_the_bot_quotes_the_take_out_surcharge()
    {
        $body = $this->postJson('/chat', ['message' => 'is there a takeout fee?'])->assertOk()->json('body');

        $this->assertStringContainsString('per cup', $body);
    }

    public function test_a_customer_can_clear_their_own_chat()
    {
        $customer = $this->customer();
        $this->postJson('/chat', ['message' => 'hello'])->assertOk();

        $this->deleteJson('/chat')->assertOk();

        $this->assertSame(0, ChatMessage::where('user_id', $customer->id)->count());
    }

    public function test_an_empty_or_oversized_message_is_refused()
    {
        $this->postJson('/chat', ['message' => ''])->assertStatus(422);
        $this->postJson('/chat', ['message' => str_repeat('a', 501)])->assertStatus(422);
    }

    public function test_the_assistant_is_on_the_landing_page()
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('queens-chat.js', false)
            ->assertSee('data-queens-chat', false)
            ->assertSee('csrf-token', false);
    }
}
