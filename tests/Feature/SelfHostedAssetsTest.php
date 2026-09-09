<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Every script and stylesheet must be served from this site.
 *
 * A blocked or slow CDN does not degrade gracefully: SweetAlert2 failing to
 * arrive left `Swal` undefined, so every inventory action button threw on its
 * first line and did nothing at all. The same failure had already made the
 * Font Awesome icons invisible. Self-hosting is the only way these pages work
 * on a connection that cannot reach a CDN.
 */
class SelfHostedAssetsTest extends TestCase
{
    use RefreshDatabase;

    /** @return array<int, string> */
    private function views(): array
    {
        return glob(resource_path('views/*.blade.php')) ?: [];
    }

    public function test_no_view_loads_a_script_from_a_cdn()
    {
        $offenders = [];

        foreach ($this->views() as $view) {
            $source = file_get_contents($view);

            preg_match_all('/<script[^>]+src="(https?:\/\/[^"]+)"/i', $source, $matches);

            foreach ($matches[1] as $url) {
                $offenders[] = basename($view).' -> '.$url;
            }
        }

        $this->assertSame([], $offenders, "These scripts would break if the CDN is unreachable:\n".implode("\n", $offenders));
    }

    public function test_no_view_loads_a_stylesheet_from_a_cdn_except_fonts()
    {
        $offenders = [];

        foreach ($this->views() as $view) {
            $source = file_get_contents($view);

            preg_match_all('/<link[^>]+href="(https?:\/\/[^"]+)"/i', $source, $matches);

            foreach ($matches[1] as $url) {
                // Google Fonts degrades to a system font, so it is survivable.
                if (str_contains($url, 'fonts.googleapis.com') || str_contains($url, 'fonts.gstatic.com')) {
                    continue;
                }

                $offenders[] = basename($view).' -> '.$url;
            }
        }

        $this->assertSame([], $offenders, "These stylesheets would break if the CDN is unreachable:\n".implode("\n", $offenders));
    }

    public function test_the_self_hosted_libraries_are_actually_present()
    {
        $required = [
            'vendor/sweetalert2/sweetalert2.all.min.js',
            'vendor/chartjs/chart.umd.min.js',
            'vendor/fontawesome/css/all.min.css',
            'vendor/fontawesome/webfonts/fa-solid-900.woff2',
        ];

        foreach ($required as $path) {
            $full = public_path($path);

            $this->assertFileExists($full, "Missing self-hosted asset: {$path}");
            $this->assertGreaterThan(1000, filesize($full), "Suspiciously small, likely a failed download: {$path}");
        }
    }

    public function test_the_inventory_page_serves_sweetalert_locally()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->withSession(['staff_user_id' => $admin->id]);

        // The action buttons open a Swal dialog on their first line, so the
        // page is useless without it.
        $html = $this->get('/inventory')->assertOk()->getContent();

        $this->assertStringContainsString('vendor/sweetalert2', $html);
        $this->assertStringNotContainsString('cdn.jsdelivr.net', $html);
    }
}
