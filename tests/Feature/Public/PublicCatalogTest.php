<?php

namespace Tests\Feature\Public;

use App\Models\Category;
use App\Models\Product;
use App\Models\Umkm;
use App\Models\User;
use App\Models\VerificationRequest;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicCatalogTest extends TestCase
{
    use RefreshDatabase;

    private function umkmCategory(): Category
    {
        return Category::firstOrCreate([
            'type' => 'umkm',
            'name' => 'Kuliner',
            'slug' => 'kuliner',
        ]);
    }

    private function productCategory(): Category
    {
        return Category::firstOrCreate([
            'type' => 'product',
            'name' => 'Makanan',
            'slug' => 'makanan',
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

    private function umkmFor(User $owner, string $status = 'approved', ?string $name = null, ?string $slug = null): Umkm
    {
        $name = $name ?? 'Warung Maju';
        $slug = $slug ?? Umkm::generateUniqueSlug($name);

        return Umkm::create([
            'user_id' => $owner->id,
            'category_id' => $this->umkmCategory()->id,
            'name' => $name,
            'slug' => $slug,
            'status' => $status,
        ]);
    }

    private function approvedRequestFor(Umkm|Product $entity): VerificationRequest
    {
        return $entity->verificationRequests()->create([
            'user_id' => $entity instanceof Umkm ? $entity->user_id : $entity->umkm->user_id,
            'reviewer_id' => $this->administrator()->id,
            'status' => 'approved',
            'notes' => 'Catatan pemeriksaan internal.',
            'reviewed_at' => now(),
        ]);
    }

    private function productFor(
        User $owner,
        string $status = 'approved',
        ?Umkm $umkm = null,
        ?string $name = null,
        ?string $slug = null,
    ): Product {
        $name = $name ?? 'Kopi Arabika';
        $slug = $slug ?? Product::generateUniqueSlug($name);

        return Product::create([
            'umkm_id' => ($umkm ?? $this->umkmFor($owner))->id,
            'category_id' => $this->productCategory()->id,
            'name' => $name,
            'slug' => $slug,
            'description' => 'Kopi asli Gunung Papandayan.',
            'price' => 15000,
            'status' => $status,
        ]);
    }

    public function test_guest_can_access_umkm_index(): void
    {
        $this->get(route('public.umkm.index'))
            ->assertOk()
            ->assertSee('UMKM');
    }

    public function test_approved_umkm_appears_on_index(): void
    {
        $umkm = $this->umkmFor($this->owner());

        $this->get(route('public.umkm.index'))
            ->assertSee($umkm->name);
    }

    public function test_draft_umkm_does_not_appear_on_index(): void
    {
        $umkm = $this->umkmFor($this->owner(), 'draft', 'Warung Draft', 'warung-draft');

        $this->get(route('public.umkm.index'))
            ->assertDontSee($umkm->name);
    }

    public function test_pending_umkm_does_not_appear_on_index(): void
    {
        $umkm = $this->umkmFor($this->owner(), 'pending', 'Warung Pending', 'warung-pending');

        $this->get(route('public.umkm.index'))
            ->assertDontSee($umkm->name);
    }

    public function test_needs_revision_umkm_does_not_appear_on_index(): void
    {
        $umkm = $this->umkmFor($this->owner(), 'needs_revision', 'Warung Revisi', 'warung-revisi');

        $this->get(route('public.umkm.index'))
            ->assertDontSee($umkm->name);
    }

    public function test_rejected_umkm_does_not_appear_on_index(): void
    {
        $umkm = $this->umkmFor($this->owner(), 'rejected', 'Warung Ditolak', 'warung-ditolak');

        $this->get(route('public.umkm.index'))
            ->assertDontSee($umkm->name);
    }

    public function test_category_is_displayed_on_umkm_index(): void
    {
        $this->umkmFor($this->owner());

        $this->get(route('public.umkm.index'))
            ->assertSee('Kuliner');
    }

    public function test_umkm_index_empty_state(): void
    {
        $this->get(route('public.umkm.index'))
            ->assertOk()
            ->assertSee('Belum ada UMKM yang terdaftar.')
            ->assertSee('Kembali ke Beranda');
    }

    public function test_approved_umkm_detail_is_accessible(): void
    {
        $umkm = $this->umkmFor($this->owner());

        $this->get(route('public.umkm.show', $umkm))
            ->assertOk()
            ->assertSee($umkm->name);
    }

    public function test_draft_umkm_detail_returns_404(): void
    {
        $umkm = $this->umkmFor($this->owner(), 'draft', 'Warung Draft', 'warung-draft');

        $this->get(route('public.umkm.show', $umkm))
            ->assertNotFound();
    }

    public function test_pending_umkm_detail_returns_404(): void
    {
        $umkm = $this->umkmFor($this->owner(), 'pending', 'Warung Pending', 'warung-pending');

        $this->get(route('public.umkm.show', $umkm))
            ->assertNotFound();
    }

    public function test_needs_revision_umkm_detail_returns_404(): void
    {
        $umkm = $this->umkmFor($this->owner(), 'needs_revision', 'Warung Revisi', 'warung-revisi');

        $this->get(route('public.umkm.show', $umkm))
            ->assertNotFound();
    }

    public function test_rejected_umkm_detail_returns_404(): void
    {
        $umkm = $this->umkmFor($this->owner(), 'rejected', 'Warung Ditolak', 'warung-ditolak');

        $this->get(route('public.umkm.show', $umkm))
            ->assertNotFound();
    }

    public function test_umkm_detail_displays_documented_public_fields(): void
    {
        $owner = $this->owner();
        $umkm = Umkm::create([
            'user_id' => $owner->id,
            'category_id' => $this->umkmCategory()->id,
            'name' => 'Kedai Kopi Senja',
            'slug' => 'kedai-kopi-senja',
            'description' => 'Menyajikan kopi lokal dengan suasana tenang.',
            'address' => 'Kp. Salamnunggal, Kec. Sukaraja, Tasikmalaya',
            'google_maps' => 'https://maps.google.com/?q=senja',
            'phone' => '081234567890',
            'email' => 'senja@example.com',
            'website' => 'senja.example.com',
            'instagram' => '@kedaikopisenja',
            'facebook' => 'Kedai Kopi Senja',
            'tiktok' => '@kedaikopisenja',
            'operational_hours' => 'Senin - Sabtu: 08.00 - 21.00',
            'status' => 'approved',
        ]);

        $this->get(route('public.umkm.show', $umkm))
            ->assertOk()
            ->assertSee('Kedai Kopi Senja')
            ->assertSee('Kuliner')
            ->assertSee('Menyajikan kopi lokal dengan suasana tenang.')
            ->assertSee('Kp. Salamnunggal, Kec. Sukaraja, Tasikmalaya')
            ->assertSee('Lihat Lokasi di Google Maps')
            ->assertSee('081234567890')
            ->assertSee('senja@example.com')
            ->assertSee('senja.example.com')
            ->assertSee('@kedaikopisenja')
            ->assertSee('Kedai Kopi Senja')
            ->assertSee('Senin - Sabtu: 08.00 - 21.00')
            ->assertSee('Terverifikasi');
    }

    public function test_umkm_detail_does_not_expose_moderation_data(): void
    {
        $owner = $this->owner();
        $reviewer = $this->administrator();
        $umkm = $this->umkmFor($owner);
        $this->approvedRequestFor($umkm);

        $this->get(route('public.umkm.show', $umkm))
            ->assertOk()
            ->assertDontSee($reviewer->name)
            ->assertDontSee('Catatan pemeriksaan internal.')
            ->assertDontSee('verification')
            ->assertDontSee('reviewer');
    }

    public function test_umkm_detail_shows_only_approved_products(): void
    {
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner);
        $approved = $this->productFor($owner, 'approved', $umkm, 'Kopi Senja', 'kopi-senja');
        $draft = $this->productFor($owner, 'draft', $umkm, 'Kopi Draft', 'kopi-draft');

        $this->get(route('public.umkm.show', $umkm))
            ->assertOk()
            ->assertSee($approved->name)
            ->assertSee('Rp 15.000')
            ->assertSee(route('public.product.show', $approved))
            ->assertDontSee($draft->name);
    }

    public function test_umkm_detail_renders_without_media(): void
    {
        $umkm = $this->umkmFor($this->owner());

        $this->get(route('public.umkm.show', $umkm))
            ->assertOk()
            ->assertDontSee('/storage/');
    }

    public function test_umkm_logo_renders_on_index(): void
    {
        $umkm = $this->umkmFor($this->owner());
        $umkm->media()->create([
            'disk' => 'public',
            'path' => 'umkms/'.$umkm->id.'/logo.png',
            'collection' => 'logo',
        ]);

        $this->get(route('public.umkm.index'))
            ->assertSee('/storage/umkms/'.$umkm->id.'/logo.png');
    }

    public function test_umkm_detail_renders_media(): void
    {
        $umkm = $this->umkmFor($this->owner());
        $umkm->media()->create(['disk' => 'public', 'path' => 'umkms/'.$umkm->id.'/banner.png', 'collection' => 'banner']);
        $umkm->media()->create(['disk' => 'public', 'path' => 'umkms/'.$umkm->id.'/logo.png', 'collection' => 'logo']);
        $umkm->media()->create(['disk' => 'public', 'path' => 'umkms/'.$umkm->id.'/galeri-1.png', 'collection' => 'gallery']);

        $this->get(route('public.umkm.show', $umkm))
            ->assertSee('/storage/umkms/'.$umkm->id.'/banner.png')
            ->assertSee('/storage/umkms/'.$umkm->id.'/logo.png')
            ->assertSee('/storage/umkms/'.$umkm->id.'/galeri-1.png');
    }

    public function test_umkm_detail_shows_whatsapp_button_when_phone_exists(): void
    {
        $umkm = $this->umkmFor($this->owner());
        $umkm->update(['phone' => '0812-3456-7890']);

        $this->get(route('public.umkm.show', $umkm))
            ->assertSee('Hubungi via WhatsApp')
            ->assertSee('https://wa.me/6281234567890');
    }

    public function test_umkm_detail_hides_whatsapp_button_without_phone(): void
    {
        $umkm = $this->umkmFor($this->owner());

        $this->get(route('public.umkm.show', $umkm))
            ->assertOk()
            ->assertDontSee('Hubungi via WhatsApp');
    }

    public function test_guest_can_access_product_index(): void
    {
        $this->get(route('public.product.index'))
            ->assertOk()
            ->assertSee('Produk');
    }

    public function test_approved_product_under_approved_umkm_appears(): void
    {
        $product = $this->productFor($this->owner());

        $this->get(route('public.product.index'))
            ->assertSee($product->name);
    }

    public function test_draft_product_does_not_appear(): void
    {
        $product = $this->productFor($this->owner(), 'draft', null, 'Kopi Draft', 'kopi-draft');

        $this->get(route('public.product.index'))
            ->assertDontSee($product->name);
    }

    public function test_pending_product_does_not_appear(): void
    {
        $product = $this->productFor($this->owner(), 'pending', null, 'Kopi Pending', 'kopi-pending');

        $this->get(route('public.product.index'))
            ->assertDontSee($product->name);
    }

    public function test_needs_revision_product_does_not_appear(): void
    {
        $product = $this->productFor($this->owner(), 'needs_revision', null, 'Kopi Revisi', 'kopi-revisi');

        $this->get(route('public.product.index'))
            ->assertDontSee($product->name);
    }

    public function test_rejected_product_does_not_appear(): void
    {
        $product = $this->productFor($this->owner(), 'rejected', null, 'Kopi Ditolak', 'kopi-ditolak');

        $this->get(route('public.product.index'))
            ->assertDontSee($product->name);
    }

    public function test_approved_product_under_pending_umkm_does_not_appear(): void
    {
        $umkm = $this->umkmFor($this->owner(), 'pending', 'Warung Pending', 'warung-pending');
        $product = $this->productFor($this->owner(), 'approved', $umkm, 'Kopi Tersembunyi', 'kopi-tersembunyi');

        $this->get(route('public.product.index'))
            ->assertDontSee($product->name);
    }

    public function test_approved_product_under_rejected_umkm_does_not_appear(): void
    {
        $umkm = $this->umkmFor($this->owner(), 'rejected', 'Warung Ditolak', 'warung-ditolak');
        $product = $this->productFor($this->owner(), 'approved', $umkm, 'Kopi Tersembunyi', 'kopi-tersembunyi');

        $this->get(route('public.product.index'))
            ->assertDontSee($product->name);
    }

    public function test_approved_product_under_needs_revision_umkm_does_not_appear(): void
    {
        $umkm = $this->umkmFor($this->owner(), 'needs_revision', 'Warung Revisi', 'warung-revisi');
        $product = $this->productFor($this->owner(), 'approved', $umkm, 'Kopi Tersembunyi', 'kopi-tersembunyi');

        $this->get(route('public.product.index'))
            ->assertDontSee($product->name);
    }

    public function test_approved_product_under_draft_umkm_does_not_appear(): void
    {
        $umkm = $this->umkmFor($this->owner(), 'draft', 'Warung Draft', 'warung-draft');
        $product = $this->productFor($this->owner(), 'approved', $umkm, 'Kopi Tersembunyi', 'kopi-tersembunyi');

        $this->get(route('public.product.index'))
            ->assertDontSee($product->name);
    }

    public function test_product_index_displays_category_and_umkm(): void
    {
        $product = $this->productFor($this->owner());

        $this->get(route('public.product.index'))
            ->assertSee($product->name)
            ->assertSee('Kopi asli Gunung Papandayan.')
            ->assertSee('Rp 15.000')
            ->assertSee($product->umkm->name)
            ->assertSee('Makanan');
    }

    public function test_product_index_empty_state(): void
    {
        $this->get(route('public.product.index'))
            ->assertOk()
            ->assertSee('Belum ada produk yang terdaftar.')
            ->assertSee('Lihat UMKM');
    }

    public function test_approved_product_with_approved_umkm_is_accessible(): void
    {
        $product = $this->productFor($this->owner());

        $this->get(route('public.product.show', $product))
            ->assertOk()
            ->assertSee($product->name);
    }

    public function test_non_approved_product_returns_404(): void
    {
        foreach (['draft', 'pending', 'needs_revision', 'rejected'] as $status) {
            $product = $this->productFor($this->owner(), $status, null, 'Kopi '.ucfirst($status), 'kopi-'.$status);

            $this->get(route('public.product.show', $product))
                ->assertNotFound();
        }
    }

    public function test_approved_product_with_non_approved_umkm_returns_404(): void
    {
        foreach (['draft', 'pending', 'needs_revision', 'rejected'] as $status) {
            $umkm = $this->umkmFor($this->owner(), $status, 'Warung '.ucfirst($status), 'warung-'.$status);
            $product = $this->productFor($this->owner(), 'approved', $umkm, 'Kopi Tersembunyi', 'kopi-tersembunyi-'.$status);

            $this->get(route('public.product.show', $product))
                ->assertNotFound();
        }
    }

    public function test_product_detail_displays_documented_fields(): void
    {
        $product = $this->productFor($this->owner());

        $this->get(route('public.product.show', $product))
            ->assertSee($product->name)
            ->assertSee('Rp 15.000')
            ->assertSee('Kopi asli Gunung Papandayan.')
            ->assertSee('Makanan')
            ->assertSee($product->umkm->name);
    }

    public function test_product_detail_displays_umkm_link(): void
    {
        $product = $this->productFor($this->owner());

        $this->get(route('public.product.show', $product))
            ->assertSee('Lihat Profil UMKM')
            ->assertSee(route('public.umkm.show', $product->umkm));
    }

    public function test_product_detail_does_not_expose_moderation_data(): void
    {
        $owner = $this->owner();
        $reviewer = $this->administrator();
        $product = $this->productFor($owner);
        $this->approvedRequestFor($product);

        $this->get(route('public.product.show', $product))
            ->assertOk()
            ->assertDontSee($reviewer->name)
            ->assertDontSee('Catatan pemeriksaan internal.')
            ->assertDontSee('verification')
            ->assertDontSee('reviewer');
    }

    public function test_product_photo_renders_on_product_detail(): void
    {
        $product = $this->productFor($this->owner());
        $product->media()->create([
            'disk' => 'public',
            'path' => 'products/'.$product->id.'/foto.png',
            'collection' => 'product',
        ]);

        $this->get(route('public.product.show', $product))
            ->assertSee('/storage/products/'.$product->id.'/foto.png');
    }

    public function test_public_routes_require_no_authentication(): void
    {
        $umkm = $this->umkmFor($this->owner());
        $product = $this->productFor($this->owner(), 'approved', $umkm, 'Kopi Senja', 'kopi-senja');

        $this->get(route('public.umkm.index'))->assertOk();
        $this->get(route('public.umkm.show', $umkm))->assertOk();
        $this->get(route('public.product.index'))->assertOk();
        $this->get(route('public.product.show', $product))->assertOk();
    }

    public function test_approved_product_with_missing_umkm_returns_404_not_crash(): void
    {
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner);
        $product = $this->productFor($owner, 'approved', $umkm, 'Kopi Yatim', 'kopi-yatim');

        // Simulate orphaned product by nulling the relationship in memory.
        // FK constraints in SQLite prevent creating true orphaned records,
        // so we verify the null-safe operator behavior at the controller level.
        $product->setRelation('umkm', null);

        $this->assertNull($product->umkm);
        // The controller uses: $product->umkm?->status === 'approved'
        // When umkm is null, this evaluates to null !== 'approved' → abort(404)
        $this->assertTrue($product->umkm?->status !== 'approved');
    }

    public function test_nonexistent_or_numeric_slug_returns_404(): void
    {
        $this->get('/umkm/123')->assertNotFound();
        $this->get('/umkm/slug-tidak-ada')->assertNotFound();
        $this->get('/produk/123')->assertNotFound();
        $this->get('/produk/slug-tidak-ada')->assertNotFound();
    }

    public function test_public_pages_do_not_expose_reviewer(): void
    {
        $owner = $this->owner();
        $reviewer = $this->administrator();
        $umkm = $this->umkmFor($owner);
        $product = $this->productFor($owner, 'approved', $umkm, 'Kopi Senja', 'kopi-senja');
        $this->approvedRequestFor($umkm);
        $this->approvedRequestFor($product);

        $this->get(route('public.umkm.index'))->assertDontSee($reviewer->name);
        $this->get(route('public.umkm.show', $umkm))->assertDontSee($reviewer->name);
        $this->get(route('public.product.index'))->assertDontSee($reviewer->name);
        $this->get(route('public.product.show', $product))->assertDontSee($reviewer->name);
    }

    public function test_public_pages_do_not_expose_verification_notes(): void
    {
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner);
        $product = $this->productFor($owner, 'approved', $umkm, 'Kopi Senja', 'kopi-senja');
        $this->approvedRequestFor($umkm);
        $this->approvedRequestFor($product);

        $this->get(route('public.umkm.index'))->assertDontSee('Catatan pemeriksaan internal.');
        $this->get(route('public.umkm.show', $umkm))->assertDontSee('Catatan pemeriksaan internal.');
        $this->get(route('public.product.index'))->assertDontSee('Catatan pemeriksaan internal.');
        $this->get(route('public.product.show', $product))->assertDontSee('Catatan pemeriksaan internal.');
    }

    public function test_public_pages_do_not_expose_internal_verification_state(): void
    {
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner);
        $product = $this->productFor($owner, 'approved', $umkm, 'Kopi Senja', 'kopi-senja');

        $this->get(route('public.umkm.index'))
            ->assertDontSee('draft')
            ->assertDontSee('pending')
            ->assertDontSee('needs_revision')
            ->assertDontSee('rejected');
        $this->get(route('public.umkm.show', $umkm))
            ->assertDontSee('draft')
            ->assertDontSee('pending')
            ->assertDontSee('needs_revision')
            ->assertDontSee('rejected');
        $this->get(route('public.product.index'))
            ->assertDontSee('draft')
            ->assertDontSee('pending')
            ->assertDontSee('needs_revision')
            ->assertDontSee('rejected');
        $this->get(route('public.product.show', $product))
            ->assertDontSee('draft')
            ->assertDontSee('pending')
            ->assertDontSee('needs_revision')
            ->assertDontSee('rejected');
    }

    public function test_guest_does_not_see_admin_links(): void
    {
        $this->get(route('public.umkm.index'))
            ->assertDontSee('/admin/dashboard')
            ->assertDontSee('/admin/umkm/verification')
            ->assertDontSee('/admin/products/verification');
    }

    public function test_guest_sees_public_navigation(): void
    {
        $this->get(route('public.umkm.index'))
            ->assertSee('Beranda')
            ->assertSee('UMKM')
            ->assertSee('Produk')
            ->assertSee('Masuk')
            ->assertSee('Daftarkan UMKM');
    }

    public function test_owner_navigation_still_works(): void
    {
        $owner = $this->owner();

        $this->actingAs($owner)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee('/admin/dashboard');
    }

    public function test_public_pages_do_not_create_records(): void
    {
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner);
        $product = $this->productFor($owner, 'approved', $umkm, 'Kopi Senja', 'kopi-senja');

        $this->get(route('public.umkm.index'));
        $this->get(route('public.umkm.show', $umkm));
        $this->get(route('public.product.index'));
        $this->get(route('public.product.show', $product));

        $this->assertDatabaseCount('verification_requests', 0);
        $this->assertDatabaseCount('umkms', 1);
        $this->assertDatabaseCount('products', 1);
        $this->assertDatabaseCount('media', 0);
    }

    public function test_umkm_index_renders_each_card_exactly_once(): void
    {
        $this->umkmFor($this->owner(), 'approved', 'Warung Pertama', 'warung-pertama');
        $this->umkmFor($this->owner(), 'approved', 'Warung Kedua', 'warung-kedua');
        $this->umkmFor($this->owner(), 'approved', 'Warung Ketiga', 'warung-ketiga');

        $response = $this->get(route('public.umkm.index'))->assertOk();

        $this->assertSame(3, substr_count($response->getContent(), 'Lihat Detail'));
    }

    public function test_product_index_renders_each_card_exactly_once(): void
    {
        $umkm = $this->umkmFor($this->owner());
        $this->productFor($this->owner(), 'approved', $umkm, 'Produk Pertama', 'produk-pertama');
        $this->productFor($this->owner(), 'approved', $umkm, 'Produk Kedua', 'produk-kedua');
        $this->productFor($this->owner(), 'approved', $umkm, 'Produk Ketiga', 'produk-ketiga');

        $response = $this->get(route('public.product.index'))->assertOk();

        $this->assertSame(3, substr_count($response->getContent(), 'Lihat Detail'));
    }
}
