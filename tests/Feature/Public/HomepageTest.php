<?php

namespace Tests\Feature\Public;

use App\Models\Category;
use App\Models\Product;
use App\Models\Umkm;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class HomepageTest extends TestCase
{
    use RefreshDatabase;

    private function umkmCategory(?string $name = null): Category
    {
        return Category::firstOrCreate([
            'type' => 'umkm',
            'name' => $name ?? 'Kuliner',
            'slug' => $name ? Str::slug($name) : 'kuliner',
        ]);
    }

    private function productCategory(?string $name = null): Category
    {
        return Category::firstOrCreate([
            'type' => 'product',
            'name' => $name ?? 'Makanan',
            'slug' => $name ? Str::slug($name) : 'makanan',
        ]);
    }

    private function owner(): User
    {
        $this->seed(DatabaseSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('owner');

        return $user;
    }

    private function administrator(): User
    {
        $this->seed(DatabaseSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('administrator');

        return $user;
    }

    private function umkmFor(string $status = 'approved', ?string $name = null, ?string $slug = null): Umkm
    {
        $name = $name ?? 'Warung Maju';
        $slug = $slug ?? Umkm::generateUniqueSlug($name);

        return Umkm::create([
            'user_id' => $this->owner()->id,
            'category_id' => $this->umkmCategory()->id,
            'name' => $name,
            'slug' => $slug,
            'status' => $status,
        ]);
    }

    private function productFor(
        ?Umkm $umkm = null,
        string $status = 'approved',
        ?string $name = null,
        ?string $slug = null,
    ): Product {
        $name = $name ?? 'Kopi Arabika';
        $slug = $slug ?? Product::generateUniqueSlug($name);

        return Product::create([
            'umkm_id' => ($umkm ?? $this->umkmFor())->id,
            'category_id' => $this->productCategory()->id,
            'name' => $name,
            'slug' => $slug,
            'description' => 'Kopi asli Gunung Papandayan.',
            'price' => 15000,
            'status' => $status,
        ]);
    }

    public function test_guest_can_access_homepage(): void
    {
        $this->get('/')
            ->assertOk();
    }

    public function test_owner_can_access_homepage(): void
    {
        $owner = $this->owner();

        $this->actingAs($owner)
            ->get('/')
            ->assertOk();
    }

    public function test_administrator_can_access_homepage(): void
    {
        $admin = $this->administrator();

        $this->actingAs($admin)
            ->get('/')
            ->assertOk();
    }

    public function test_homepage_does_not_redirect_authenticated_users(): void
    {
        $owner = $this->owner();
        $admin = $this->administrator();

        $this->actingAs($owner)->get('/')->assertOk();
        $this->actingAs($admin)->get('/')->assertOk();
    }

    public function test_hero_heading_is_rendered(): void
    {
        $this->get('/')
            ->assertSee('Portal UMKM Desa Salamnunggal');
    }

    public function test_hero_primary_cta_is_rendered(): void
    {
        $this->get('/')
            ->assertSee('Jelajahi UMKM')
            ->assertSee(route('public.umkm.index'));
    }

    public function test_approved_umkm_count_is_correct(): void
    {
        $this->umkmFor('approved', 'Warung Maju', 'warung-maju');
        $this->umkmFor('approved', 'Kedai Kopi', 'kedai-kopi');
        $this->umkmFor('pending', 'Warung Pending', 'warung-pending');

        $this->get('/')
            ->assertSee('>2</dd>', false);
    }

    public function test_non_approved_umkm_are_excluded_from_count(): void
    {
        $this->umkmFor('draft', 'Warung Draft', 'warung-draft');
        $this->umkmFor('pending', 'Warung Pending', 'warung-pending');
        $this->umkmFor('needs_revision', 'Warung Revisi', 'warung-revisi');
        $this->umkmFor('rejected', 'Warung Ditolak', 'warung-ditolak');

        $this->get('/')
            ->assertSee('>0</dd>', false);
    }

    public function test_approved_product_count_is_correct(): void
    {
        $umkm = $this->umkmFor();
        $this->productFor($umkm, 'approved', 'Kopi Arabika', 'kopi-arabika');
        $this->productFor($umkm, 'approved', 'Kopi Robusta', 'kopi-robusta');
        $this->productFor($umkm, 'approved', 'Keripik Pisang', 'keripik-pisang');

        $this->get('/')
            ->assertSee('>3</dd>', false);
    }

    public function test_products_under_non_approved_umkm_are_excluded_from_count(): void
    {
        $umkm = $this->umkmFor();
        $this->productFor($umkm, 'approved', 'Kopi Arabika', 'kopi-arabika');
        $this->productFor($umkm, 'approved', 'Kopi Robusta', 'kopi-robusta');

        $pendingUmkm = $this->umkmFor('pending', 'Warung Pending', 'warung-pending');
        $this->productFor($pendingUmkm, 'approved', 'Kopi Tersembunyi', 'kopi-tersembunyi');

        $this->get('/')
            ->assertSee('>2</dd>', false);
    }

    public function test_category_count_is_correct(): void
    {
        $this->umkmFor('approved', 'Warung Maju', 'warung-maju');
        $this->productFor();

        $this->umkmCategory('Minuman');

        $this->get('/')
            ->assertSee('>2</dd>', false)
            ->assertSee('>1</dd>', false);
    }

    public function test_no_moderation_counts_are_displayed(): void
    {
        $umkm = $this->umkmFor('pending', 'Warung Pending', 'warung-pending');
        $umkm->verificationRequests()->create([
            'user_id' => $umkm->user_id,
            'status' => 'pending',
        ]);
        $product = $this->productFor($umkm, 'pending', 'Kopi Pending', 'kopi-pending');
        $product->verificationRequests()->create([
            'user_id' => $umkm->user_id,
            'status' => 'pending',
        ]);

        $this->get('/')
            ->assertSee('>0</dd>', false)
            ->assertDontSee('Menunggu')
            ->assertDontSee('Perlu Revisi')
            ->assertDontSee('Ditolak');
    }

    public function test_category_section_is_rendered_with_public_data(): void
    {
        $this->umkmFor();

        $this->get('/')
            ->assertSee('Kategori UMKM')
            ->assertSee('Kuliner');
    }

    public function test_only_umkm_category_type_is_used_in_category_section(): void
    {
        $this->umkmFor('approved', 'Warung Maju', 'warung-maju');
        $this->productFor();

        $html = $this->get('/')->getContent();
        $categorySection = Str::between($html, 'Kategori UMKM', 'UMKM Unggulan');

        $this->assertStringContainsString('Kuliner', $categorySection);
        $this->assertStringNotContainsString('Makanan', $categorySection);
    }

    public function test_categories_without_public_data_are_hidden(): void
    {
        $this->umkmCategory('Minuman');
        $this->productCategory('Kerajinan');

        $this->get('/')
            ->assertDontSee('Minuman')
            ->assertDontSee('Kerajinan');
    }

    public function test_category_section_is_hidden_when_empty(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertDontSee('Kategori UMKM');
    }

    public function test_approved_umkm_appears_in_featured(): void
    {
        $umkm = $this->umkmFor('approved', 'Kedai Kopi Senja', 'kedai-kopi-senja');

        $this->get('/')
            ->assertSee('UMKM Unggulan')
            ->assertSee('Kedai Kopi Senja');
    }

    public function test_non_approved_umkm_do_not_appear_in_featured(): void
    {
        $this->umkmFor('draft', 'Warung Draft', 'warung-draft');
        $this->umkmFor('pending', 'Warung Pending', 'warung-pending');
        $this->umkmFor('needs_revision', 'Warung Revisi', 'warung-revisi');
        $this->umkmFor('rejected', 'Warung Ditolak', 'warung-ditolak');

        $this->get('/')
            ->assertDontSee('Warung Draft')
            ->assertDontSee('Warung Pending')
            ->assertDontSee('Warung Revisi')
            ->assertDontSee('Warung Ditolak')
            ->assertDontSee('UMKM Unggulan');
    }

    public function test_umkm_link_uses_slug(): void
    {
        $this->umkmFor('approved', 'Kedai Kopi Senja', 'kedai-kopi-senja');

        $this->get('/')
            ->assertSee('/umkm/kedai-kopi-senja');
    }

    public function test_approved_product_under_approved_umkm_appears_in_featured(): void
    {
        $product = $this->productFor();

        $this->get('/')
            ->assertSee('Produk Unggulan')
            ->assertSee($product->name);
    }

    public function test_non_approved_products_do_not_appear_in_featured(): void
    {
        $umkm = $this->umkmFor();
        $this->productFor($umkm, 'draft', 'Kopi Draft', 'kopi-draft');
        $this->productFor($umkm, 'pending', 'Kopi Pending', 'kopi-pending');
        $this->productFor($umkm, 'needs_revision', 'Kopi Revisi', 'kopi-revisi');
        $this->productFor($umkm, 'rejected', 'Kopi Ditolak', 'kopi-ditolak');

        $this->get('/')
            ->assertDontSee('Kopi Draft')
            ->assertDontSee('Kopi Pending')
            ->assertDontSee('Kopi Revisi')
            ->assertDontSee('Kopi Ditolak')
            ->assertDontSee('Produk Unggulan');
    }

    public function test_products_under_non_approved_umkm_do_not_appear_in_featured(): void
    {
        $pendingUmkm = $this->umkmFor('pending', 'Warung Pending', 'warung-pending');
        $this->productFor($pendingUmkm, 'approved', 'Kopi Tersembunyi', 'kopi-tersembunyi');

        $this->get('/')
            ->assertDontSee('Kopi Tersembunyi')
            ->assertDontSee('Produk Unggulan');
    }

    public function test_product_link_uses_slug(): void
    {
        $this->productFor(null, 'approved', 'Kopi Arabika', 'kopi-arabika');

        $this->get('/')
            ->assertSee('/produk/kopi-arabika');
    }

    public function test_cta_appears(): void
    {
        $this->get('/')
            ->assertSee('Punya usaha di Desa Salamnunggal?')
            ->assertSee('Daftarkan UMKM');
    }

    public function test_cta_links_to_valid_routes(): void
    {
        $this->get('/')
            ->assertSee(route('register'))
            ->assertSee(route('login'));
    }

    public function test_homepage_renders_with_media(): void
    {
        $umkm = $this->umkmFor('approved', 'Kedai Kopi Senja', 'kedai-kopi-senja');
        $umkm->media()->create([
            'disk' => 'public',
            'path' => 'umkms/'.$umkm->id.'/logo.png',
            'collection' => 'logo',
        ]);

        $this->get('/')
            ->assertSee('/storage/umkms/'.$umkm->id.'/logo.png');
    }

    public function test_homepage_renders_without_media(): void
    {
        $this->umkmFor();

        $this->get('/')
            ->assertOk()
            ->assertDontSee('/storage/');
    }

    public function test_homepage_does_not_create_records(): void
    {
        $this->get('/');
        $this->get('/');

        $this->assertDatabaseCount('umkms', 0);
        $this->assertDatabaseCount('products', 0);
        $this->assertDatabaseCount('verification_requests', 0);
        $this->assertDatabaseCount('media', 0);
    }

    public function test_reviewer_and_notes_are_not_exposed(): void
    {
        $reviewer = $this->administrator();
        $umkm = $this->umkmFor();
        $umkm->verificationRequests()->create([
            'user_id' => $umkm->user_id,
            'reviewer_id' => $reviewer->id,
            'status' => 'approved',
            'notes' => 'Catatan pemeriksaan internal.',
            'reviewed_at' => now(),
        ]);

        $this->get('/')
            ->assertDontSee($reviewer->name)
            ->assertDontSee('Catatan pemeriksaan internal.');
    }

    public function test_internal_status_values_are_not_exposed(): void
    {
        $this->umkmFor('approved', 'Warung Maju', 'warung-maju');

        $this->get('/')
            ->assertDontSee('draft')
            ->assertDontSee('pending')
            ->assertDontSee('needs_revision')
            ->assertDontSee('rejected');
    }

    public function test_guest_navigation_remains_correct(): void
    {
        $this->get('/')
            ->assertSee('Beranda')
            ->assertSee('UMKM')
            ->assertSee('Produk')
            ->assertSee('Masuk')
            ->assertSee('Daftarkan UMKM')
            ->assertDontSee('/admin/dashboard')
            ->assertDontSee('/admin/umkm/verification')
            ->assertDontSee('/admin/products/verification');
    }

    public function test_admin_navigation_remains_correct(): void
    {
        $admin = $this->administrator();

        $this->actingAs($admin)
            ->get('/')
            ->assertSee('/admin/dashboard')
            ->assertSee('/admin/umkm/verification')
            ->assertSee('/admin/products/verification');
    }

    public function test_owner_navigation_remains_correct(): void
    {
        $owner = $this->owner();

        $this->actingAs($owner)
            ->get('/')
            ->assertSee('/dashboard')
            ->assertDontSee('/admin/dashboard')
            ->assertDontSee('/admin/umkm/verification');
    }

    public function test_homepage_search_form_is_present(): void
    {
        $this->get('/')
            ->assertSee(route('public.search'))
            ->assertSee('Cari UMKM atau produk...')
            ->assertSee('Cari');
    }

    public function test_search_page_is_accessible_as_guest(): void
    {
        $this->get(route('public.search'))
            ->assertOk()
            ->assertSee('Cari UMKM dan Produk');
    }

    public function test_search_finds_approved_umkm_by_name(): void
    {
        $this->umkmFor('approved', 'Kedai Kopi Senja', 'kedai-kopi-senja');

        $this->get(route('public.search', ['q' => 'Kopi']))
            ->assertOk()
            ->assertSee('Kedai Kopi Senja')
            ->assertSee('/umkm/kedai-kopi-senja');
    }

    public function test_search_shows_umkm_logo_with_fallback(): void
    {
        $umkm = $this->umkmFor('approved', 'Kedai Kopi Senja', 'kedai-kopi-senja');
        $umkm->media()->create([
            'disk' => 'public',
            'path' => 'umkms/'.$umkm->id.'/logo.png',
            'collection' => 'logo',
        ]);

        $this->get(route('public.search', ['q' => 'Kopi']))
            ->assertOk()
            ->assertSee('/storage/umkms/'.$umkm->id.'/logo.png');
    }

    public function test_search_shows_product_photo_with_fallback(): void
    {
        $product = $this->productFor(null, 'approved', 'Kopi Arabika', 'kopi-arabika');
        $product->media()->create([
            'disk' => 'public',
            'path' => 'products/'.$product->id.'/foto.png',
            'collection' => 'product',
        ]);

        $this->get(route('public.search', ['q' => 'Arabika']))
            ->assertOk()
            ->assertSee('/storage/products/'.$product->id.'/foto.png');
    }

    public function test_search_renders_results_without_media(): void
    {
        $this->umkmFor('approved', 'Kedai Kopi Senja', 'kedai-kopi-senja');

        $this->get(route('public.search', ['q' => 'Kopi']))
            ->assertOk()
            ->assertSee('Kedai Kopi Senja')
            ->assertDontSee('/storage/');
    }

    public function test_search_excludes_non_approved_umkm(): void
    {
        $this->umkmFor('draft', 'Kopi Draft', 'kopi-draft');
        $this->umkmFor('pending', 'Kopi Pending', 'kopi-pending');
        $this->umkmFor('needs_revision', 'Kopi Revisi', 'kopi-revisi');
        $this->umkmFor('rejected', 'Kopi Ditolak', 'kopi-ditolak');

        $this->get(route('public.search', ['q' => 'Kopi']))
            ->assertOk()
            ->assertDontSee('Kopi Draft')
            ->assertDontSee('Kopi Pending')
            ->assertDontSee('Kopi Revisi')
            ->assertDontSee('Kopi Ditolak')
            ->assertSee('Tidak menemukan hasil untuk kata kunci');
    }

    public function test_search_finds_approved_product_under_approved_umkm(): void
    {
        $this->productFor(null, 'approved', 'Kopi Arabika', 'kopi-arabika');

        $this->get(route('public.search', ['q' => 'Arabika']))
            ->assertOk()
            ->assertSee('Kopi Arabika')
            ->assertSee('/produk/kopi-arabika');
    }

    public function test_search_excludes_product_under_non_approved_umkm(): void
    {
        $pendingUmkm = $this->umkmFor('pending', 'Warung Pending', 'warung-pending');
        $this->productFor($pendingUmkm, 'approved', 'Kopi Gelap', 'kopi-gelap');

        $this->get(route('public.search', ['q' => 'Gelap']))
            ->assertOk()
            ->assertDontSee('Kopi Gelap')
            ->assertSee('Tidak menemukan hasil untuk kata kunci');
    }

    public function test_search_excludes_non_approved_products(): void
    {
        $umkm = $this->umkmFor();
        $this->productFor($umkm, 'draft', 'Kopi Draft', 'kopi-draft');
        $this->productFor($umkm, 'pending', 'Kopi Pending', 'kopi-pending');
        $this->productFor($umkm, 'needs_revision', 'Kopi Revisi', 'kopi-revisi');
        $this->productFor($umkm, 'rejected', 'Kopi Ditolak', 'kopi-ditolak');

        $this->get(route('public.search', ['q' => 'Kopi']))
            ->assertOk()
            ->assertDontSee('Kopi Draft')
            ->assertDontSee('Kopi Pending')
            ->assertDontSee('Kopi Revisi')
            ->assertDontSee('Kopi Ditolak');
    }

    public function test_search_with_empty_query_shows_prompt(): void
    {
        $this->get(route('public.search'))
            ->assertOk()
            ->assertSee('Temukan UMKM dan produk yang Anda cari.');
    }

    public function test_search_without_results_shows_empty_state(): void
    {
        $this->get(route('public.search', ['q' => 'xyzabc']))
            ->assertOk()
            ->assertSee('Tidak menemukan hasil untuk kata kunci')
            ->assertSee('Lihat Semua UMKM')
            ->assertSee('Lihat Semua Produk');
    }

    public function test_search_does_not_create_records(): void
    {
        $this->get(route('public.search', ['q' => 'kopi']));

        $this->assertDatabaseCount('umkms', 0);
        $this->assertDatabaseCount('products', 0);
        $this->assertDatabaseCount('verification_requests', 0);
    }

    public function test_search_does_not_expose_reviewer_or_notes(): void
    {
        $reviewer = $this->administrator();
        $umkm = $this->umkmFor('approved', 'Kedai Kopi Senja', 'kedai-kopi-senja');
        $umkm->verificationRequests()->create([
            'user_id' => $umkm->user_id,
            'reviewer_id' => $reviewer->id,
            'status' => 'approved',
            'notes' => 'Catatan pemeriksaan internal.',
            'reviewed_at' => now(),
        ]);

        $this->get(route('public.search', ['q' => 'Kopi']))
            ->assertSee('Kedai Kopi Senja')
            ->assertDontSee($reviewer->name)
            ->assertDontSee('Catatan pemeriksaan internal.');
    }
}
