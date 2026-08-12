<?php

namespace Tests\Feature\Public;

use App\Models\Category;
use App\Models\Umkm;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NavigationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_navigation_includes_search_link(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee(route('public.search'))
            ->assertSee('Cari');
    }

    public function test_every_guest_public_page_renders_without_dead_links(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->get('/')->assertOk();
        $this->get(route('public.umkm.index'))->assertOk();
        $this->get(route('public.product.index'))->assertOk();
        $this->get(route('public.search'))->assertOk();
        $this->get(route('public.about'))->assertOk();
        $this->get(route('public.contact'))->assertOk();
    }

    public function test_owner_pages_do_not_have_dead_links(): void
    {
        $this->seed(DatabaseSeeder::class);

        $owner = User::factory()->create();
        $owner->assignRole('owner');

        $this->actingAs($owner)->get('/dashboard')->assertOk();
        $this->actingAs($owner)->get(route('public.umkm.index'))->assertOk();
        $this->actingAs($owner)->get(route('public.product.index'))->assertOk();
    }

    public function test_administrator_pages_do_not_have_dead_links(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::factory()->create();
        $admin->assignRole('administrator');

        $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk();
        $this->actingAs($admin)->get(route('admin.umkm.verification.index'))->assertOk();
        $this->actingAs($admin)->get(route('admin.products.verification.index'))->assertOk();
    }

    public function test_owner_without_umkm_sees_ajukan_umkm_link(): void
    {
        $this->seed(DatabaseSeeder::class);

        $owner = User::factory()->create();
        $owner->assignRole('owner');

        $this->actingAs($owner)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('href="'.route('owner.umkm.create').'"', false)
            ->assertSee('Ajukan UMKM');
    }

    public function test_owner_with_draft_umkm_sees_umkm_saya_link(): void
    {
        $this->seed(DatabaseSeeder::class);

        $owner = User::factory()->create();
        $owner->assignRole('owner');
        $umkm = $this->umkmFor($owner, 'draft');

        $this->actingAs($owner)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('href="'.route('owner.umkm.edit', $umkm).'"', false)
            ->assertSee('UMKM Saya');
    }

    public function test_owner_with_approved_umkm_sees_products_link(): void
    {
        $this->seed(DatabaseSeeder::class);

        $owner = User::factory()->create();
        $owner->assignRole('owner');
        $umkm = $this->umkmFor($owner, 'approved');

        $this->actingAs($owner)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('href="'.route('owner.products.index', $umkm).'"', false)
            ->assertSee('Produk')
            ->assertDontSee('UMKM Saya');
    }

    public function test_owner_with_pending_umkm_sees_umkm_link_but_not_products(): void
    {
        $this->seed(DatabaseSeeder::class);

        $owner = User::factory()->create();
        $owner->assignRole('owner');
        $umkm = $this->umkmFor($owner, 'pending');

        $this->actingAs($owner)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('href="'.route('owner.umkm.edit', $umkm).'"', false)
            ->assertSee('UMKM Saya')
            ->assertDontSee(route('owner.products.index', $umkm));
    }

    public function test_administrator_navigation_includes_lihat_portal(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::factory()->create();
        $admin->assignRole('administrator');

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('href="'.route('public.umkm.index').'"', false)
            ->assertSee('Lihat Portal');
    }

    private function umkmFor(User $owner, string $status): Umkm
    {
        $category = Category::firstOrCreate([
            'type' => 'umkm',
            'name' => 'Kuliner',
            'slug' => 'kuliner',
        ]);

        return Umkm::create([
            'user_id' => $owner->id,
            'category_id' => $category->id,
            'name' => 'Warung Maju',
            'slug' => Umkm::generateUniqueSlug('Warung Maju'),
            'status' => $status,
        ]);
    }

    public function test_registration_cta_is_available_on_guest_pages(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee(route('register'))
            ->assertSee('Daftarkan UMKM');
    }

    public function test_guest_logo_links_to_homepage(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('href="'.url('/').'"', false)
            ->assertDontSee('href="'.route('dashboard').'"', false);
    }

    public function test_authenticated_logo_links_to_dashboard(): void
    {
        $this->seed(DatabaseSeeder::class);

        $owner = User::factory()->create();
        $owner->assignRole('owner');

        $this->actingAs($owner)
            ->get('/')
            ->assertOk()
            ->assertSee('href="'.route('dashboard').'"', false);
    }

    public function test_guest_layout_logo_links_to_homepage(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('href="'.url('/').'"', false)
            ->assertDontSee('href="'.route('dashboard').'"', false);
    }

    public function test_navigation_hamburger_has_accessibility_attributes(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('aria-label="Buka menu"', false)
            ->assertSee('aria-controls="mobile-navigation"', false)
            ->assertSee('id="mobile-navigation"', false);
    }

    public function test_navigation_logo_has_accessible_label(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('aria-label="'.config('app.name', 'Portal UMKM Salamnunggal').'"', false);
    }
}
