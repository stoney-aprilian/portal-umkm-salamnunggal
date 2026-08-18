<?php

namespace Tests\Feature\Admin;

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

class ProductCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
    }

    private function administrator(): User
    {
        $this->seed(DatabaseSeeder::class);

        $user = User::factory()->create(['status' => 'approved']);
        $user->assignRole('administrator');

        return $user;
    }

    private function owner(): User
    {
        $this->seed(DatabaseSeeder::class);

        $user = User::factory()->create(['status' => 'approved']);
        $user->assignRole('owner');

        return $user;
    }

    private function umkmFor(User $owner, string $status = 'approved'): Umkm
    {
        return Umkm::create([
            'user_id' => $owner->id,
            'category_id' => Category::firstOrCreate(['type' => 'umkm', 'name' => 'Kerajinan', 'slug' => 'kerajinan'])->id,
            'name' => 'Warung Maju',
            'slug' => Umkm::generateUniqueSlug('Warung Maju'),
            'status' => $status,
        ]);
    }

    private function productCategory(): Category
    {
        return Category::firstOrCreate(
            ['type' => 'product', 'name' => 'Makanan'],
            ['slug' => 'makanan'],
        );
    }

    private function payload(Umkm $umkm, array $overrides = []): array
    {
        return array_merge([
            'umkm_id' => $umkm->id,
            'category_id' => $this->productCategory()->id,
            'name' => 'Kopi Robusta 250gr',
            'description' => 'Kopi asli Gunung Papandayan.',
            'price' => 25000,
        ], $overrides);
    }

    public function test_administrator_can_view_product_list(): void
    {
        $admin = $this->administrator();
        $umkm = $this->umkmFor($this->owner());

        Product::create([
            'umkm_id' => $umkm->id,
            'category_id' => $this->productCategory()->id,
            'name' => 'Kopi Arabika',
            'slug' => 'kopi-arabika',
            'price' => 15000,
            'status' => 'approved',
        ]);
        Product::create([
            'umkm_id' => $umkm->id,
            'category_id' => $this->productCategory()->id,
            'name' => 'Kopi Robusta',
            'slug' => 'kopi-robusta',
            'price' => 25000,
            'status' => 'pending',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.products.index'))
            ->assertOk()
            ->assertSee('Kelola Produk')
            ->assertSee('Kopi Arabika')
            ->assertSee('Kopi Robusta')
            ->assertSee('Warung Maju')
            ->assertSee('Makanan');
    }

    public function test_guest_cannot_access_product_admin_routes(): void
    {
        $this->get(route('admin.products.index'))
            ->assertRedirect(route('login'));

        $this->post(route('admin.products.store'), [
            'name' => 'Produk Baru',
        ])->assertRedirect(route('login'));
    }

    public function test_owner_cannot_access_product_admin_routes(): void
    {
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner);
        $product = Product::create([
            'umkm_id' => $umkm->id,
            'category_id' => $this->productCategory()->id,
            'name' => 'Kopi Arabika',
            'slug' => 'kopi-arabika',
            'price' => 15000,
            'status' => 'approved',
        ]);

        $this->actingAs($owner)
            ->get(route('admin.products.index'))
            ->assertForbidden();

        $this->actingAs($owner)
            ->get(route('admin.products.create'))
            ->assertForbidden();

        $this->actingAs($owner)
            ->post(route('admin.products.store'), $this->payload($umkm))
            ->assertForbidden();

        $this->actingAs($owner)
            ->put(route('admin.products.update', $product), $this->payload($umkm))
            ->assertForbidden();

        $this->actingAs($owner)
            ->delete(route('admin.products.destroy', $product))
            ->assertForbidden();
    }

    public function test_administrator_can_choose_umkm_and_create_product(): void
    {
        $admin = $this->administrator();
        $umkm = $this->umkmFor($this->owner());

        $this->actingAs($admin)
            ->get(route('admin.products.create'))
            ->assertOk()
            ->assertSee('Warung Maju');

        $this->actingAs($admin)
            ->post(route('admin.products.store'), $this->payload($umkm))
            ->assertRedirect()
            ->assertSessionHas('status');

        $this->assertDatabaseHas('products', [
            'name' => 'Kopi Robusta 250gr',
            'umkm_id' => $umkm->id,
            'status' => 'approved',
        ]);
    }

    public function test_product_belongs_to_selected_umkm_not_administrator(): void
    {
        $admin = $this->administrator();
        $umkm = $this->umkmFor($this->owner());

        $this->actingAs($admin)
            ->post(route('admin.products.store'), $this->payload($umkm));

        $product = Product::firstOrFail();

        $this->assertSame($umkm->id, $product->umkm_id);
        $this->assertSame($umkm->user_id, $product->umkm->user_id);
        $this->assertNotSame($admin->id, $product->umkm->user_id);
    }

    public function test_product_category_is_accepted_and_umkm_category_rejected(): void
    {
        $admin = $this->administrator();
        $umkm = $this->umkmFor($this->owner());
        $umkmCategory = Category::firstOrCreate(['type' => 'umkm', 'name' => 'Kerajinan'], ['slug' => 'kerajinan']);

        $this->actingAs($admin)
            ->post(route('admin.products.store'), $this->payload($umkm, [
                'category_id' => $umkmCategory->id,
            ]))
            ->assertSessionHasErrors('category_id');

        $this->assertDatabaseCount('products', 0);
    }

    public function test_all_product_fields_are_saved(): void
    {
        $admin = $this->administrator();
        $umkm = $this->umkmFor($this->owner());

        $this->actingAs($admin)
            ->post(route('admin.products.store'), $this->payload($umkm))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('products', [
            'name' => 'Kopi Robusta 250gr',
            'slug' => 'kopi-robusta-250gr',
            'description' => 'Kopi asli Gunung Papandayan.',
            'price' => '25000',
            'category_id' => $this->productCategory()->id,
            'umkm_id' => $umkm->id,
            'status' => 'approved',
        ]);
    }

    public function test_administrator_can_edit_product(): void
    {
        $admin = $this->administrator();
        $umkm = $this->umkmFor($this->owner());
        $product = Product::create([
            'umkm_id' => $umkm->id,
            'category_id' => $this->productCategory()->id,
            'name' => 'Kopi Arabika',
            'slug' => 'kopi-arabika',
            'price' => 15000,
            'status' => 'approved',
        ]);

        $this->actingAs($admin)
            ->put(route('admin.products.update', $product), $this->payload($umkm, [
                'name' => 'Kopi Arabika Premium',
                'price' => 35000,
            ]))
            ->assertRedirect()
            ->assertSessionHas('status');

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Kopi Arabika Premium',
            'slug' => 'kopi-arabika-premium',
            'price' => '35000',
        ]);
    }

    public function test_umkm_ownership_remains_correct_after_edit(): void
    {
        $admin = $this->administrator();
        $umkm = $this->umkmFor($this->owner());
        $otherUmkm = $this->umkmFor($this->owner());
        $product = Product::create([
            'umkm_id' => $umkm->id,
            'category_id' => $this->productCategory()->id,
            'name' => 'Kopi Arabika',
            'slug' => 'kopi-arabika',
            'price' => 15000,
            'status' => 'approved',
        ]);

        $this->actingAs($admin)
            ->put(route('admin.products.update', $product), $this->payload($umkm, [
                'name' => 'Kopi Arabika Diperbarui',
            ]))
            ->assertSessionHasNoErrors();

        $this->assertSame($umkm->id, $product->fresh()->umkm_id);

        $this->actingAs($admin)
            ->put(route('admin.products.update', $product), $this->payload($otherUmkm))
            ->assertSessionHasNoErrors();

        $this->assertSame($otherUmkm->id, $product->fresh()->umkm_id);
    }

    public function test_non_approved_umkm_cannot_be_selected(): void
    {
        $admin = $this->administrator();
        $umkm = $this->umkmFor($this->owner(), 'pending');

        $this->actingAs($admin)
            ->post(route('admin.products.store'), $this->payload($umkm))
            ->assertSessionHasErrors('umkm_id');

        $this->assertDatabaseCount('products', 0);
    }

    public function test_administrator_can_delete_product(): void
    {
        $admin = $this->administrator();
        $umkm = $this->umkmFor($this->owner());
        $product = Product::create([
            'umkm_id' => $umkm->id,
            'category_id' => $this->productCategory()->id,
            'name' => 'Kopi Arabika',
            'slug' => 'kopi-arabika',
            'price' => 15000,
            'status' => 'approved',
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.products.destroy', $product))
            ->assertRedirect(route('admin.products.index'))
            ->assertSessionHas('status');

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_delete_cleans_media_files_and_verification_requests(): void
    {
        $admin = $this->administrator();
        $umkm = $this->umkmFor($this->owner());
        $product = Product::create([
            'umkm_id' => $umkm->id,
            'category_id' => $this->productCategory()->id,
            'name' => 'Kopi Arabika',
            'slug' => 'kopi-arabika',
            'price' => 15000,
            'status' => 'approved',
        ]);

        $media = $product->media()->create([
            'disk' => 'public',
            'path' => 'products/'.$product->id.'/foto.jpg',
            'collection' => 'product',
            'sort_order' => 0,
        ]);
        Storage::disk('public')->put($media->path, 'content');

        $product->verificationRequests()->create([
            'user_id' => $umkm->user_id,
            'status' => 'approved',
            'reviewed_at' => now(),
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.products.destroy', $product))
            ->assertRedirect(route('admin.products.index'));

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
        $this->assertDatabaseCount('media', 0);
        $this->assertDatabaseCount('verification_requests', 0);
        Storage::disk('public')->assertMissing($media->path);
    }

    public function test_administrator_can_upload_product_photo(): void
    {
        $admin = $this->administrator();
        $umkm = $this->umkmFor($this->owner());
        $product = Product::create([
            'umkm_id' => $umkm->id,
            'category_id' => $this->productCategory()->id,
            'name' => 'Kopi Arabika',
            'slug' => 'kopi-arabika',
            'price' => 15000,
            'status' => 'approved',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.products.media.store', [$product, 'product']), [
                'file_product' => UploadedFile::fake()->image('foto.png'),
            ])->assertRedirect()->assertSessionHas('status');

        $media = $product->media()->where('collection', 'product')->first();
        $this->assertNotNull($media);
        Storage::disk('public')->assertExists($media->path);
    }

    public function test_administrator_can_replace_product_photo(): void
    {
        $admin = $this->administrator();
        $umkm = $this->umkmFor($this->owner());
        $product = Product::create([
            'umkm_id' => $umkm->id,
            'category_id' => $this->productCategory()->id,
            'name' => 'Kopi Arabika',
            'slug' => 'kopi-arabika',
            'price' => 15000,
            'status' => 'approved',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.products.media.store', [$product, 'product']), [
                'file_product' => UploadedFile::fake()->image('foto-1.png'),
            ]);

        $old = $product->media()->where('collection', 'product')->first();
        Storage::disk('public')->assertExists($old->path);

        $this->actingAs($admin)
            ->post(route('admin.products.media.store', [$product, 'product']), [
                'file_product' => UploadedFile::fake()->image('foto-2.png'),
            ])->assertSessionHas('status');

        $this->assertDatabaseCount('media', 1);
        $new = $product->media()->where('collection', 'product')->first();
        $this->assertNotSame($old->id, $new->id);
        Storage::disk('public')->assertExists($new->path);
        Storage::disk('public')->assertMissing($old->path);
    }

    public function test_administrator_can_delete_product_photo(): void
    {
        $admin = $this->administrator();
        $umkm = $this->umkmFor($this->owner());
        $product = Product::create([
            'umkm_id' => $umkm->id,
            'category_id' => $this->productCategory()->id,
            'name' => 'Kopi Arabika',
            'slug' => 'kopi-arabika',
            'price' => 15000,
            'status' => 'approved',
        ]);
        $media = $product->media()->create([
            'disk' => 'public',
            'path' => 'products/'.$product->id.'/foto.jpg',
            'collection' => 'product',
            'sort_order' => 0,
        ]);
        Storage::disk('public')->put($media->path, 'content');

        $this->actingAs($admin)
            ->delete(route('admin.media.destroy', $media))
            ->assertRedirect()->assertSessionHas('status');

        $this->assertDatabaseMissing('media', ['id' => $media->id]);
        Storage::disk('public')->assertMissing($media->path);
    }

    public function test_invalid_and_oversized_product_photo_is_rejected(): void
    {
        $admin = $this->administrator();
        $umkm = $this->umkmFor($this->owner());
        $product = Product::create([
            'umkm_id' => $umkm->id,
            'category_id' => $this->productCategory()->id,
            'name' => 'Kopi Arabika',
            'slug' => 'kopi-arabika',
            'price' => 15000,
            'status' => 'approved',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.products.media.store', [$product, 'product']), [
                'file_product' => UploadedFile::fake()->create('dokumen.pdf', 10, 'application/pdf'),
            ])->assertSessionHasErrors('file_product');

        $this->actingAs($admin)
            ->post(route('admin.products.media.store', [$product, 'product']), [
                'file_product' => UploadedFile::fake()->image('besar.png')->size(3000),
            ])->assertSessionHasErrors('file_product');

        $this->assertDatabaseCount('media', 0);
        $this->assertCount(0, Storage::disk('public')->allFiles());
    }

    public function test_product_media_points_to_correct_product(): void
    {
        $admin = $this->administrator();
        $umkm = $this->umkmFor($this->owner());
        $product = Product::create([
            'umkm_id' => $umkm->id,
            'category_id' => $this->productCategory()->id,
            'name' => 'Kopi Arabika',
            'slug' => 'kopi-arabika',
            'price' => 15000,
            'status' => 'approved',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.products.media.store', [$product, 'product']), [
                'file_product' => UploadedFile::fake()->image('foto.png'),
            ]);

        $media = Media::first();
        $this->assertSame(Product::class, $media->mediable_type);
        $this->assertSame($product->id, $media->mediable_id);
        $this->assertSame('product', $media->collection);
        $this->assertStringStartsWith('products/'.$product->id.'/', $media->path);
    }

    public function test_administrator_can_manage_products_of_approved_umkm(): void
    {
        $admin = $this->administrator();
        $umkm = $this->umkmFor($this->owner());

        $this->actingAs($admin)
            ->post(route('admin.products.store'), $this->payload($umkm))
            ->assertSessionHasNoErrors();

        $product = Product::firstOrFail();

        $this->actingAs($admin)
            ->post(route('admin.products.media.store', [$product, 'product']), [
                'file_product' => UploadedFile::fake()->image('foto.png'),
            ])->assertSessionHas('status');

        $this->assertDatabaseHas('products', ['id' => $product->id, 'status' => 'approved']);
        $this->assertDatabaseCount('media', 1);
    }

    public function test_activity_log_is_recorded(): void
    {
        $admin = $this->administrator();
        $umkm = $this->umkmFor($this->owner());

        $this->actingAs($admin)
            ->post(route('admin.products.store'), $this->payload($umkm));

        $product = Product::firstOrFail();

        $this->assertDatabaseHas('activity_log', [
            'event' => 'product_created',
            'causer_id' => $admin->id,
            'subject_id' => $product->id,
            'subject_type' => Product::class,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.products.media.store', [$product, 'product']), [
                'file_product' => UploadedFile::fake()->image('foto-1.png'),
            ]);

        $this->assertDatabaseHas('activity_log', [
            'event' => 'product_media_uploaded',
            'causer_id' => $admin->id,
            'subject_id' => $product->id,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.products.media.store', [$product, 'product']), [
                'file_product' => UploadedFile::fake()->image('foto-2.png'),
            ]);

        $this->assertDatabaseHas('activity_log', [
            'event' => 'product_media_replaced',
            'causer_id' => $admin->id,
            'subject_id' => $product->id,
        ]);

        $photo = $product->media()->where('collection', 'product')->first();

        $this->actingAs($admin)
            ->delete(route('admin.media.destroy', $photo));

        $this->assertDatabaseHas('activity_log', [
            'event' => 'product_media_deleted',
            'causer_id' => $admin->id,
            'subject_id' => $product->id,
        ]);

        $this->actingAs($admin)
            ->put(route('admin.products.update', $product), $this->payload($umkm, [
                'name' => 'Kopi Arabika Update',
            ]));

        $this->assertDatabaseHas('activity_log', [
            'event' => 'product_updated',
            'causer_id' => $admin->id,
            'subject_id' => $product->id,
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.products.destroy', $product));

        $this->assertDatabaseHas('activity_log', [
            'event' => 'product_deleted',
            'causer_id' => $admin->id,
            'subject_id' => $product->id,
        ]);
    }

    public function test_approved_product_is_visible_on_public(): void
    {
        $admin = $this->administrator();
        $umkm = $this->umkmFor($this->owner());

        $this->actingAs($admin)
            ->post(route('admin.products.store'), $this->payload($umkm))
            ->assertSessionHasNoErrors();

        $product = Product::firstOrFail();

        $this->get(route('public.product.show', $product))
            ->assertOk()
            ->assertSee('Kopi Robusta 250gr');

        $this->get(route('public.product.index'))
            ->assertOk()
            ->assertSee('Kopi Robusta 250gr');
    }

    public function test_non_approved_product_is_not_visible_on_public(): void
    {
        $this->seed(DatabaseSeeder::class);

        $owner = User::factory()->create(['status' => 'approved']);
        $owner->assignRole('owner');
        $umkm = $this->umkmFor($owner);

        $product = Product::create([
            'umkm_id' => $umkm->id,
            'category_id' => $this->productCategory()->id,
            'name' => 'Draft Produk',
            'slug' => 'draft-produk',
            'price' => 10000,
            'status' => 'draft',
        ]);

        $this->get(route('public.product.show', $product))
            ->assertNotFound();

        $this->get(route('public.product.index'))
            ->assertOk()
            ->assertDontSee('Draft Produk');
    }

    public function test_owner_product_flow_still_works(): void
    {
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner);

        $this->actingAs($owner)
            ->post(route('owner.products.store', $umkm), [
                'category_id' => $this->productCategory()->id,
                'name' => 'Kopi Milik Owner',
                'price' => 20000,
            ])
            ->assertRedirect()
            ->assertSessionHas('status');

        $product = $umkm->products()->firstOrFail();
        $this->assertSame('draft', $product->status);
        $this->assertSame($umkm->id, $product->umkm_id);

        $this->actingAs($owner)
            ->post(route('owner.products.submit', $product))
            ->assertRedirect();

        $this->assertSame('pending', $product->fresh()->status);
    }

    public function test_admin_crud_umkm_still_works(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();

        $this->actingAs($admin)
            ->get(route('admin.umkms.index'))
            ->assertOk();

        $this->actingAs($admin)
            ->post(route('admin.umkms.store'), [
                'owner_id' => $owner->id,
                'category_id' => Category::firstOrCreate(['type' => 'umkm', 'name' => 'Kerajinan'], ['slug' => 'kerajinan'])->id,
                'name' => 'Kopi Senja',
            ])->assertRedirect()->assertSessionHas('status');
    }

    public function test_admin_media_umkm_still_works(): void
    {
        $admin = $this->administrator();
        $umkm = $this->umkmFor($this->owner());

        $this->actingAs($admin)
            ->post(route('admin.umkms.media.store', [$umkm, 'logo']), [
                'file_logo' => UploadedFile::fake()->image('logo.png'),
            ])->assertRedirect()->assertSessionHas('status');

        $this->assertDatabaseCount('media', 1);
        $this->assertDatabaseHas('media', [
            'collection' => 'logo',
            'mediable_type' => Umkm::class,
            'mediable_id' => $umkm->id,
        ]);
    }
}