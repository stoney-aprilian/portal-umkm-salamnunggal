<?php

namespace Tests\Feature\Owner;

use App\Models\Category;
use App\Models\Media;
use App\Models\Product;
use App\Models\Umkm;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class OwnerMediaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
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

    private function umkmFor(User $owner, string $status = 'draft'): Umkm
    {
        return Umkm::create([
            'user_id' => $owner->id,
            'category_id' => Category::firstOrCreate(['type' => 'umkm', 'name' => 'Kuliner', 'slug' => 'kuliner'])->id,
            'name' => 'Warung Maju',
            'slug' => Umkm::generateUniqueSlug('Warung Maju'),
            'status' => $status,
        ]);
    }

    private function productFor(Umkm $umkm, string $status = 'draft'): Product
    {
        return Product::create([
            'umkm_id' => $umkm->id,
            'category_id' => Category::firstOrCreate(['type' => 'product', 'name' => 'Makanan', 'slug' => 'makanan'])->id,
            'name' => 'Kopi Arabika',
            'slug' => Product::generateUniqueSlug('Kopi Arabika'),
            'description' => 'Kopi asli Gunung Papandayan.',
            'price' => 15000,
            'status' => $status,
        ]);
    }

    // 1. Guest ditolak.

    public function test_guest_cannot_upload_umkm_media(): void
    {
        $umkm = $this->umkmFor($this->owner());

        $this->post(route('owner.umkm.media.store', [$umkm, 'logo']), [
            'file_logo' => UploadedFile::fake()->image('logo.png'),
        ])->assertRedirect(route('login'));

        $this->assertDatabaseCount('media', 0);
    }

    public function test_guest_cannot_delete_media(): void
    {
        $media = $this->umkmFor($this->owner())->media()->create([
            'disk' => 'public',
            'path' => 'umkms/1/logo.png',
            'collection' => 'logo',
            'sort_order' => 0,
        ]);

        $this->delete(route('owner.media.destroy', $media))->assertRedirect(route('login'));
        $this->assertDatabaseCount('media', 1);
    }

    // 2. Administrator ditolak untuk upload owner-only.

    public function test_administrator_cannot_upload_umkm_media(): void
    {
        $umkm = $this->umkmFor($this->owner());
        $admin = $this->administrator();

        $this->actingAs($admin)
            ->post(route('owner.umkm.media.store', [$umkm, 'logo']), [
                'file_logo' => UploadedFile::fake()->image('logo.png'),
            ])->assertForbidden();

        $this->assertDatabaseCount('media', 0);
    }

    public function test_administrator_cannot_upload_product_photo(): void
    {
        $umkm = $this->umkmFor($this->owner(), 'approved');
        $product = $this->productFor($umkm);
        $admin = $this->administrator();

        $this->actingAs($admin)
            ->post(route('owner.products.media.store', [$product, 'product']), [
                'file_product' => UploadedFile::fake()->image('foto.png'),
            ])->assertForbidden();

        $this->assertDatabaseCount('media', 0);
    }

    // 3. Owner lain ditolak.

    public function test_other_owner_cannot_upload_to_someone_elses_umkm(): void
    {
        $umkm = $this->umkmFor($this->owner());
        $other = $this->owner();

        $this->actingAs($other)
            ->post(route('owner.umkm.media.store', [$umkm, 'banner']), [
                'file_banner' => UploadedFile::fake()->image('banner.png'),
            ])->assertForbidden();

        $this->assertDatabaseCount('media', 0);
    }

    public function test_other_owner_cannot_upload_to_someone_elses_product(): void
    {
        $umkm = $this->umkmFor($this->owner(), 'approved');
        $product = $this->productFor($umkm);
        $other = $this->owner();

        $this->actingAs($other)
            ->post(route('owner.products.media.store', [$product, 'product']), [
                'file_product' => UploadedFile::fake()->image('foto.png'),
            ])->assertForbidden();

        $this->assertDatabaseCount('media', 0);
    }

    public function test_other_owner_cannot_delete_someone_elses_media(): void
    {
        $umkm = $this->umkmFor($this->owner());
        $media = $umkm->media()->create([
            'disk' => 'public',
            'path' => 'umkms/'.$umkm->id.'/logo.png',
            'collection' => 'logo',
            'sort_order' => 0,
        ]);
        $other = $this->owner();

        $this->actingAs($other)
            ->delete(route('owner.media.destroy', $media))
            ->assertForbidden();

        $this->assertDatabaseHas('media', ['id' => $media->id]);
    }

    // 4-6. Owner dapat upload logo, banner, galeri.

    public function test_owner_can_upload_logo(): void
    {
        $umkm = $this->umkmFor($this->owner());

        $this->actingAs($umkm->user)
            ->post(route('owner.umkm.media.store', [$umkm, 'logo']), [
                'file_logo' => UploadedFile::fake()->image('logo.png'),
            ])->assertRedirect()->assertSessionHas('status');

        $media = $umkm->media()->where('collection', 'logo')->first();
        $this->assertNotNull($media);
        Storage::disk('public')->assertExists($media->path);
    }

    public function test_owner_can_upload_banner(): void
    {
        $umkm = $this->umkmFor($this->owner());

        $this->actingAs($umkm->user)
            ->post(route('owner.umkm.media.store', [$umkm, 'banner']), [
                'file_banner' => UploadedFile::fake()->image('banner.png'),
            ])->assertRedirect()->assertSessionHas('status');

        $media = $umkm->media()->where('collection', 'banner')->first();
        $this->assertNotNull($media);
        Storage::disk('public')->assertExists($media->path);
    }

    public function test_owner_can_upload_gallery_multiple(): void
    {
        $umkm = $this->umkmFor($this->owner());

        $this->actingAs($umkm->user)
            ->post(route('owner.umkm.media.store', [$umkm, 'gallery']), [
                'gallery' => [
                    UploadedFile::fake()->image('galeri-1.png'),
                    UploadedFile::fake()->image('galeri-2.png'),
                ],
            ])->assertRedirect()->assertSessionHas('status');

        $gallery = $umkm->media()->where('collection', 'gallery')->orderBy('sort_order')->get();
        $this->assertCount(2, $gallery);
        $this->assertTrue($gallery[0]->sort_order < $gallery[1]->sort_order);

        foreach ($gallery as $item) {
            Storage::disk('public')->assertExists($item->path);
        }
    }

    // 7. Owner dapat upload foto produk.

    public function test_owner_can_upload_product_photo(): void
    {
        $umkm = $this->umkmFor($this->owner(), 'approved');
        $product = $this->productFor($umkm);

        $this->actingAs($umkm->user)
            ->post(route('owner.products.media.store', [$product, 'product']), [
                'file_product' => UploadedFile::fake()->image('foto.png'),
            ])->assertRedirect()->assertSessionHas('status');

        $media = $product->media()->first();
        $this->assertNotNull($media);
        $this->assertSame('product', $media->collection);
        Storage::disk('public')->assertExists($media->path);
    }

    // 8-10. Validasi file.

    public function test_invalid_mime_type_is_rejected(): void
    {
        $umkm = $this->umkmFor($this->owner());

        $this->actingAs($umkm->user)
            ->post(route('owner.umkm.media.store', [$umkm, 'logo']), [
                'file_logo' => UploadedFile::fake()->create('dokumen.pdf', 10, 'application/pdf'),
            ])->assertSessionHasErrors('file_logo');

        $this->assertDatabaseCount('media', 0);
        $this->assertCount(0, Storage::disk('public')->allFiles());
    }

    public function test_svg_is_rejected(): void
    {
        $umkm = $this->umkmFor($this->owner());

        $this->actingAs($umkm->user)
            ->post(route('owner.umkm.media.store', [$umkm, 'logo']), [
                'file_logo' => UploadedFile::fake()->create('gambar.svg', 10, 'image/svg+xml'),
            ])->assertSessionHasErrors('file_logo');

        $this->assertDatabaseCount('media', 0);
        $this->assertCount(0, Storage::disk('public')->allFiles());
    }

    public function test_oversized_file_is_rejected(): void
    {
        $umkm = $this->umkmFor($this->owner());

        $this->actingAs($umkm->user)
            ->post(route('owner.umkm.media.store', [$umkm, 'logo']), [
                'file_logo' => UploadedFile::fake()->image('besar.png')->size(3000),
            ])->assertSessionHasErrors('file_logo');

        $this->assertDatabaseCount('media', 0);
        $this->assertCount(0, Storage::disk('public')->allFiles());
    }

    // 11. Filename user tidak menjadi path.

    public function test_original_filename_is_not_used_as_storage_path(): void
    {
        $umkm = $this->umkmFor($this->owner());

        $this->actingAs($umkm->user)
            ->post(route('owner.umkm.media.store', [$umkm, 'logo']), [
                'file_logo' => UploadedFile::fake()->image('logo-rahasia.png'),
            ]);

        $media = $umkm->media()->where('collection', 'logo')->first();
        $this->assertNotNull($media);
        $this->assertStringStartsWith('umkms/'.$umkm->id.'/', $media->path);
        $this->assertStringNotContainsString('logo-rahasia', $media->path);
    }

    // 12. Polymorphic target benar.

    public function test_media_record_uses_correct_polymorphic_target(): void
    {
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner);

        $productOwner = $this->owner();
        $approvedUmkm = $this->umkmFor($productOwner, 'approved');
        $product = $this->productFor($approvedUmkm);

        $this->actingAs($owner)
            ->post(route('owner.umkm.media.store', [$umkm, 'banner']), [
                'file_banner' => UploadedFile::fake()->image('banner.png'),
            ]);

        $this->actingAs($productOwner)
            ->post(route('owner.products.media.store', [$product, 'product']), [
                'file_product' => UploadedFile::fake()->image('foto.png'),
            ]);

        $umkmMedia = $umkm->media()->where('collection', 'banner')->first();
        $this->assertSame(Umkm::class, $umkmMedia->mediable_type);
        $this->assertSame($umkm->id, $umkmMedia->mediable_id);

        $productMedia = $product->media()->where('collection', 'product')->first();
        $this->assertSame(Product::class, $productMedia->mediable_type);
        $this->assertSame($product->id, $productMedia->mediable_id);
    }

    // 13-14. Status & VerificationRequest tidak berubah.

    public function test_upload_does_not_change_status_or_create_verification_request(): void
    {
        $umkm = $this->umkmFor($this->owner());
        $this->assertDatabaseCount('verification_requests', 0);

        $this->actingAs($umkm->user)
            ->post(route('owner.umkm.media.store', [$umkm, 'logo']), [
                'file_logo' => UploadedFile::fake()->image('logo.png'),
            ]);

        $this->assertSame('draft', $umkm->fresh()->status);
        $this->assertDatabaseCount('verification_requests', 0);
    }

    public function test_product_photo_upload_does_not_change_status_or_create_verification_request(): void
    {
        $umkm = $this->umkmFor($this->owner(), 'approved');
        $product = $this->productFor($umkm);
        $this->assertDatabaseCount('verification_requests', 0);

        $this->actingAs($umkm->user)
            ->post(route('owner.products.media.store', [$product, 'product']), [
                'file_product' => UploadedFile::fake()->image('foto.png'),
            ]);

        $this->assertSame('draft', $product->fresh()->status);
        $this->assertSame('approved', $umkm->fresh()->status);
        $this->assertDatabaseCount('verification_requests', 0);
    }

    // 15. Replacement tidak merusak state.

    public function test_replacing_logo_removes_old_record_and_file(): void
    {
        $umkm = $this->umkmFor($this->owner());

        $this->actingAs($umkm->user)
            ->post(route('owner.umkm.media.store', [$umkm, 'logo']), [
                'file_logo' => UploadedFile::fake()->image('logo-1.png'),
            ]);

        $old = $umkm->media()->where('collection', 'logo')->first();
        Storage::disk('public')->assertExists($old->path);

        $this->actingAs($umkm->user)
            ->post(route('owner.umkm.media.store', [$umkm, 'logo']), [
                'file_logo' => UploadedFile::fake()->image('logo-2.png'),
            ]);

        $this->assertDatabaseCount('media', 1);
        $new = $umkm->media()->where('collection', 'logo')->first();
        $this->assertNotSame($old->id, $new->id);
        Storage::disk('public')->assertExists($new->path);
        Storage::disk('public')->assertMissing($old->path);
    }

    // 16-17. Public page menampilkan media / fallback tanpa media.

    public function test_public_umkm_page_shows_stored_media(): void
    {
        $umkm = $this->umkmFor($this->owner(), 'approved');
        $media = $umkm->media()->create([
            'disk' => 'public',
            'path' => 'umkms/'.$umkm->id.'/logo.png',
            'collection' => 'logo',
            'sort_order' => 0,
        ]);

        $this->get(route('public.umkm.show', $umkm))
            ->assertOk()
            ->assertSee('/storage/'.$media->path);
    }

    public function test_public_pages_fall_back_without_media(): void
    {
        $umkm = $this->umkmFor($this->owner(), 'approved');
        $product = $this->productFor($umkm, 'approved');

        $this->get(route('public.umkm.show', $umkm))
            ->assertOk()
            ->assertDontSee('/storage/');

        $this->get(route('public.product.show', $product))
            ->assertOk()
            ->assertDontSee('/storage/');
    }

    // 18. Target media tidak dapat dimanipulasi lewat request input.

    public function test_request_input_cannot_override_media_target(): void
    {
        $umkm = $this->umkmFor($this->owner());
        $other = $this->owner();
        $otherUmkm = $this->umkmFor($other);

        $this->actingAs($umkm->user)
            ->post(route('owner.umkm.media.store', [$umkm, 'logo']), [
                'file_logo' => UploadedFile::fake()->image('logo.png'),
                'disk' => 'private',
                'path' => 'evil/path.png',
                'collection' => 'product',
                'mediable_type' => Product::class,
                'mediable_id' => 999,
            ]);

        $media = Media::first();
        $this->assertSame('public', $media->disk);
        $this->assertSame('logo', $media->collection);
        $this->assertSame(Umkm::class, $media->mediable_type);
        $this->assertSame($umkm->id, $media->mediable_id);
        $this->assertStringStartsWith('umkms/'.$umkm->id.'/', $media->path);
        Storage::disk('public')->assertExists($media->path);
        Storage::disk('local')->assertMissing($media->path);
    }

    // 19. Upload gagal tidak menghapus media lama.

    public function test_failed_upload_keeps_existing_logo_intact(): void
    {
        $umkm = $this->umkmFor($this->owner());

        $this->actingAs($umkm->user)
            ->post(route('owner.umkm.media.store', [$umkm, 'logo']), [
                'file_logo' => UploadedFile::fake()->image('logo-1.png'),
            ]);

        $old = $umkm->media()->where('collection', 'logo')->first();

        $this->actingAs($umkm->user)
            ->post(route('owner.umkm.media.store', [$umkm, 'logo']), [
                'file_logo' => UploadedFile::fake()->create('gambar.svg', 10, 'image/svg+xml'),
            ])->assertSessionHasErrors('file_logo');

        $this->assertDatabaseCount('media', 1);
        $this->assertSame($old->id, $umkm->media()->where('collection', 'logo')->first()->id);
        Storage::disk('public')->assertExists($old->path);
    }

    // 20. Konsistensi storage & DB untuk sukses/gagal.

    public function test_storage_and_db_stay_consistent_on_success_and_failure(): void
    {
        $umkm = $this->umkmFor($this->owner());

        $this->actingAs($umkm->user)
            ->post(route('owner.umkm.media.store', [$umkm, 'banner']), [
                'file_banner' => UploadedFile::fake()->image('banner.png'),
            ]);

        $media = $umkm->media()->where('collection', 'banner')->first();
        $this->assertNotNull($media);
        Storage::disk('public')->assertExists($media->path);

        $filesAfterSuccess = Storage::disk('public')->allFiles();
        $this->assertCount(1, $filesAfterSuccess);

        $this->actingAs($umkm->user)
            ->post(route('owner.umkm.media.store', [$umkm, 'gallery']), [
                'gallery' => [
                    UploadedFile::fake()->create('dokumen.pdf', 10, 'application/pdf'),
                ],
            ])->assertSessionHasErrors('gallery.*');

        $this->assertDatabaseCount('media', 1);
        $this->assertSame($filesAfterSuccess, Storage::disk('public')->allFiles());
    }

    public function test_owner_can_delete_own_gallery_media(): void
    {
        $umkm = $this->umkmFor($this->owner());
        $media = $umkm->media()->create([
            'disk' => 'public',
            'path' => 'umkms/'.$umkm->id.'/gallery/1.png',
            'collection' => 'gallery',
            'sort_order' => 1,
        ]);
        Storage::disk('public')->put($media->path, 'content');

        $this->actingAs($umkm->user)
            ->delete(route('owner.media.destroy', $media))
            ->assertRedirect()
            ->assertSessionHas('status');

        $this->assertDatabaseMissing('media', ['id' => $media->id]);
        Storage::disk('public')->assertMissing($media->path);
    }

    public function test_guest_cannot_upload_product_photo(): void
    {
        $umkm = $this->umkmFor($this->owner(), 'approved');
        $product = $this->productFor($umkm);

        $this->post(route('owner.products.media.store', [$product, 'product']), [
            'file_product' => UploadedFile::fake()->image('foto.png'),
        ])->assertRedirect(route('login'));

        $this->assertDatabaseCount('media', 0);
    }

    public function test_pending_umkm_cannot_accept_media(): void
    {
        $umkm = $this->umkmFor($this->owner(), 'pending');

        $this->actingAs($umkm->user)
            ->post(route('owner.umkm.media.store', [$umkm, 'logo']), [
                'file_logo' => UploadedFile::fake()->image('logo.png'),
            ])->assertRedirect()->assertSessionHas('error');

        $this->assertDatabaseCount('media', 0);
    }

    public function test_invalid_collection_is_rejected(): void
    {
        $umkm = $this->umkmFor($this->owner());

        $this->actingAs($umkm->user)
            ->post(route('owner.umkm.media.store', [$umkm, 'hero']), [
                'file_hero' => UploadedFile::fake()->image('hero.png'),
            ])->assertNotFound();

        $this->assertDatabaseCount('media', 0);
    }

    public function test_upload_success_flash_renders_on_edit_page(): void
    {
        $umkm = $this->umkmFor($this->owner());

        $this->actingAs($umkm->user)
            ->post(route('owner.umkm.media.store', [$umkm, 'logo']), [
                'file_logo' => UploadedFile::fake()->image('logo.png'),
            ])->assertRedirect();

        $this->get(route('owner.umkm.edit', $umkm))
            ->assertOk()
            ->assertSee('Media berhasil diunggah.');
    }

    public function test_blocked_upload_error_flash_renders_on_dashboard(): void
    {
        $umkm = $this->umkmFor($this->owner(), 'pending');

        $this->actingAs($umkm->user)
            ->post(route('owner.umkm.media.store', [$umkm, 'logo']), [
                'file_logo' => UploadedFile::fake()->image('logo.png'),
            ])->assertRedirect();

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Media hanya dapat dikelola ketika UMKM masih dapat diubah.');
    }

    // 22. Status gate pada penghapusan media.

    public function test_owner_cannot_delete_media_of_approved_umkm(): void
    {
        $umkm = $this->umkmFor($this->owner(), 'approved');
        $media = $umkm->media()->create([
            'disk' => 'public',
            'path' => 'umkms/'.$umkm->id.'/logo.png',
            'collection' => 'logo',
            'sort_order' => 0,
        ]);
        Storage::disk('public')->put($media->path, 'content');

        $this->actingAs($umkm->user)
            ->delete(route('owner.media.destroy', $media))
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertDatabaseHas('media', ['id' => $media->id]);
        Storage::disk('public')->assertExists($media->path);
    }

    public function test_owner_cannot_delete_product_photo_of_approved_product(): void
    {
        $umkm = $this->umkmFor($this->owner(), 'approved');
        $product = $this->productFor($umkm, 'approved');
        $media = $product->media()->create([
            'disk' => 'public',
            'path' => 'products/'.$product->id.'/foto.png',
            'collection' => 'product',
            'sort_order' => 0,
        ]);
        Storage::disk('public')->put($media->path, 'content');

        $this->actingAs($umkm->user)
            ->delete(route('owner.media.destroy', $media))
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertDatabaseHas('media', ['id' => $media->id]);
        Storage::disk('public')->assertExists($media->path);
    }

    public function test_owner_cannot_delete_product_media_when_umkm_not_approved(): void
    {
        $umkm = $this->umkmFor($this->owner(), 'needs_revision');
        $product = $this->productFor($umkm, 'draft');
        $media = $product->media()->create([
            'disk' => 'public',
            'path' => 'products/'.$product->id.'/foto.png',
            'collection' => 'product',
            'sort_order' => 0,
        ]);
        Storage::disk('public')->put($media->path, 'content');

        $this->actingAs($umkm->user)
            ->delete(route('owner.media.destroy', $media))
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertDatabaseHas('media', ['id' => $media->id]);
        Storage::disk('public')->assertExists($media->path);
    }

    public function test_owner_can_delete_media_of_needs_revision_umkm(): void
    {
        $umkm = $this->umkmFor($this->owner(), 'needs_revision');
        $media = $umkm->media()->create([
            'disk' => 'public',
            'path' => 'umkms/'.$umkm->id.'/gallery/1.png',
            'collection' => 'gallery',
            'sort_order' => 1,
        ]);
        Storage::disk('public')->put($media->path, 'content');

        $this->actingAs($umkm->user)
            ->delete(route('owner.media.destroy', $media))
            ->assertRedirect()
            ->assertSessionHas('status');

        $this->assertDatabaseMissing('media', ['id' => $media->id]);
        Storage::disk('public')->assertMissing($media->path);
    }

    public function test_owner_can_delete_media_of_rejected_umkm(): void
    {
        $umkm = $this->umkmFor($this->owner(), 'rejected');
        $media = $umkm->media()->create([
            'disk' => 'public',
            'path' => 'umkms/'.$umkm->id.'/banner.png',
            'collection' => 'banner',
            'sort_order' => 0,
        ]);
        Storage::disk('public')->put($media->path, 'content');

        $this->actingAs($umkm->user)
            ->delete(route('owner.media.destroy', $media))
            ->assertRedirect()
            ->assertSessionHas('status');

        $this->assertDatabaseMissing('media', ['id' => $media->id]);
        Storage::disk('public')->assertMissing($media->path);
    }

    public function test_owner_can_delete_product_photo_of_needs_revision_product(): void
    {
        $umkm = $this->umkmFor($this->owner(), 'approved');
        $product = $this->productFor($umkm, 'needs_revision');
        $media = $product->media()->create([
            'disk' => 'public',
            'path' => 'products/'.$product->id.'/foto.png',
            'collection' => 'product',
            'sort_order' => 0,
        ]);
        Storage::disk('public')->put($media->path, 'content');

        $this->actingAs($umkm->user)
            ->delete(route('owner.media.destroy', $media))
            ->assertRedirect()
            ->assertSessionHas('status');

        $this->assertDatabaseMissing('media', ['id' => $media->id]);
        Storage::disk('public')->assertMissing($media->path);
    }

    public function test_owner_can_delete_product_photo_of_rejected_product(): void
    {
        $umkm = $this->umkmFor($this->owner(), 'approved');
        $product = $this->productFor($umkm, 'rejected');
        $media = $product->media()->create([
            'disk' => 'public',
            'path' => 'products/'.$product->id.'/foto.png',
            'collection' => 'product',
            'sort_order' => 0,
        ]);
        Storage::disk('public')->put($media->path, 'content');

        $this->actingAs($umkm->user)
            ->delete(route('owner.media.destroy', $media))
            ->assertRedirect()
            ->assertSessionHas('status');

        $this->assertDatabaseMissing('media', ['id' => $media->id]);
        Storage::disk('public')->assertMissing($media->path);
    }

    public function test_administrator_cannot_delete_media(): void
    {
        $umkm = $this->umkmFor($this->owner(), 'draft');
        $media = $umkm->media()->create([
            'disk' => 'public',
            'path' => 'umkms/'.$umkm->id.'/logo.png',
            'collection' => 'logo',
            'sort_order' => 0,
        ]);
        Storage::disk('public')->put($media->path, 'content');
        $admin = $this->administrator();

        $this->actingAs($admin)
            ->delete(route('owner.media.destroy', $media))
            ->assertForbidden();

        $this->assertDatabaseHas('media', ['id' => $media->id]);
        Storage::disk('public')->assertExists($media->path);
    }
}
