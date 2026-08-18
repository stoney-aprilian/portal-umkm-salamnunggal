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

class AdminMediaTest extends TestCase
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

    private function umkmFor(User $owner, string $status = 'draft'): Umkm
    {
        return Umkm::create([
            'user_id' => $owner->id,
            'category_id' => Category::firstOrCreate(['type' => 'umkm', 'name' => 'Kerajinan', 'slug' => 'kerajinan'])->id,
            'name' => 'Warung Maju',
            'slug' => Umkm::generateUniqueSlug('Warung Maju'),
            'status' => $status,
        ]);
    }

    public function test_administrator_can_upload_logo(): void
    {
        $admin = $this->administrator();
        $umkm = $this->umkmFor($this->owner());

        $this->actingAs($admin)
            ->post(route('admin.umkms.media.store', [$umkm, 'logo']), [
                'file_logo' => UploadedFile::fake()->image('logo.png'),
            ])->assertRedirect()->assertSessionHas('status');

        $media = $umkm->media()->where('collection', 'logo')->first();
        $this->assertNotNull($media);
        $this->assertSame('logo', $media->collection);
        Storage::disk('public')->assertExists($media->path);
    }

    public function test_administrator_can_upload_banner(): void
    {
        $admin = $this->administrator();
        $umkm = $this->umkmFor($this->owner());

        $this->actingAs($admin)
            ->post(route('admin.umkms.media.store', [$umkm, 'banner']), [
                'file_banner' => UploadedFile::fake()->image('banner.png'),
            ])->assertRedirect()->assertSessionHas('status');

        $media = $umkm->media()->where('collection', 'banner')->first();
        $this->assertNotNull($media);
        $this->assertSame('banner', $media->collection);
        Storage::disk('public')->assertExists($media->path);
    }

    public function test_administrator_can_upload_gallery_multiple(): void
    {
        $admin = $this->administrator();
        $umkm = $this->umkmFor($this->owner());

        $this->actingAs($admin)
            ->post(route('admin.umkms.media.store', [$umkm, 'gallery']), [
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

    public function test_administrator_can_replace_logo(): void
    {
        $admin = $this->administrator();
        $umkm = $this->umkmFor($this->owner());

        $this->actingAs($admin)
            ->post(route('admin.umkms.media.store', [$umkm, 'logo']), [
                'file_logo' => UploadedFile::fake()->image('logo-1.png'),
            ]);

        $old = $umkm->media()->where('collection', 'logo')->first();
        Storage::disk('public')->assertExists($old->path);

        $this->actingAs($admin)
            ->post(route('admin.umkms.media.store', [$umkm, 'logo']), [
                'file_logo' => UploadedFile::fake()->image('logo-2.png'),
            ])->assertSessionHas('status');

        $this->assertDatabaseCount('media', 1);
        $new = $umkm->media()->where('collection', 'logo')->first();
        $this->assertNotSame($old->id, $new->id);
        Storage::disk('public')->assertExists($new->path);
        Storage::disk('public')->assertMissing($old->path);
    }

    public function test_administrator_can_replace_banner(): void
    {
        $admin = $this->administrator();
        $umkm = $this->umkmFor($this->owner());

        $this->actingAs($admin)
            ->post(route('admin.umkms.media.store', [$umkm, 'banner']), [
                'file_banner' => UploadedFile::fake()->image('banner-1.png'),
            ]);

        $old = $umkm->media()->where('collection', 'banner')->first();

        $this->actingAs($admin)
            ->post(route('admin.umkms.media.store', [$umkm, 'banner']), [
                'file_banner' => UploadedFile::fake()->image('banner-2.png'),
            ])->assertSessionHas('status');

        $this->assertDatabaseCount('media', 1);
        $new = $umkm->media()->where('collection', 'banner')->first();
        $this->assertNotSame($old->id, $new->id);
        Storage::disk('public')->assertExists($new->path);
        Storage::disk('public')->assertMissing($old->path);
    }

    public function test_administrator_can_delete_media(): void
    {
        $admin = $this->administrator();
        $umkm = $this->umkmFor($this->owner());
        $media = $umkm->media()->create([
            'disk' => 'public',
            'path' => 'umkms/'.$umkm->id.'/logo.png',
            'collection' => 'logo',
            'sort_order' => 0,
        ]);
        Storage::disk('public')->put($media->path, 'content');

        $this->actingAs($admin)
            ->delete(route('admin.media.destroy', $media))
            ->assertRedirect()->assertSessionHas('status');

        $this->assertDatabaseMissing('media', ['id' => $media->id]);
        Storage::disk('public')->assertMissing($media->path);
    }

    public function test_media_record_uses_correct_polymorphic_target(): void
    {
        $admin = $this->administrator();
        $umkm = $this->umkmFor($this->owner());

        $this->actingAs($admin)
            ->post(route('admin.umkms.media.store', [$umkm, 'logo']), [
                'file_logo' => UploadedFile::fake()->image('logo.png'),
            ]);

        $media = Media::first();
        $this->assertSame(Umkm::class, $media->mediable_type);
        $this->assertSame($umkm->id, $media->mediable_id);
        $this->assertSame('public', $media->disk);
        $this->assertStringStartsWith('umkms/'.$umkm->id.'/', $media->path);
    }

    public function test_administrator_can_manage_media_of_approved_umkm(): void
    {
        $admin = $this->administrator();
        $umkm = $this->umkmFor($this->owner(), 'approved');

        $this->actingAs($admin)
            ->post(route('admin.umkms.media.store', [$umkm, 'logo']), [
                'file_logo' => UploadedFile::fake()->image('logo.png'),
            ])->assertSessionHas('status');

        $logo = $umkm->media()->where('collection', 'logo')->first();
        $this->assertNotNull($logo);

        $this->actingAs($admin)
            ->post(route('admin.umkms.media.store', [$umkm, 'logo']), [
                'file_logo' => UploadedFile::fake()->image('logo-2.png'),
            ])->assertSessionHas('status');

        $this->assertDatabaseCount('media', 1);
        Storage::disk('public')->assertMissing($logo->path);

        $newLogo = $umkm->media()->where('collection', 'logo')->first();

        $this->actingAs($admin)
            ->delete(route('admin.media.destroy', $newLogo))
            ->assertSessionHas('status');

        $this->assertDatabaseCount('media', 0);
        Storage::disk('public')->assertMissing($newLogo->path);
    }

    public function test_other_owner_cannot_access_admin_media_routes(): void
    {
        $umkm = $this->umkmFor($this->owner());
        $other = $this->owner();

        $this->actingAs($other)
            ->post(route('admin.umkms.media.store', [$umkm, 'logo']), [
                'file_logo' => UploadedFile::fake()->image('logo.png'),
            ])->assertForbidden();

        $this->assertDatabaseCount('media', 0);

        $media = $umkm->media()->create([
            'disk' => 'public',
            'path' => 'umkms/'.$umkm->id.'/banner.png',
            'collection' => 'banner',
            'sort_order' => 0,
        ]);
        Storage::disk('public')->put($media->path, 'content');

        $this->actingAs($other)
            ->delete(route('admin.media.destroy', $media))
            ->assertForbidden();

        $this->assertDatabaseHas('media', ['id' => $media->id]);
        Storage::disk('public')->assertExists($media->path);
    }

    public function test_guest_cannot_access_admin_media_routes(): void
    {
        $umkm = $this->umkmFor($this->owner());

        $this->post(route('admin.umkms.media.store', [$umkm, 'logo']), [
            'file_logo' => UploadedFile::fake()->image('logo.png'),
        ])->assertRedirect(route('login'));

        $media = $umkm->media()->create([
            'disk' => 'public',
            'path' => 'umkms/'.$umkm->id.'/logo.png',
            'collection' => 'logo',
            'sort_order' => 0,
        ]);

        $this->delete(route('admin.media.destroy', $media))
            ->assertRedirect(route('login'));

        $this->assertDatabaseCount('media', 1);
    }

    public function test_owner_existing_flow_still_works(): void
    {
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner);

        $this->actingAs($owner)
            ->post(route('owner.umkm.media.store', [$umkm, 'logo']), [
                'file_logo' => UploadedFile::fake()->image('logo.png'),
            ])->assertRedirect()->assertSessionHas('status');

        $this->assertDatabaseCount('media', 1);
    }

    public function test_owner_cannot_upload_to_approved_umkm(): void
    {
        $owner = $this->owner();
        $approvedUmkm = $this->umkmFor($owner, 'approved');

        $this->actingAs($owner)
            ->post(route('owner.umkm.media.store', [$approvedUmkm, 'banner']), [
                'file_banner' => UploadedFile::fake()->image('banner.png'),
            ])->assertRedirect()->assertSessionHas('error');

        $this->assertDatabaseCount('media', 0);
    }

    public function test_invalid_file_is_rejected(): void
    {
        $admin = $this->administrator();
        $umkm = $this->umkmFor($this->owner());

        $this->actingAs($admin)
            ->post(route('admin.umkms.media.store', [$umkm, 'logo']), [
                'file_logo' => UploadedFile::fake()->create('dokumen.pdf', 10, 'application/pdf'),
            ])->assertSessionHasErrors('file_logo');

        $this->assertDatabaseCount('media', 0);
        $this->assertCount(0, Storage::disk('public')->allFiles());
    }

    public function test_oversized_file_is_rejected(): void
    {
        $admin = $this->administrator();
        $umkm = $this->umkmFor($this->owner());

        $this->actingAs($admin)
            ->post(route('admin.umkms.media.store', [$umkm, 'logo']), [
                'file_logo' => UploadedFile::fake()->image('besar.png')->size(3000),
            ])->assertSessionHasErrors('file_logo');

        $this->assertDatabaseCount('media', 0);
        $this->assertCount(0, Storage::disk('public')->allFiles());
    }

    public function test_gallery_limit_is_enforced(): void
    {
        $admin = $this->administrator();
        $umkm = $this->umkmFor($this->owner());

        $this->actingAs($admin)
            ->post(route('admin.umkms.media.store', [$umkm, 'gallery']), [
                'gallery' => [
                    UploadedFile::fake()->image('1.png'),
                    UploadedFile::fake()->image('2.png'),
                    UploadedFile::fake()->image('3.png'),
                    UploadedFile::fake()->image('4.png'),
                    UploadedFile::fake()->image('5.png'),
                    UploadedFile::fake()->image('6.png'),
                ],
            ])->assertSessionHasErrors('gallery');

        $this->assertDatabaseCount('media', 0);
        $this->assertCount(0, Storage::disk('public')->allFiles());
    }

    public function test_invalid_collection_is_rejected(): void
    {
        $admin = $this->administrator();
        $umkm = $this->umkmFor($this->owner());

        $this->actingAs($admin)
            ->post(route('admin.umkms.media.store', [$umkm, 'hero']), [
                'file_hero' => UploadedFile::fake()->image('hero.png'),
            ])->assertNotFound();

        $this->assertDatabaseCount('media', 0);
    }

    public function test_administrator_can_delete_product_media(): void
    {
        $admin = $this->administrator();
        $umkm = $this->umkmFor($this->owner(), 'approved');
        $product = Product::create([
            'umkm_id' => $umkm->id,
            'category_id' => Category::firstOrCreate(['type' => 'product', 'name' => 'Makanan', 'slug' => 'makanan'])->id,
            'name' => 'Kopi Arabika',
            'slug' => Product::generateUniqueSlug('Kopi Arabika'),
            'price' => 15000,
            'status' => 'approved',
        ]);
        $media = $product->media()->create([
            'disk' => 'public',
            'path' => 'products/'.$product->id.'/foto.png',
            'collection' => 'product',
            'sort_order' => 0,
        ]);
        Storage::disk('public')->put($media->path, 'content');

        $this->actingAs($admin)
            ->delete(route('admin.media.destroy', $media))
            ->assertRedirect()
            ->assertSessionHas('status');

        $this->assertDatabaseMissing('media', ['id' => $media->id]);
        Storage::disk('public')->assertMissing($media->path);
    }

    public function test_activity_log_is_recorded_for_upload_replace_and_delete(): void
    {
        $admin = $this->administrator();
        $umkm = $this->umkmFor($this->owner());

        $this->actingAs($admin)
            ->post(route('admin.umkms.media.store', [$umkm, 'logo']), [
                'file_logo' => UploadedFile::fake()->image('logo-1.png'),
            ]);

        $this->assertDatabaseHas('activity_log', [
            'event' => 'media_uploaded',
            'causer_id' => $admin->id,
            'subject_id' => $umkm->id,
            'causer_type' => User::class,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.umkms.media.store', [$umkm, 'logo']), [
                'file_logo' => UploadedFile::fake()->image('logo-2.png'),
            ]);

        $this->assertDatabaseHas('activity_log', [
            'event' => 'media_replaced',
            'causer_id' => $admin->id,
            'subject_id' => $umkm->id,
        ]);

        $logo = $umkm->media()->where('collection', 'logo')->first();

        $this->actingAs($admin)
            ->delete(route('admin.media.destroy', $logo));

        $this->assertDatabaseHas('activity_log', [
            'event' => 'media_deleted',
            'causer_id' => $admin->id,
            'subject_id' => $umkm->id,
        ]);
    }

    public function test_no_orphan_media_or_files_after_replace_and_delete(): void
    {
        $admin = $this->administrator();
        $umkm = $this->umkmFor($this->owner());

        $this->actingAs($admin)
            ->post(route('admin.umkms.media.store', [$umkm, 'banner']), [
                'file_banner' => UploadedFile::fake()->image('banner-1.png'),
            ]);

        $old = $umkm->media()->where('collection', 'banner')->first();

        $this->actingAs($admin)
            ->post(route('admin.umkms.media.store', [$umkm, 'banner']), [
                'file_banner' => UploadedFile::fake()->image('banner-2.png'),
            ]);

        $this->assertDatabaseCount('media', 1);
        $this->assertCount(1, Storage::disk('public')->allFiles());
        Storage::disk('public')->assertMissing($old->path);

        $banner = $umkm->media()->where('collection', 'banner')->first();

        $this->actingAs($admin)
            ->delete(route('admin.media.destroy', $banner));

        $this->assertDatabaseCount('media', 0);
        $this->assertCount(0, Storage::disk('public')->allFiles());
    }

    public function test_failed_upload_keeps_existing_logo_intact(): void
    {
        $admin = $this->administrator();
        $umkm = $this->umkmFor($this->owner());

        $this->actingAs($admin)
            ->post(route('admin.umkms.media.store', [$umkm, 'logo']), [
                'file_logo' => UploadedFile::fake()->image('logo-1.png'),
            ]);

        $old = $umkm->media()->where('collection', 'logo')->first();

        $this->actingAs($admin)
            ->post(route('admin.umkms.media.store', [$umkm, 'logo']), [
                'file_logo' => UploadedFile::fake()->create('gambar.svg', 10, 'image/svg+xml'),
            ])->assertSessionHasErrors('file_logo');

        $this->assertDatabaseCount('media', 1);
        $this->assertSame($old->id, $umkm->media()->where('collection', 'logo')->first()->id);
        Storage::disk('public')->assertExists($old->path);
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
                'category_id' => Category::firstOrCreate(['type' => 'umkm', 'name' => 'Kerajinan', 'slug' => 'kerajinan'])->id,
                'name' => 'Kopi Senja',
            ])->assertRedirect()->assertSessionHas('status');

        $this->assertDatabaseHas('umkms', [
            'name' => 'Kopi Senja',
            'user_id' => $owner->id,
        ]);
    }
}