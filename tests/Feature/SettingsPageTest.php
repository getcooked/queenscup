<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingsPageTest extends TestCase
{
    use RefreshDatabase;

    private function asAdmin(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->withSession(['staff_user_id' => $admin->id]);
    }

    public function test_settings_is_laid_out_to_fit_one_screen()
    {
        $this->asAdmin();

        $this->get('/settings')
            ->assertOk()
            // The page itself must not scroll: fixed height, clipped content.
            ->assertSee('body{height:100vh;overflow:hidden;', false)
            ->assertSee('.layout{display:flex;height:100vh}', false)
            ->assertSee('overflow:hidden;display:flex;flex-direction:column}', false)
            // All three cards share one row.
            ->assertSee('.grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr))', false);
    }

    public function test_the_three_cards_live_in_a_single_grid()
    {
        $this->asAdmin();

        $html = $this->get('/settings')->assertOk()->getContent();

        // The staff card used to sit in its own grid below the QR pair.
        $this->assertStringNotContainsString('grid spaced', $html);
        $this->assertSame(1, substr_count($html, '<div class="grid">'));
        $this->assertSame(3, substr_count($html, '<section class="card">'));
    }

    public function test_signing_out_asks_for_confirmation_first()
    {
        $this->asAdmin();

        $this->get('/settings')->assertOk()->assertSee('js/admin-sidebar.js', false);

        // The prompt is wired once in the shared script rather than per page.
        $script = file_get_contents(public_path('js/admin-sidebar.js'));
        $this->assertStringContainsString('wrapLogout', $script);
        $this->assertStringContainsString('Sign out?', $script);
        $this->assertStringContainsString('qcWrapped', $script);

        $stylesheet = file_get_contents(public_path('css/admin-shell.css'));
        $this->assertStringContainsString('.qc-confirm-backdrop', $stylesheet);
    }

    public function test_every_panel_page_gets_the_confirmation()
    {
        $this->asAdmin();

        foreach (['/dashboard', '/pos', '/inventory', '/reports', '/settings', '/profile', '/reservations'] as $uri) {
            $this->get($uri)->assertOk()->assertSee('js/admin-sidebar.js', false);
        }
    }
}
