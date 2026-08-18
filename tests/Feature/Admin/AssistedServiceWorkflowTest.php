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
use Spatie\Activitylog\Models\Activity;
use Tests\TestCase;

/**
 * One real assisted service scenario: an owner without an email address
 * is registered and served entirely by the administrator. The owner is
 * never required to log in; the administrator creates the UMKM, uploads
 * all media, creates the product with its photo, and everything stays
 * owned by the owner and visible on the public portal.
 */
class AssistedServiceWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
    }

    public function test_assisted_service_workflow_end_to_end(): void
    {
        // 1. Admin membuat Owner tanpa email.
        $this->seed(DatabaseSeeder::class);

        $admin = User::factory()->create(['status' => 'approved']);
        $admin->assignRole('administrator');

        $this->actingAs($admin)
            ->post(route('admin.users.store'), [
                'name' => 'Ibu Siti Purnama',
                'email' => '',
                'phone' => '0812-3456-7890',
                'password' => 'rahasia123',
                'password_confirmation' => 'rahasia123',
            ])->assertRedirect()->assertSessionHas('status');

        $owner = User::where('name', 'Ibu Siti Purnama')->firstOrFail();
        $this->assertNull($owner->email);
        $this->assertTrue($owner->hasRole('owner'));

        // 2. Admin membuat UMKM atas nama owner tersebut.
        $umkmCategory = Category::firstOrCreate(
            ['type' => 'umkm', 'name' => 'Kerajinan'],
            ['slug' => 'kerajinan'],
        );
        $productCategory = Category::firstOrCreate(
            ['type' => 'product', 'name' => 'Kerajinan Tangan'],
            ['slug' => 'kerajinan-tangan'],
        );

        $this->actingAs($admin)
            ->post(route('admin.umkms.store'), [
                'owner_id' => $owner->id,
                'category_id' => $umkmCategory->id,
                'name' => 'Tenun Ibu Siti',
                'description' => 'Tenun khas Salamnunggal.',
                'address' => 'Kp. Salamnunggal RT 01 RW 02, Tasikmalaya',
                'google_maps' => 'https://maps.app.goo.gl/tenun123',
                'phone' => '0812-3456-7890',
                'email' => '',
                'operational_hours' => '08.00 - 16.00',
            ])->assertRedirect()->assertSessionHas('status');

        $umkm = Umkm::where('name', 'Tenun Ibu Siti')->firstOrFail();
        $this->assertSame('approved', $umkm->status);
        $this->assertSame($owner->id, $umkm->user_id);

        // 3-5. Admin mengunggah logo, banner, dan galeri.
        $this->actingAs($admin)
            ->post(route('admin.umkms.media.store', [$umkm, 'logo']), [
                'file_logo' => UploadedFile::fake()->image('logo.png'),
            ])->assertSessionHas('status');

        $this->actingAs($admin)
            ->post(route('admin.umkms.media.store', [$umkm, 'banner']), [
                'file_banner' => UploadedFile::fake()->image('banner.png'),
            ])->assertSessionHas('status');

        $this->actingAs($admin)
            ->post(route('admin.umkms.media.store', [$umkm, 'gallery']), [
                'gallery' => [
                    UploadedFile::fake()->image('galeri-1.png'),
                    UploadedFile::fake()->image('galeri-2.png'),
                ],
            ])->assertSessionHas('status');

        $logo = $umkm->media()->where('collection', 'logo')->firstOrFail();
        $banner = $umkm->media()->where('collection', 'banner')->firstOrFail();
        $gallery = $umkm->media()->where('collection', 'gallery')->orderBy('sort_order')->get();
        $this->assertCount(2, $gallery);

        // 6. Admin membuat produk untuk UMKM tersebut.
        $this->actingAs($admin)
            ->post(route('admin.products.store'), [
                'umkm_id' => $umkm->id,
                'category_id' => $productCategory->id,
                'name' => 'Sarung Tenun',
                'description' => 'Sarung tenun tangan.',
                'price' => 150000,
            ])->assertRedirect()->assertSessionHas('status');

        $product = Product::where('name', 'Sarung Tenun')->firstOrFail();
        $this->assertSame('approved', $product->status);
        $this->assertSame($umkm->id, $product->umkm_id);

        // 7. Admin mengunggah foto produk.
        $this->actingAs($admin)
            ->post(route('admin.products.media.store', [$product, 'product']), [
                'file_product' => UploadedFile::fake()->image('foto-produk.png'),
            ])->assertSessionHas('status');

        $productPhoto = $product->media()->where('collection', 'product')->firstOrFail();

        // 8. Ownership seluruh entity benar.
        $this->assertSame($owner->id, $umkm->fresh()->user_id);
        $this->assertSame($umkm->id, $product->fresh()->umkm_id);
        $this->assertNotSame($admin->id, $umkm->user_id);

        foreach ([$logo, $banner, $productPhoto] as $media) {
            $this->assertSame('public', $media->disk);
            $this->assertSame('media', $media->getTable());
        }

        $this->assertSame(Umkm::class, $logo->mediable_type);
        $this->assertSame($umkm->id, $logo->mediable_id);
        $this->assertSame(Umkm::class, $banner->mediable_type);
        $this->assertSame($umkm->id, $banner->mediable_id);
        $this->assertSame(Product::class, $productPhoto->mediable_type);
        $this->assertSame($product->id, $productPhoto->mediable_id);

        // 9. Data dan file media tersimpan.
        $this->assertDatabaseHas('umkms', ['id' => $umkm->id, 'user_id' => $owner->id]);
        $this->assertDatabaseHas('products', ['id' => $product->id, 'umkm_id' => $umkm->id]);
        $this->assertSame(5, Media::count());
        foreach ([$logo, $banner, $productPhoto] as $media) {
            Storage::disk('public')->assertExists($media->path);
        }
        foreach ($gallery as $item) {
            Storage::disk('public')->assertExists($item->path);
        }

        // 10. UMKM dan produk tampil di portal publik.
        $this->get(route('public.umkm.show', $umkm))
            ->assertOk()
            ->assertSee('Tenun Ibu Siti')
            ->assertSee('Sarung Tenun');

        $this->get(route('public.product.show', $product))
            ->assertOk()
            ->assertSee('Sarung Tenun');

        $this->get(route('public.umkm.index'))
            ->assertOk()
            ->assertSee('Tenun Ibu Siti');

        $this->get(route('public.product.index'))
            ->assertOk()
            ->assertSee('Sarung Tenun');

        // 11. Owner tanpa email tidak pernah diperlukan untuk login.
        $this->post(route('logout'))
            ->assertRedirect();

        $this->assertGuest();

        $this->post('/login', [
            'password' => 'rahasia123',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
        $this->assertNull($owner->fresh()->email);

        // 12. Administrator tercatat sebagai actor pada activity log.
        foreach (['user_created', 'umkm_created', 'product_created'] as $event) {
            $this->assertDatabaseHas('activity_log', [
                'event' => $event,
                'causer_id' => $admin->id,
            ]);
        }

        $this->assertSame(1, Activity::where('event', 'umkm_created')->where('causer_id', $admin->id)->count());
        $this->assertSame(1, Activity::where('event', 'product_created')->where('causer_id', $admin->id)->count());
        $this->assertSame(3, Activity::where('event', 'media_uploaded')->where('causer_id', $admin->id)->where('subject_id', $umkm->id)->count());
        $this->assertSame(1, Activity::where('event', 'product_media_uploaded')->where('causer_id', $admin->id)->where('subject_id', $product->id)->count());
    }

    public function test_admin_created_owner_is_approved_and_not_stuck_in_self_service_verification(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::factory()->create(['status' => 'approved']);
        $admin->assignRole('administrator');

        $this->actingAs($admin)
            ->post(route('admin.users.store'), [
                'name' => 'Pak Jajang',
                'email' => '',
                'phone' => '0812-1111-2222',
                'password' => 'rahasia123',
                'password_confirmation' => 'rahasia123',
            ])->assertRedirect()->assertSessionHas('status');

        $owner = User::where('name', 'Pak Jajang')->firstOrFail();

        $this->assertSame('approved', $owner->fresh()->status);
        $this->assertNull($owner->email);
        $this->assertSame(0, $owner->verificationRequests()->count());
        $this->assertDatabaseMissing('verification_requests', [
            'verifiable_type' => User::class,
            'verifiable_id' => $owner->id,
        ]);
    }

    public function test_admin_created_owner_with_email_can_login_and_use_dashboard_without_verification(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::factory()->create(['status' => 'approved']);
        $admin->assignRole('administrator');

        $this->actingAs($admin)
            ->post(route('admin.users.store'), [
                'name' => 'Bu Yati',
                'email' => 'yati@example.com',
                'phone' => '0812-3333-4444',
                'password' => 'rahasia123',
                'password_confirmation' => 'rahasia123',
            ])->assertRedirect()->assertSessionHas('status');

        $this->post('/login', [
            'email' => 'yati@example.com',
            'password' => 'rahasia123',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticated();
    }

    public function test_umkm_detail_shows_products_and_create_link_prefills_umkm(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::factory()->create(['status' => 'approved']);
        $admin->assignRole('administrator');

        $owner = User::factory()->create(['status' => 'approved']);
        $owner->assignRole('owner');

        $umkm = Umkm::create([
            'user_id' => $owner->id,
            'category_id' => Category::firstOrCreate(['type' => 'umkm', 'name' => 'Kerajinan', 'slug' => 'kerajinan'])->id,
            'name' => 'Tenun Ibu Siti',
            'slug' => 'tenun-ibu-siti',
            'status' => 'approved',
        ]);

        $product = Product::create([
            'umkm_id' => $umkm->id,
            'category_id' => Category::firstOrCreate(['type' => 'product', 'name' => 'Kerajinan Tangan', 'slug' => 'kerajinan-tangan'])->id,
            'name' => 'Sarung Tenun',
            'slug' => 'sarung-tenun',
            'price' => 150000,
            'status' => 'approved',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.umkms.show', $umkm))
            ->assertOk()
            ->assertSee('Produk UMKM ini')
            ->assertSee('Sarung Tenun')
            ->assertSee(route('admin.products.show', $product));

        $response = $this->actingAs($admin)
            ->get(route('admin.products.create', ['umkm' => $umkm->id]))
            ->assertOk()
            ->assertSee('UMKM telah dipilih dari halaman Kelola UMKM');

        $this->assertStringContainsString(
            '<option value="'.$umkm->id.'" selected',
            $response->getContent(),
        );
    }

    public function test_owner_detail_links_to_umkm_and_create_prefills_owner(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::factory()->create(['status' => 'approved']);
        $admin->assignRole('administrator');

        $owner = User::factory()->create(['status' => 'approved']);
        $owner->assignRole('owner');

        $umkm = Umkm::create([
            'user_id' => $owner->id,
            'category_id' => Category::firstOrCreate(['type' => 'umkm', 'name' => 'Kerajinan', 'slug' => 'kerajinan'])->id,
            'name' => 'Tenun Ibu Siti',
            'slug' => 'tenun-ibu-siti',
            'status' => 'approved',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.users.show', $owner))
            ->assertOk()
            ->assertSee('Kelola UMKM')
            ->assertSee(route('admin.umkms.show', $umkm));

        $newOwner = User::factory()->create(['status' => 'approved']);
        $newOwner->assignRole('owner');

        $this->actingAs($admin)
            ->get(route('admin.users.show', $newOwner))
            ->assertOk()
            ->assertSee('Buat UMKM atas nama Owner ini')
            ->assertSee(route('admin.umkms.create', ['owner' => $newOwner->id]));

        $response = $this->actingAs($admin)
            ->get(route('admin.umkms.create', ['owner' => $newOwner->id]))
            ->assertOk()
            ->assertSee('Owner telah dipilih dari halaman Kelola Pengguna');

        $this->assertStringContainsString(
            '<option value="'.$newOwner->id.'" selected',
            $response->getContent(),
        );
    }
}
