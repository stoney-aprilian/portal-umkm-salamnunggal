<?php

namespace Tests\Feature\Public;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicExperienceTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_input_has_accessible_label(): void
    {
        $this->get(route('public.search'))
            ->assertOk()
            ->assertSee('Kata kunci pencarian')
            ->assertSee('Cari');
    }

    public function test_homepage_hero_search_form_is_complete(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee(route('public.search'))
            ->assertSee('Cari UMKM atau produk...')
            ->assertSee('Cari');
    }

    public function test_public_pages_render_with_empty_catalog(): void
    {
        $this->get('/')->assertOk();
        $this->get(route('public.umkm.index'))->assertOk()->assertSee('Belum ada UMKM yang terdaftar.');
        $this->get(route('public.product.index'))->assertOk()->assertSee('Belum ada produk yang terdaftar.');
    }

    public function test_footer_contact_links_use_proper_schemes(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->get('/')
            ->assertOk()
            ->assertSee('href="tel:+6281234567890"', false)
            ->assertSee('href="mailto:portal@umkm-salamnunggal.id"', false);
    }

    public function test_guest_can_reach_search_from_search_results_page(): void
    {
        $this->get(route('public.search', ['q' => 'xyzabc']))
            ->assertOk()
            ->assertSee('Lihat Semua UMKM')
            ->assertSee('Lihat Semua Produk');
    }

    public function test_pages_use_application_identity_in_title(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('<title>Portal UMKM Salamnunggal</title>', false);

        $this->get(route('login'))
            ->assertOk()
            ->assertSee('<title>Masuk — Portal UMKM Salamnunggal</title>', false);
    }

    public function test_public_pages_do_not_leak_laravel_starter_identity(): void
    {
        foreach (['/', '/umkm', '/produk', route('public.search'), route('public.about'), route('public.contact')] as $page) {
            $this->get($page)
                ->assertOk()
                ->assertDontSee('Laravel');
        }
    }
}
