<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Umkm;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use LogicException;
use Tests\TestCase;

class CategoryManagementTest extends TestCase
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
        return Category::create([
            'type' => 'umkm',
            'name' => 'Kerajinan',
            'slug' => 'kerajinan',
        ]);
    }

    private function productCategory(): Category
    {
        return Category::create([
            'type' => 'product',
            'name' => 'Kerajinan Tangan',
            'slug' => 'kerajinan-tangan',
        ]);
    }

    public function test_administrator_can_view_categories(): void
    {
        $admin = $this->administrator();

        $this->actingAs($admin)
            ->get(route('admin.categories.index'))
            ->assertOk()
            ->assertSee('Kategori UMKM')
            ->assertSee('Kategori Produk')
            ->assertSee('Kuliner')
            ->assertSee('Makanan');
    }

    public function test_guest_cannot_access_categories(): void
    {
        $this->get(route('admin.categories.index'))
            ->assertRedirect(route('login'));

        $this->post(route('admin.categories.store', 'umkm'), [
            'name' => 'Kategori Baru',
        ])->assertRedirect(route('login'));
    }

    public function test_owner_cannot_access_categories(): void
    {
        $owner = $this->owner();

        $this->actingAs($owner)
            ->get(route('admin.categories.index'))
            ->assertForbidden();

        $this->actingAs($owner)
            ->post(route('admin.categories.store', 'umkm'), [
                'name' => 'Kategori Baru',
            ])->assertForbidden();
    }

    public function test_administrator_can_create_umkm_category(): void
    {
        $admin = $this->administrator();

        $this->actingAs($admin)
            ->post(route('admin.categories.store', 'umkm'), [
                'name' => 'Fashion',
                'description' => 'Pakaian dan aksesoris.',
            ])
            ->assertRedirect(route('admin.categories.index'))
            ->assertSessionHas('status');

        $this->assertDatabaseHas('categories', [
            'type' => 'umkm',
            'name' => 'Fashion',
            'slug' => 'fashion',
            'description' => 'Pakaian dan aksesoris.',
        ]);
    }

    public function test_administrator_can_create_product_category(): void
    {
        $admin = $this->administrator();

        $this->actingAs($admin)
            ->post(route('admin.categories.store', 'product'), [
                'name' => 'Minuman',
            ])
            ->assertRedirect(route('admin.categories.index'))
            ->assertSessionHas('status');

        $this->assertDatabaseHas('categories', [
            'type' => 'product',
            'name' => 'Minuman',
            'slug' => 'minuman',
        ]);
    }

    public function test_duplicate_name_within_same_type_is_rejected(): void
    {
        $admin = $this->administrator();

        $this->actingAs($admin)
            ->post(route('admin.categories.store', 'umkm'), [
                'name' => 'Kuliner',
            ])
            ->assertSessionHasErrors('name');

        $this->assertSame(1, Category::where('type', 'umkm')->where('name', 'Kuliner')->count());
    }

    public function test_same_name_across_different_types_is_allowed_with_suffixed_slug(): void
    {
        $admin = $this->administrator();

        $this->actingAs($admin)
            ->post(route('admin.categories.store', 'product'), [
                'name' => 'Kuliner',
            ])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('categories', [
            'type' => 'product',
            'name' => 'Kuliner',
            'slug' => 'kuliner-2',
        ]);
    }

    public function test_invalid_type_route_is_rejected(): void
    {
        $admin = $this->administrator();

        $this->actingAs($admin)
            ->post(route('admin.categories.store', 'food'), [
                'name' => 'Kategori Aneh',
            ])
            ->assertMethodNotAllowed();

        $this->actingAs($admin)
            ->get(route('admin.categories.create', 'food'))
            ->assertNotFound();
    }

    public function test_administrator_can_edit_category_and_slug_is_regenerated(): void
    {
        $admin = $this->administrator();
        $category = $this->umkmCategory();

        $this->actingAs($admin)
            ->put(route('admin.categories.update', $category), [
                'name' => 'Kerajinan Kayu',
                'description' => 'Produk kayu olahan.',
            ])
            ->assertRedirect(route('admin.categories.index'))
            ->assertSessionHas('status');

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'type' => 'umkm',
            'name' => 'Kerajinan Kayu',
            'slug' => 'kerajinan-kayu',
            'description' => 'Produk kayu olahan.',
        ]);
    }

    public function test_type_cannot_be_manipulated_through_update(): void
    {
        $admin = $this->administrator();
        $category = $this->umkmCategory();

        $this->actingAs($admin)
            ->put(route('admin.categories.update', $category), [
                'name' => 'Kerajinan',
                'type' => 'product',
            ])
            ->assertSessionHasNoErrors();

        $this->assertSame('umkm', $category->fresh()->type);
    }

    public function test_umkm_category_cannot_be_used_as_product_category(): void
    {
        $this->seed(DatabaseSeeder::class);

        $owner = User::factory()->create(['status' => 'approved']);
        $owner->assignRole('owner');

        $umkmCategory = Category::where('type', 'umkm')->first();
        $umkm = Umkm::create([
            'user_id' => $owner->id,
            'category_id' => $umkmCategory->id,
            'name' => 'Warung Uji',
            'slug' => Umkm::generateUniqueSlug('Warung Uji'),
            'status' => 'approved',
        ]);

        $this->expectException(LogicException::class);

        $umkm->products()->create([
            'category_id' => $umkmCategory->id,
            'name' => 'Produk Salah',
            'slug' => 'produk-salah',
            'price' => 10000,
            'status' => 'draft',
        ]);
    }

    public function test_product_category_cannot_be_used_as_umkm_category(): void
    {
        $this->seed(DatabaseSeeder::class);

        $owner = User::factory()->create(['status' => 'approved']);
        $owner->assignRole('owner');

        $productCategory = Category::where('type', 'product')->first();

        $this->expectException(LogicException::class);

        Umkm::create([
            'user_id' => $owner->id,
            'category_id' => $productCategory->id,
            'name' => 'UMKM Salah',
            'slug' => Umkm::generateUniqueSlug('UMKM Salah'),
            'status' => 'draft',
        ]);
    }

    public function test_used_umkm_category_cannot_be_deleted(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $category = $this->umkmCategory();

        $umkm = Umkm::create([
            'user_id' => $owner->id,
            'category_id' => $category->id,
            'name' => 'Warung Kerajinan',
            'slug' => Umkm::generateUniqueSlug('Warung Kerajinan'),
            'status' => 'draft',
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.categories.destroy', $category))
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertDatabaseHas('categories', ['id' => $category->id]);
        $this->assertDatabaseHas('umkms', ['id' => $umkm->id, 'category_id' => $category->id]);
    }

    public function test_used_product_category_cannot_be_deleted(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::factory()->create(['status' => 'approved']);
        $admin->assignRole('administrator');

        $owner = User::factory()->create(['status' => 'approved']);
        $owner->assignRole('owner');

        $productCategory = Category::where('type', 'product')->first();

        $umkm = Umkm::create([
            'user_id' => $owner->id,
            'category_id' => Category::where('type', 'umkm')->first()->id,
            'name' => 'Warung Uji',
            'slug' => Umkm::generateUniqueSlug('Warung Uji'),
            'status' => 'approved',
        ]);

        $product = $umkm->products()->create([
            'category_id' => $productCategory->id,
            'name' => 'Produk Uji',
            'slug' => 'produk-uji',
            'price' => 15000,
            'status' => 'draft',
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.categories.destroy', $productCategory))
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertDatabaseHas('categories', ['id' => $productCategory->id]);
        $this->assertDatabaseHas('products', ['id' => $product->id, 'category_id' => $productCategory->id]);
    }

    public function test_unused_category_can_be_deleted(): void
    {
        $admin = $this->administrator();
        $category = $this->umkmCategory();

        $this->actingAs($admin)
            ->delete(route('admin.categories.destroy', $category))
            ->assertRedirect(route('admin.categories.index'))
            ->assertSessionHas('status');

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_category_actions_are_recorded_in_activity_log(): void
    {
        $admin = $this->administrator();
        $category = $this->umkmCategory();

        $this->actingAs($admin)
            ->put(route('admin.categories.update', $category), [
                'name' => 'Kerajinan Kayu',
            ]);

        $this->actingAs($admin)
            ->delete(route('admin.categories.destroy', $category));

        $this->assertDatabaseHas('activity_log', [
            'event' => 'category_updated',
            'subject_type' => Category::class,
            'subject_id' => $category->id,
            'causer_id' => $admin->id,
        ]);

        $this->assertDatabaseHas('activity_log', [
            'event' => 'category_deleted',
            'subject_type' => Category::class,
            'subject_id' => $category->id,
            'causer_id' => $admin->id,
        ]);
    }

    public function test_owner_self_service_flow_still_works_with_umkm_category(): void
    {
        $owner = $this->owner();
        $umkmCategory = Category::where('type', 'umkm')->first();

        $this->actingAs($owner)
            ->post(route('owner.umkm.store'), [
                'name' => 'Warung Baru',
                'category_id' => $umkmCategory->id,
            ])
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('status');

        $this->assertDatabaseHas('umkms', [
            'name' => 'Warung Baru',
            'category_id' => $umkmCategory->id,
        ]);
    }

    public function test_owner_flow_rejects_product_category_for_umkm(): void
    {
        $owner = $this->owner();
        $productCategory = Category::where('type', 'product')->first();

        $this->actingAs($owner)
            ->post(route('owner.umkm.store'), [
                'name' => 'Warung Salah',
                'category_id' => $productCategory->id,
            ])
            ->assertSessionHasErrors('category_id');

        $this->assertDatabaseMissing('umkms', ['name' => 'Warung Salah']);
    }

    public function test_admin_dashboard_remains_accessible(): void
    {
        $admin = $this->administrator();

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk();
    }
}