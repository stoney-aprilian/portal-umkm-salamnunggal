<?php

namespace Tests\Feature\Public;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FooterTest extends TestCase
{
    use RefreshDatabase;

    public function test_footer_shows_contact_information_from_settings(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->get('/')
            ->assertOk()
            ->assertSee('Portal UMKM Salamnunggal')
            ->assertSee('Kantor Desa Salamnunggal')
            ->assertSee('+62 812-3456-7890')
            ->assertSee('portal@umkm-salamnunggal.id')
            ->assertSee('Senin - Jumat, 08.00 - 15.00 WIB');
    }

    public function test_footer_falls_back_honestly_without_settings(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Portal UMKM Salamnunggal');
    }

    public function test_footer_quick_links_are_present(): void
    {
        $this->get('/')
            ->assertSee(route('public.umkm.index'))
            ->assertSee(route('public.product.index'))
            ->assertSee(route('public.search'))
            ->assertSee(route('public.about'))
            ->assertSee(route('public.contact'))
            ->assertSee(route('register'));
    }

    public function test_footer_is_rendered_across_pages(): void
    {
        $this->seed(DatabaseSeeder::class);

        foreach (['/', '/umkm', '/produk', route('public.about'), route('public.contact'), route('public.search')] as $page) {
            $this->get($page)
                ->assertOk()
                ->assertSee('Jelajahi');
        }

        $this->actingAs($this->owner())
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Jelajahi');
    }

    private function owner(): \App\Models\User
    {
        $this->seed(DatabaseSeeder::class);

        $user = \App\Models\User::factory()->create();
        $user->assignRole('owner');

        return $user;
    }
}
