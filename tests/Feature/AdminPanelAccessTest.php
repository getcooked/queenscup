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
            $this->get($uri)
                ->assertOk()
                ->assertSee('css/admin-shell.css', false)
                ->assertSee('js/admin-sidebar.js', false);
        }
    }

    public function test_staff_orders_sidebar_uses_server_routes_for_admin_pages()
    {
        $this->get('/orders')
            ->assertOk()
            ->assertSee('js/admin-sidebar.js', false)
            ->assertSee('data-current-page="orders"', false)
            ->assertDontSee('href="#inventory" data-page="inventory"', false)
            ->assertDontSee('href="#reports" data-page="reports"', false)
            ->assertDontSee('href="#settings" data-page="settings"', false);
    }

    public function test_shared_sidebar_assets_add_bootstrap_icons_and_the_orders_indicator()
    {
        $script = file_get_contents(public_path('js/admin-sidebar.js'));
        $stylesheet = file_get_contents(public_path('css/admin-shell.css'));

        $this->assertStringContainsString("dashboard: 'bi-speedometer2'", $script);
        $this->assertStringContainsString("orders: 'bi-receipt'", $script);
        $this->assertStringContainsString('data-orders-indicator', $script);
        $this->assertStringContainsString('bootstrap-icons@1.11.3', $stylesheet);
        $this->assertStringContainsString('.sidebar .nav-badge', $stylesheet);
    }

    public function test_orders_prefers_the_authenticated_staff_session_for_its_shell()
    {
        $admin = User::factory()->create([
            'name' => 'Sidebar Admin',
            'email' => 'sidebar@example.com',
            'role' => 'admin',
        ]);

        $payload = [
            'id' => $admin->id,
            'username' => $admin->email,
            'role' => 'admin',
            'fullName' => $admin->name,
            'email' => $admin->email,
        ];

        $this->withSession(['staff_user_id' => $admin->id])
            ->get('/orders')
            ->assertOk()
            ->assertSee('var AUTHENTICATED_STAFF='.json_encode($payload).';', false);
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
