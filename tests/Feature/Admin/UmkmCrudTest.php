<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Media;
use App\Models\Product;
use App\Models\Umkm;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\Models\Activity;
use Tests\TestCase;

class UmkmCrudTest extends TestCase
{
    use RefreshDatabase;

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

    private function umkmCategory(): Category
    {
        return Category::firstOrCreate(
            ['type' => 'umkm', 'name' => 'Kerajinan'],
            ['slug' => 'kerajinan'],
        );
    }

    private function productCategory(): Category
    {
        return Category::firstOrCreate(
            ['type' => 'product', 'name' => 'Kerajinan Tangan'],
            ['slug' => 'kerajinan-tangan'],
        );
    }

    private function payload(User $owner, array $overrides = []): array
    {
        return array_merge([
            'owner_id' => $owner->id,
            'category_id' => $this->umkmCategory()->id,
            'name' => 'Warung Kopi Salamnunggal',
            'description' => 'Kopi robusta lokal dengan suasana pedesaan.',
            'address' => 'Jl. Raya Salamnunggal No. 12, Tasikmalaya',
            'google_maps' => 'https://maps.app.goo.gl/abcd1234',
            'phone' => '0812-3456-7890',
            'email' => 'warung@example.com',
            'website' => 'https://warungkopi.example.com',
            'instagram' => '@warungkopi.salamnunggal',
            'facebook' => 'warungkopi.salamnunggal',
            'tiktok' => '@warungkopi.salamnunggal',
            'operational_hours' => '08.00 - 17.00',
        ], $overrides);
    }

    public function test_administrator_can_view_umkm_list(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $otherOwner = $this->owner();
        $category = $this->umkmCategory();

        Umkm::create([
            'user_id' => $owner->id,
            'category_id' => $category->id,
            'name' => 'Kopi Senja',
            'slug' => 'kopi-senja',
            'status' => 'approved',
        ]);
        Umkm::create([
            'user_id' => $otherOwner->id,
            'category_id' => $category->id,
            'name' => 'Bakso Mas Asep',
            'slug' => 'bakso-mas-asep',
            'status' => 'pending',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.umkms.index'))
            ->assertOk()
            ->assertSee('Kelola UMKM')
            ->assertSee('Kopi Senja')
            ->assertSee('Bakso Mas Asep')
            ->assertSee($owner->name)
            ->assertSee('Kerajinan');
    }

    public function test_guest_cannot_access_umkm_admin_routes(): void
    {
        $this->get(route('admin.umkms.index'))
            ->assertRedirect(route('login'));

        $this->post(route('admin.umkms.store'), [
            'owner_id' => 1,
            'name' => 'UMKM Baru',
        ])->assertRedirect(route('login'));
    }

    public function test_owner_cannot_access_umkm_admin_routes(): void
    {
        $owner = $this->owner();
        $category = $this->umkmCategory();

        $umkm = Umkm::create([
            'user_id' => $owner->id,
            'category_id' => $category->id,
            'name' => 'Kopi Senja',
            'slug' => 'kopi-senja',
            'status' => 'approved',
        ]);

        $this->actingAs($owner)
            ->get(route('admin.umkms.index'))
            ->assertForbidden();

        $this->actingAs($owner)
            ->get(route('admin.umkms.create'))
            ->assertForbidden();

        $this->actingAs($owner)
            ->post(route('admin.umkms.store'), $this->payload($owner))
            ->assertForbidden();

        $this->actingAs($owner)
            ->put(route('admin.umkms.update', $umkm), $this->payload($owner))
            ->assertForbidden();

        $this->actingAs($owner)
            ->delete(route('admin.umkms.destroy', $umkm))
            ->assertForbidden();
    }

    public function test_administrator_can_create_umkm_on_behalf_of_owner(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();

        $this->actingAs($admin)
            ->post(route('admin.umkms.store'), $this->payload($owner))
            ->assertRedirect()
            ->assertSessionHas('status');

        $this->assertDatabaseHas('umkms', [
            'name' => 'Warung Kopi Salamnunggal',
            'status' => 'approved',
        ]);
    }

    public function test_created_umkm_belongs_to_owner_not_administrator(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();

        $this->actingAs($admin)
            ->post(route('admin.umkms.store'), $this->payload($owner));

        $umkm = Umkm::firstOrFail();

        $this->assertSame($owner->id, $umkm->user_id);
        $this->assertNotSame($admin->id, $umkm->user_id);
        $this->assertDatabaseHas('umkms', [
            'id' => $umkm->id,
            'user_id' => $owner->id,
        ]);
    }

    public function test_product_category_is_rejected(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $productCategory = $this->productCategory();

        $this->actingAs($admin)
            ->post(route('admin.umkms.store'), $this->payload($owner, [
                'category_id' => $productCategory->id,
            ]))
            ->assertSessionHasErrors('category_id');

        $this->assertDatabaseCount('umkms', 0);
    }

    public function test_umkm_category_is_required(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();

        $this->actingAs($admin)
            ->post(route('admin.umkms.store'), $this->payload($owner, [
                'category_id' => 99999,
            ]))
            ->assertSessionHasErrors('category_id');

        $this->assertDatabaseCount('umkms', 0);
    }

    public function test_all_umkm_fields_are_saved(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();

        $this->actingAs($admin)
            ->post(route('admin.umkms.store'), $this->payload($owner))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('umkms', [
            'name' => 'Warung Kopi Salamnunggal',
            'slug' => 'warung-kopi-salamnunggal',
            'description' => 'Kopi robusta lokal dengan suasana pedesaan.',
            'address' => 'Jl. Raya Salamnunggal No. 12, Tasikmalaya',
            'google_maps' => 'https://maps.app.goo.gl/abcd1234',
            'phone' => '0812-3456-7890',
            'email' => 'warung@example.com',
            'website' => 'https://warungkopi.example.com',
            'instagram' => '@warungkopi.salamnunggal',
            'facebook' => 'warungkopi.salamnunggal',
            'tiktok' => '@warungkopi.salamnunggal',
            'operational_hours' => '08.00 - 17.00',
        ]);
    }

    public function test_google_maps_is_saved_and_validated(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();

        $this->actingAs($admin)
            ->post(route('admin.umkms.store'), $this->payload($owner, [
                'google_maps' => 'https://maps.app.goo.gl/xyz987',
            ]))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('umkms', [
            'google_maps' => 'https://maps.app.goo.gl/xyz987',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.umkms.store'), $this->payload($owner, [
                'google_maps' => 'bukan-url',
            ]))
            ->assertSessionHasErrors('google_maps');
    }

    public function test_administrator_can_edit_umkm(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $category = $this->umkmCategory();

        $umkm = Umkm::create([
            'user_id' => $owner->id,
            'category_id' => $category->id,
            'name' => 'Kopi Senja',
            'slug' => 'kopi-senja',
            'status' => 'approved',
        ]);

        $this->actingAs($admin)
            ->put(route('admin.umkms.update', $umkm), $this->payload($owner, [
                'name' => 'Kopi Senja Baru',
                'google_maps' => 'https://maps.app.goo.gl/new123',
                'operational_hours' => '09.00 - 18.00',
            ]))
            ->assertRedirect()
            ->assertSessionHas('status');

        $this->assertDatabaseHas('umkms', [
            'id' => $umkm->id,
            'name' => 'Kopi Senja Baru',
            'google_maps' => 'https://maps.app.goo.gl/new123',
            'operational_hours' => '09.00 - 18.00',
        ]);
    }

    public function test_ownership_is_preserved_after_edit(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $category = $this->umkmCategory();

        $umkm = Umkm::create([
            'user_id' => $owner->id,
            'category_id' => $category->id,
            'name' => 'Kopi Senja',
            'slug' => 'kopi-senja',
            'status' => 'approved',
        ]);

        $this->actingAs($admin)
            ->put(route('admin.umkms.update', $umkm), $this->payload($owner, [
                'name' => 'Kopi Senja Diperbarui',
            ]))
            ->assertSessionHasNoErrors();

        $this->assertSame($owner->id, $umkm->fresh()->user_id);
    }

    public function test_administrator_can_transfer_umkm_to_another_owner(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $otherOwner = $this->owner();
        $category = $this->umkmCategory();

        $umkm = Umkm::create([
            'user_id' => $owner->id,
            'category_id' => $category->id,
            'name' => 'Kopi Senja',
            'slug' => 'kopi-senja',
            'status' => 'approved',
        ]);

        $this->actingAs($admin)
            ->put(route('admin.umkms.update', $umkm), $this->payload($otherOwner))
            ->assertSessionHasNoErrors();

        $this->assertSame($otherOwner->id, $umkm->fresh()->user_id);
    }

    public function test_owner_with_existing_umkm_is_rejected(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $category = $this->umkmCategory();

        Umkm::create([
            'user_id' => $owner->id,
            'category_id' => $category->id,
            'name' => 'Kopi Senja',
            'slug' => 'kopi-senja',
            'status' => 'approved',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.umkms.store'), $this->payload($owner))
            ->assertSessionHasErrors('owner_id');

        $this->assertDatabaseCount('umkms', 1);
    }

    public function test_slug_stays_unique_after_edit(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $otherOwner = $this->owner();
        $category = $this->umkmCategory();

        $first = Umkm::create([
            'user_id' => $owner->id,
            'category_id' => $category->id,
            'name' => 'Warung Kopi',
            'slug' => 'warung-kopi',
            'status' => 'approved',
        ]);
        $second = Umkm::create([
            'user_id' => $otherOwner->id,
            'category_id' => $category->id,
            'name' => 'Warung Kopi',
            'slug' => 'warung-kopi-2',
            'status' => 'approved',
        ]);

        $this->actingAs($admin)
            ->put(route('admin.umkms.update', $first), $this->payload($owner, [
                'name' => 'Kopi Nusantara',
            ]))
            ->assertSessionHasNoErrors();

        $this->assertSame('kopi-nusantara', $first->fresh()->slug);

        $this->actingAs($admin)
            ->put(route('admin.umkms.update', $second), $this->payload($otherOwner, [
                'name' => 'Kopi Nusantara',
            ]))
            ->assertSessionHasNoErrors();

        $this->assertSame('kopi-nusantara-2', $second->fresh()->slug);
    }

    public function test_administrator_can_delete_umkm(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $category = $this->umkmCategory();

        $umkm = Umkm::create([
            'user_id' => $owner->id,
            'category_id' => $category->id,
            'name' => 'Kopi Senja',
            'slug' => 'kopi-senja',
            'status' => 'approved',
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.umkms.destroy', $umkm))
            ->assertRedirect(route('admin.umkms.index'))
            ->assertSessionHas('status');

        $this->assertDatabaseMissing('umkms', ['id' => $umkm->id]);
    }

    public function test_delete_removes_products_media_and_verification_requests(): void
    {
        Storage::fake('public');

        $admin = $this->administrator();
        $owner = $this->owner();
        $category = $this->umkmCategory();
        $productCategory = $this->productCategory();

        $umkm = Umkm::create([
            'user_id' => $owner->id,
            'category_id' => $category->id,
            'name' => 'Kopi Senja',
            'slug' => 'kopi-senja',
            'status' => 'approved',
        ]);

        $product = Product::create([
            'umkm_id' => $umkm->id,
            'category_id' => $productCategory->id,
            'name' => 'Kopi Robusta 250gr',
            'slug' => 'kopi-robusta-250gr',
            'price' => 25000,
            'status' => 'approved',
        ]);

        $umkmLogo = $umkm->media()->create([
            'disk' => 'public',
            'path' => 'umkms/'.$umkm->id.'/logo/logo.jpg',
            'collection' => 'logo',
            'sort_order' => 0,
        ]);
        Storage::disk('public')->put($umkmLogo->path, 'logo-content');

        $productPhoto = $product->media()->create([
            'disk' => 'public',
            'path' => 'products/'.$product->id.'/foto.jpg',
            'collection' => 'product',
            'sort_order' => 0,
        ]);
        Storage::disk('public')->put($productPhoto->path, 'photo-content');

        $umkm->verificationRequests()->create([
            'user_id' => $owner->id,
            'status' => 'approved',
            'reviewed_at' => now(),
        ]);
        $product->verificationRequests()->create([
            'user_id' => $owner->id,
            'status' => 'pending',
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.umkms.destroy', $umkm))
            ->assertRedirect(route('admin.umkms.index'));

        $this->assertDatabaseMissing('umkms', ['id' => $umkm->id]);
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
        $this->assertDatabaseCount('media', 0);
        $this->assertDatabaseCount('verification_requests', 0);
        Storage::disk('public')->assertMissing($umkmLogo->path);
        Storage::disk('public')->assertMissing($productPhoto->path);
    }

    public function test_activity_log_is_recorded_for_create_update_and_delete(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();

        $this->actingAs($admin)
            ->post(route('admin.umkms.store'), $this->payload($owner));

        $umkm = Umkm::firstOrFail();

        $this->assertDatabaseHas('activity_log', [
            'event' => 'umkm_created',
            'causer_id' => $admin->id,
            'subject_id' => $umkm->id,
        ]);

        $this->actingAs($admin)
            ->put(route('admin.umkms.update', $umkm), $this->payload($owner, [
                'name' => 'Warung Kopi Update',
            ]));

        $this->assertDatabaseHas('activity_log', [
            'event' => 'umkm_updated',
            'causer_id' => $admin->id,
            'subject_id' => $umkm->id,
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.umkms.destroy', $umkm));

        $this->assertDatabaseHas('activity_log', [
            'event' => 'umkm_deleted',
            'causer_id' => $admin->id,
            'subject_id' => $umkm->id,
        ]);

        $this->assertSame(3, Activity::where('subject_id', $umkm->id)->count());
    }

    public function test_owner_flow_still_works(): void
    {
        $this->seed(DatabaseSeeder::class);

        $owner = User::factory()->create(['status' => 'approved']);
        $owner->assignRole('owner');
        $category = $this->umkmCategory();

        $this->actingAs($owner)
            ->post(route('owner.umkm.store'), [
                'category_id' => $category->id,
                'name' => 'Kopi Milik Owner',
            ])
            ->assertRedirect(route('dashboard'));

        $umkm = $owner->umkm()->firstOrFail();
        $this->assertSame('draft', $umkm->status);
        $this->assertSame($owner->id, $umkm->user_id);

        $this->actingAs($owner)
            ->post(route('owner.umkm.submit', $umkm))
            ->assertRedirect(route('dashboard'));

        $this->assertSame('pending', $umkm->fresh()->status);
    }

    public function test_admin_verification_flow_still_works(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::factory()->create(['status' => 'approved']);
        $admin->assignRole('administrator');

        $owner = User::factory()->create(['status' => 'approved']);
        $owner->assignRole('owner');
        $category = $this->umkmCategory();

        $umkm = Umkm::create([
            'user_id' => $owner->id,
            'category_id' => $category->id,
            'name' => 'Kopi Senja',
            'slug' => 'kopi-senja',
            'status' => 'pending',
        ]);
        $request = $umkm->verificationRequests()->create([
            'user_id' => $owner->id,
            'status' => 'pending',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.umkm.verification.approve', $request))
            ->assertRedirect();

        $this->assertSame('approved', $umkm->fresh()->status);
        $this->assertSame('approved', $request->fresh()->status);
    }

    public function test_admin_created_approved_umkm_is_visible_on_public(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();

        $this->actingAs($admin)
            ->post(route('admin.umkms.store'), $this->payload($owner))
            ->assertSessionHasNoErrors();

        $umkm = Umkm::firstOrFail();

        $this->get(route('public.umkm.show', $umkm))
            ->assertOk()
            ->assertSee('Warung Kopi Salamnunggal');

        $this->get(route('public.umkm.index'))
            ->assertOk()
            ->assertSee('Warung Kopi Salamnunggal');
    }

    public function test_non_approved_umkm_is_not_visible_on_public(): void
    {
        $this->seed(DatabaseSeeder::class);

        $owner = User::factory()->create(['status' => 'approved']);
        $owner->assignRole('owner');
        $category = $this->umkmCategory();

        $draft = Umkm::create([
            'user_id' => $owner->id,
            'category_id' => $category->id,
            'name' => 'Draft Rahasia',
            'slug' => 'draft-rahasia',
            'status' => 'draft',
        ]);

        $this->get(route('public.umkm.show', $draft))
            ->assertNotFound();

        $this->get(route('public.umkm.index'))
            ->assertOk()
            ->assertDontSee('Draft Rahasia');
    }
}