<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ErrorPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_unknown_page_renders_custom_404(): void
    {
        $this->get('/halaman-tidak-ada')
            ->assertNotFound()
            ->assertSee('404')
            ->assertSee('Halaman Tidak Ditemukan')
            ->assertSee('Kembali ke Beranda');
    }

    public function test_forbidden_page_renders_custom_403(): void
    {
        $this->seed(DatabaseSeeder::class);

        $owner = User::factory()->create();
        $owner->assignRole('owner');

        $this->actingAs($owner)
            ->get(route('admin.dashboard'))
            ->assertForbidden()
            ->assertSee('403')
            ->assertSee('Akses Ditolak')
            ->assertSee('Kembali ke Beranda');
    }

    public function test_error_pages_do_not_leak_laravel_starter_identity(): void
    {
        $this->get('/halaman-tidak-ada')
            ->assertNotFound()
            ->assertDontSee('Laravel')
            ->assertSee('Portal UMKM Salamnunggal');
    }

    public function test_rate_limited_requests_render_custom_429(): void
    {
        $this->app->make(\Illuminate\Routing\Router::class)
            ->get('/throttle-test', fn () => 'ok')
            ->middleware('throttle:1,1');

        $this->get('/throttle-test')->assertOk();

        $this->get('/throttle-test')
            ->assertStatus(429)
            ->assertSee('429')
            ->assertSee('Terlalu Banyak Permintaan')
            ->assertSee('Kembali ke Beranda');
    }
}
