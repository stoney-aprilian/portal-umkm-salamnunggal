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

class CategoryBrowsingTest extends TestCase
{
    use RefreshDatabase;

    private function umkmCategory(?string $name = null, ?string $description = null): Category
    {
        return Category::firstOrCreate([
            'type' => 'umkm',
            'name' => $name ?? 'Kuliner',
            'slug' => $name ? Str::slug($name) : 'kuliner',
            'description' => $description,
        ]);
    }

    private function productCategory(?string $name = null, ?string $description = null): Category
    {
        return Category::firstOrCreate([
            'type' => 'product',
            'name' => $name ?? 'Makanan',
            'slug' => $name ? Str::slug($name) : 'makanan',
            'description' => $description,
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

    private function umkmFor(
        Category $category,
        string $status = 'approved',
        ?string $name = null,
        ?string $slug = null,
    ): Umkm {
        $name = $name ?? 'Warung Maju';
        $slug = $slug ?? Umkm::generateUniqueSlug($name);

        return Umkm::create([
            'user_id' => $this->owner()->id,
            'category_id' => $category->id,
            'name' => $name,
            'slug' => $slug,
            'status' => $status,
        ]);
    }

    private function productFor(
        Umkm $umkm,
        Category $category,
        string $status = 'approved',
        ?string $name = null,
        ?string $slug = null,
    ): Product {
        $name = $name ?? 'Kopi Arabika';
        $slug = $slug ?? Product::generateUniqueSlug($name);

        return Product::create([
            'umkm_id' => $umkm->id,
            'category_id' => $category->id,
            'name' => $name,
            'slug' => $slug,
            'description' => 'Kopi asli Gunung Papandayan.',
            'price' => 15000,
            'status' => $status,
        ]);
    }

    public function test_guest_can_access_umkm_category_page(): void
    {
        $category = $this->umkmCategory();

        $this->get(route('public.category.umkm', $category))
            ->assertOk()
            ->assertSee('UMKM Kategori Kuliner');
    }

    public function test_guest_can_access_product_category_page(): void
    {
        $category = $this->productCategory();

        $this->get(route('public.category.product', $category))
            ->assertOk()
            ->assertSee('Produk Kategori Makanan');
    }

    public function test_nonexistent_category_slug_returns_404(): void
    {
        $this->get('/kategori/tidak-ada/umkm')->assertNotFound();
        $this->get('/kategori/tidak-ada/produk')->assertNotFound();
    }

    public function test_nonexistent_numeric_slug_returns_404(): void
    {
        $this->get('/kategori/123/umkm')->assertNotFound();
        $this->get('/kategori/123/produk')->assertNotFound();
    }

    public function test_wrong_category_type_on_umkm_route_returns_404(): void
    {
        $this->productCategory();

        $this->get('/kategori/makanan/umkm')->assertNotFound();
    }

    public function test_wrong_category_type_on_product_route_returns_404(): void
    {
        $this->umkmCategory();

        $this->get('/kategori/kuliner/produk')->assertNotFound();
    }

    public function test_query_string_cannot_bypass_type_protection(): void
    {
        $this->umkmCategory();
        $this->productCategory();

        $this->get('/kategori/kuliner/produk?type=umkm')->assertNotFound();
        $this->get('/kategori/makanan/umkm?type=product')->assertNotFound();
    }

    public function test_approved_umkm_appears_on_category_page(): void
    {
        $category = $this->umkmCategory();
        $this->umkmFor($category, 'approved', 'Warung Maju', 'warung-maju');

        $this->get(route('public.category.umkm', $category))
            ->assertOk()
            ->assertSee('Warung Maju');
    }

    public function test_draft_umkm_is_excluded(): void
    {
        $category = $this->umkmCategory();
        $this->umkmFor($category, 'draft', 'Warung Draft', 'warung-draft');

        $this->get(route('public.category.umkm', $category))
            ->assertOk()
            ->assertDontSee('Warung Draft');
    }

    public function test_pending_umkm_is_excluded(): void
    {
        $category = $this->umkmCategory();
        $this->umkmFor($category, 'pending', 'Warung Pending', 'warung-pending');

        $this->get(route('public.category.umkm', $category))
            ->assertOk()
            ->assertDontSee('Warung Pending');
    }

    public function test_needs_revision_umkm_is_excluded(): void
    {
        $category = $this->umkmCategory();
        $this->umkmFor($category, 'needs_revision', 'Warung Revisi', 'warung-revisi');

        $this->get(route('public.category.umkm', $category))
            ->assertOk()
            ->assertDontSee('Warung Revisi');
    }

    public function test_rejected_umkm_is_excluded(): void
    {
        $category = $this->umkmCategory();
        $this->umkmFor($category, 'rejected', 'Warung Ditolak', 'warung-ditolak');

        $this->get(route('public.category.umkm', $category))
            ->assertOk()
            ->assertDontSee('Warung Ditolak');
    }

    public function test_umkm_from_selected_category_appears(): void
    {
        $kuliner = $this->umkmCategory();
        $this->umkmFor($kuliner, 'approved', 'Warung Maju', 'warung-maju');

        $this->get(route('public.category.umkm', $kuliner))
            ->assertOk()
            ->assertSee('Warung Maju');
    }

    public function test_umkm_from_another_category_is_excluded(): void
    {
        $kuliner = $this->umkmCategory();
        $minuman = $this->umkmCategory('Minuman');
        $this->umkmFor($minuman, 'approved', 'Kedai Es Teh', 'kedai-es-teh');

        $this->get(route('public.category.umkm', $kuliner))
            ->assertOk()
            ->assertDontSee('Kedai Es Teh');
    }

    public function test_umkm_category_name_is_displayed(): void
    {
        $category = $this->umkmCategory();

        $this->get(route('public.category.umkm', $category))
            ->assertOk()
            ->assertSee('UMKM Kategori Kuliner');
    }

    public function test_umkm_category_description_is_displayed(): void
    {
        $category = $this->umkmCategory('Kuliner', 'Kuliner khas Desa Salamnunggal.');

        $this->get(route('public.category.umkm', $category))
            ->assertOk()
            ->assertSee('Kuliner khas Desa Salamnunggal.');
    }

    public function test_umkm_category_description_is_omitted_when_absent(): void
    {
        $category = $this->umkmCategory();

        $this->get(route('public.category.umkm', $category))
            ->assertOk()
            ->assertDontSee('Kuliner khas');
    }

    public function test_umkm_detail_link_uses_slug(): void
    {
        $category = $this->umkmCategory();
        $umkm = $this->umkmFor($category, 'approved', 'Warung Maju', 'warung-maju');

        $this->get(route('public.category.umkm', $category))
            ->assertOk()
            ->assertSee('/umkm/warung-maju')
            ->assertDontSee('/umkm/'.$umkm->id);
    }

    public function test_umkm_category_page_renders_media(): void
    {
        $category = $this->umkmCategory();
        $umkm = $this->umkmFor($category);
        $umkm->media()->create([
            'disk' => 'public',
            'path' => 'umkms/'.$umkm->id.'/logo.png',
            'collection' => 'logo',
        ]);

        $this->get(route('public.category.umkm', $category))
            ->assertOk()
            ->assertSee('/storage/umkms/'.$umkm->id.'/logo.png');
    }

    public function test_umkm_category_page_renders_without_media(): void
    {
        $category = $this->umkmCategory();
        $this->umkmFor($category);

        $this->get(route('public.category.umkm', $category))
            ->assertOk()
            ->assertDontSee('/storage/');
    }

    public function test_umkm_category_empty_state_works(): void
    {
        $category = $this->umkmCategory();

        $this->get(route('public.category.umkm', $category))
            ->assertOk()
            ->assertSee('Belum ada UMKM dalam kategori ini.')
            ->assertSee('Lihat Semua UMKM')
            ->assertSee('Kembali ke Beranda');
    }

    public function test_umkm_category_result_count_is_displayed(): void
    {
        $category = $this->umkmCategory();
        $this->umkmFor($category, 'approved', 'Warung Maju', 'warung-maju');

        $this->get(route('public.category.umkm', $category))
            ->assertOk()
            ->assertSee('Menampilkan 1 UMKM dalam kategori ini.');
    }

    public function test_approved_product_under_approved_umkm_appears(): void
    {
        $category = $this->productCategory();
        $umkm = $this->umkmFor($this->umkmCategory());
        $this->productFor($umkm, $category, 'approved', 'Kopi Arabika', 'kopi-arabika');

        $this->get(route('public.category.product', $category))
            ->assertOk()
            ->assertSee('Kopi Arabika');
    }

    public function test_draft_product_is_excluded(): void
    {
        $category = $this->productCategory();
        $umkm = $this->umkmFor($this->umkmCategory());
        $this->productFor($umkm, $category, 'draft', 'Kopi Draft', 'kopi-draft');

        $this->get(route('public.category.product', $category))
            ->assertOk()
            ->assertDontSee('Kopi Draft');
    }

    public function test_pending_product_is_excluded(): void
    {
        $category = $this->productCategory();
        $umkm = $this->umkmFor($this->umkmCategory());
        $this->productFor($umkm, $category, 'pending', 'Kopi Pending', 'kopi-pending');

        $this->get(route('public.category.product', $category))
            ->assertOk()
            ->assertDontSee('Kopi Pending');
    }

    public function test_needs_revision_product_is_excluded(): void
    {
        $category = $this->productCategory();
        $umkm = $this->umkmFor($this->umkmCategory());
        $this->productFor($umkm, $category, 'needs_revision', 'Kopi Revisi', 'kopi-revisi');

        $this->get(route('public.category.product', $category))
            ->assertOk()
            ->assertDontSee('Kopi Revisi');
    }

    public function test_rejected_product_is_excluded(): void
    {
        $category = $this->productCategory();
        $umkm = $this->umkmFor($this->umkmCategory());
        $this->productFor($umkm, $category, 'rejected', 'Kopi Ditolak', 'kopi-ditolak');

        $this->get(route('public.category.product', $category))
            ->assertOk()
            ->assertDontSee('Kopi Ditolak');
    }

    public function test_product_from_another_category_is_excluded(): void
    {
        $makanan = $this->productCategory();
        $minuman = $this->productCategory('Minuman');
        $umkm = $this->umkmFor($this->umkmCategory());
        $this->productFor($umkm, $minuman, 'approved', 'Es Teh Manis', 'es-teh-manis');

        $this->get(route('public.category.product', $makanan))
            ->assertOk()
            ->assertDontSee('Es Teh Manis');
    }

    public function test_approved_product_under_pending_umkm_is_excluded(): void
    {
        $category = $this->productCategory();
        $pendingUmkm = $this->umkmFor($this->umkmCategory(), 'pending', 'Warung Pending', 'warung-pending');
        $this->productFor($pendingUmkm, $category, 'approved', 'Kopi Gelap', 'kopi-gelap');

        $this->get(route('public.category.product', $category))
            ->assertOk()
            ->assertDontSee('Kopi Gelap');
    }

    public function test_approved_product_under_rejected_umkm_is_excluded(): void
    {
        $category = $this->productCategory();
        $rejectedUmkm = $this->umkmFor($this->umkmCategory(), 'rejected', 'Warung Ditolak', 'warung-ditolak');
        $this->productFor($rejectedUmkm, $category, 'approved', 'Kopi Gelap', 'kopi-gelap');

        $this->get(route('public.category.product', $category))
            ->assertOk()
            ->assertDontSee('Kopi Gelap');
    }

    public function test_approved_product_under_needs_revision_umkm_is_excluded(): void
    {
        $category = $this->productCategory();
        $revisionUmkm = $this->umkmFor($this->umkmCategory(), 'needs_revision', 'Warung Revisi', 'warung-revisi');
        $this->productFor($revisionUmkm, $category, 'approved', 'Kopi Gelap', 'kopi-gelap');

        $this->get(route('public.category.product', $category))
            ->assertOk()
            ->assertDontSee('Kopi Gelap');
    }

    public function test_approved_product_under_draft_umkm_is_excluded(): void
    {
        $category = $this->productCategory();
        $draftUmkm = $this->umkmFor($this->umkmCategory(), 'draft', 'Warung Draft', 'warung-draft');
        $this->productFor($draftUmkm, $category, 'approved', 'Kopi Gelap', 'kopi-gelap');

        $this->get(route('public.category.product', $category))
            ->assertOk()
            ->assertDontSee('Kopi Gelap');
    }

    public function test_product_category_name_is_displayed(): void
    {
        $category = $this->productCategory();

        $this->get(route('public.category.product', $category))
            ->assertOk()
            ->assertSee('Produk Kategori Makanan');
    }

    public function test_product_category_description_is_displayed(): void
    {
        $category = $this->productCategory('Makanan', 'Makanan ringan dan berat dari Desa Salamnunggal.');

        $this->get(route('public.category.product', $category))
            ->assertOk()
            ->assertSee('Makanan ringan dan berat dari Desa Salamnunggal.');
    }

    public function test_umkm_name_is_displayed_on_product_category_page(): void
    {
        $category = $this->productCategory();
        $umkm = $this->umkmFor($this->umkmCategory(), 'approved', 'Warung Maju', 'warung-maju');
        $this->productFor($umkm, $category);

        $this->get(route('public.category.product', $category))
            ->assertOk()
            ->assertSee('Warung Maju');
    }

    public function test_product_detail_link_uses_slug(): void
    {
        $category = $this->productCategory();
        $umkm = $this->umkmFor($this->umkmCategory());
        $product = $this->productFor($umkm, $category, 'approved', 'Kopi Arabika', 'kopi-arabika');

        $this->get(route('public.category.product', $category))
            ->assertOk()
            ->assertSee('/produk/kopi-arabika')
            ->assertDontSee('/produk/'.$product->id);
    }

    public function test_product_category_page_renders_media(): void
    {
        $category = $this->productCategory();
        $umkm = $this->umkmFor($this->umkmCategory());
        $product = $this->productFor($umkm, $category);
        $product->media()->create([
            'disk' => 'public',
            'path' => 'products/'.$product->id.'/kopi.png',
            'collection' => 'product',
        ]);

        $this->get(route('public.category.product', $category))
            ->assertOk()
            ->assertSee('/storage/products/'.$product->id.'/kopi.png');
    }

    public function test_product_category_page_renders_without_media(): void
    {
        $category = $this->productCategory();
        $umkm = $this->umkmFor($this->umkmCategory());
        $this->productFor($umkm, $category);

        $this->get(route('public.category.product', $category))
            ->assertOk()
            ->assertDontSee('/storage/');
    }

    public function test_product_category_empty_state_works(): void
    {
        $category = $this->productCategory();

        $this->get(route('public.category.product', $category))
            ->assertOk()
            ->assertSee('Belum ada produk dalam kategori ini.')
            ->assertSee('Lihat Semua Produk')
            ->assertSee('Kembali ke Beranda');
    }

    public function test_product_category_result_count_is_displayed(): void
    {
        $category = $this->productCategory();
        $umkm = $this->umkmFor($this->umkmCategory());
        $this->productFor($umkm, $category, 'approved', 'Kopi Arabika', 'kopi-arabika');

        $this->get(route('public.category.product', $category))
            ->assertOk()
            ->assertSee('Menampilkan 1 produk dalam kategori ini.');
    }

    public function test_homepage_category_pills_are_clickable(): void
    {
        $category = $this->umkmCategory();
        $this->umkmFor($category);

        $this->get('/')
            ->assertOk()
            ->assertSee(route('public.category.umkm', $category));
    }

    public function test_homepage_category_pill_points_to_correct_route(): void
    {
        $category = $this->umkmCategory();
        $this->umkmFor($category);

        $this->get('/')
            ->assertOk()
            ->assertSee('/kategori/kuliner/umkm');
    }

    public function test_homepage_does_not_expose_wrong_category_type_route(): void
    {
        $category = $this->umkmCategory();
        $this->umkmFor($category);

        $this->get('/')
            ->assertOk()
            ->assertDontSee('/kategori/kuliner/produk');
    }

    public function test_public_category_pages_require_no_authentication(): void
    {
        $umkmCategory = $this->umkmCategory();
        $productCategory = $this->productCategory();

        $this->get(route('public.category.umkm', $umkmCategory))->assertOk();
        $this->get(route('public.category.product', $productCategory))->assertOk();
    }

    public function test_category_pages_do_not_expose_reviewer_information(): void
    {
        $reviewer = $this->administrator();
        $category = $this->umkmCategory();
        $umkm = $this->umkmFor($category);
        $umkm->verificationRequests()->create([
            'user_id' => $umkm->user_id,
            'reviewer_id' => $reviewer->id,
            'status' => 'approved',
            'reviewed_at' => now(),
        ]);

        $this->get(route('public.category.umkm', $category))
            ->assertOk()
            ->assertDontSee($reviewer->name);
    }

    public function test_category_pages_do_not_expose_verification_notes(): void
    {
        $category = $this->umkmCategory();
        $umkm = $this->umkmFor($category);
        $umkm->verificationRequests()->create([
            'user_id' => $umkm->user_id,
            'status' => 'approved',
            'notes' => 'Catatan pemeriksaan internal.',
            'reviewed_at' => now(),
        ]);

        $this->get(route('public.category.umkm', $category))
            ->assertOk()
            ->assertDontSee('Catatan pemeriksaan internal.');
    }

    public function test_category_pages_create_no_records(): void
    {
        $category = $this->umkmCategory();
        $this->umkmFor($category);

        $this->get(route('public.category.umkm', $category))->assertOk();
        $this->get(route('public.category.umkm', $category))->assertOk();

        $this->assertDatabaseCount('umkms', 1);
        $this->assertDatabaseCount('products', 0);
        $this->assertDatabaseCount('verification_requests', 0);
        $this->assertDatabaseCount('media', 0);
    }

    public function test_owner_and_admin_functionality_remains_unaffected(): void
    {
        $owner = $this->owner();
        $admin = $this->administrator();

        $this->get(route('owner.umkm.create'))->assertRedirect(route('login'));
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));

        $this->actingAs($owner)->get(route('owner.umkm.create'))->assertOk();
        $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk();
    }

    public function test_internal_status_values_are_not_exposed(): void
    {
        $category = $this->umkmCategory();
        $this->umkmFor($category);

        $this->get(route('public.category.umkm', $category))
            ->assertOk()
            ->assertDontSee('draft')
            ->assertDontSee('pending')
            ->assertDontSee('needs_revision')
            ->assertDontSee('rejected');
    }
}
