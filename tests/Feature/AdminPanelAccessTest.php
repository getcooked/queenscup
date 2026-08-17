<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminPanelAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_from_staff_and_admin_pages()
    {
        foreach (['/dashboard', '/inventory', '/reports', '/settings', '/pos', '/profile'] as $uri) {
            $this->get($uri)->assertRedirect('/staff-login');
        }

        $this->postJson('/inventory')->assertUnauthorized();
        $this->postJson('/pos')->assertUnauthorized();
        $this->postJson('/staff')->assertUnauthorized();
        $this->postJson('/settings/qr-code')->assertUnauthorized();
    }

    public function test_cashiers_can_use_staff_pages_but_not_admin_pages()
    {
        $cashier = User::factory()->create(['role' => 'cashier']);
        $this->withSession(['staff_user_id' => $cashier->id]);

        $this->get('/pos')
            ->assertOk()
            ->assertDontSee('href="'.route('dashboard').'"', false)
            ->assertDontSee('href="'.route('inventory.index').'"', false);
        $this->get('/profile')
            ->assertOk()
            ->assertDontSee('href="'.route('dashboard').'"', false)
            ->assertDontSee('href="'.route('settings').'"', false);

        foreach (['/dashboard', '/inventory', '/reports', '/settings'] as $uri) {
            $this->get($uri)->assertRedirect('/pos');
        }

        $this->postJson('/staff')->assertForbidden();
        $this->postJson('/inventory')->assertForbidden();
    }

    public function test_admins_can_open_every_panel_page()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->withSession(['staff_user_id' => $admin->id]);

        foreach (['/dashboard', '/inventory', '/reports', '/settings', '/pos', '/profile'] as $uri) {
            $this->get($uri)->assertOk();
        }
    }

    public function test_login_returns_the_correct_destination_for_each_staff_role()
    {
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('secret123'),
            'role' => 'admin',
        ]);

        $this->postJson('/staff-login', [
            'email' => $admin->email,
            'password' => 'secret123',
        ])->assertOk()
            ->assertJsonPath('redirect_to', route('dashboard'))
            ->assertSessionHas('staff_user_id', $admin->id);

        $cashier = User::factory()->create([
            'email' => 'cashier@example.com',
            'password' => Hash::make('secret123'),
            'role' => 'cashier',
        ]);

        $this->postJson('/staff-login', [
            'email' => $cashier->email,
            'password' => 'secret123',
        ])->assertOk()
            ->assertJsonPath('redirect_to', route('point-of-sales.index'))
            ->assertSessionHas('staff_user_id', $cashier->id);
    }

    public function test_logout_invalidates_the_staff_session()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->withSession(['staff_user_id' => $admin->id])
            ->post('/staff-logout')
            ->assertRedirect('/staff-login')
            ->assertSessionMissing('staff_user_id');

        $this->get('/dashboard')->assertRedirect('/staff-login');
    }
}
