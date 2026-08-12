<?php

namespace Tests\Feature\CoreBusiness;

use App\Models\Category;
use App\Models\Umkm;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use LogicException;
use Tests\TestCase;

class UmkmTest extends TestCase
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
        return User::factory()->create();
    }

    public function test_migration_creates_expected_table_and_columns(): void
    {
        $this->assertTrue(Schema::hasTable('umkms'));

        $columns = [
            'id',
            'user_id',
            'category_id',
            'name',
            'slug',
            'description',
            'address',
            'google_maps',
            'phone',
            'email',
            'website',
            'instagram',
            'facebook',
            'tiktok',
            'operational_hours',
            'status',
            'created_at',
            'updated_at',
        ];

        foreach ($columns as $column) {
            $this->assertTrue(Schema::hasColumn('umkms', $column), "missing column: {$column}");
        }

        $this->assertFalse(Schema::hasColumn('umkms', 'logo'));
        $this->assertFalse(Schema::hasColumn('umkms', 'banner'));
        $this->assertFalse(Schema::hasColumn('umkms', 'logo_path'));
        $this->assertFalse(Schema::hasColumn('umkms', 'banner_path'));
    }

    public function test_foreign_keys_to_users_and_categories_exist(): void
    {
        $foreignKeys = Schema::getForeignKeys('umkms');

        $referencedTables = array_map(fn ($fk) => $fk['foreign_table'], $foreignKeys);

        $this->assertContains('users', $referencedTables);
        $this->assertContains('categories', $referencedTables);
    }

    public function test_user_id_is_unique(): void
    {
        $user = $this->owner();
        $category = $this->umkmCategory();

        Umkm::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'name' => 'UMKM Pertama',
            'slug' => 'umkm-pertama',
        ]);

        $this->assertThrows(
            fn () => Umkm::create([
                'user_id' => $user->id,
                'category_id' => $category->id,
                'name' => 'UMKM Kedua',
                'slug' => 'umkm-kedua',
            ]),
            QueryException::class,
        );

        $this->assertDatabaseCount('umkms', 1);
    }

    public function test_owner_can_create_umkm(): void
    {
        $user = $this->owner();
        $category = $this->umkmCategory();

        $umkm = Umkm::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'name' => 'Warung Nasi Bu Siti',
            'slug' => 'warung-nasi-bu-siti',
        ]);

        $this->assertDatabaseHas('umkms', [
            'id' => $umkm->id,
            'user_id' => $user->id,
            'category_id' => $category->id,
            'name' => 'Warung Nasi Bu Siti',
            'slug' => 'warung-nasi-bu-siti',
        ]);
    }

    public function test_umkm_belongs_to_correct_user(): void
    {
        $user = $this->owner();

        $umkm = Umkm::create([
            'user_id' => $user->id,
            'category_id' => $this->umkmCategory()->id,
            'name' => 'Toko Sembako Jaya',
            'slug' => 'toko-sembako-jaya',
        ]);

        $this->assertSame($user->id, $umkm->user->id);
    }

    public function test_umkm_belongs_to_correct_category(): void
    {
        $category = $this->umkmCategory();

        $umkm = Umkm::create([
            'user_id' => $this->owner()->id,
            'category_id' => $category->id,
            'name' => 'Toko Sembako Jaya',
            'slug' => 'toko-sembako-jaya',
        ]);

        $this->assertSame($category->id, $umkm->category->id);
        $this->assertSame('umkm', $umkm->category->type);
    }

    public function test_administrator_does_not_own_an_umkm(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $admin = User::factory()->create();
        $admin->assignRole('administrator');

        $this->assertDatabaseCount('umkms', 0);
        $this->assertFalse(Umkm::where('user_id', $admin->id)->exists());
    }

    public function test_all_documented_status_values_are_valid(): void
    {
        $statuses = ['draft', 'pending', 'approved', 'needs_revision', 'rejected'];

        foreach ($statuses as $status) {
            Umkm::create([
                'user_id' => $this->owner()->id,
                'category_id' => $this->umkmCategory()->id,
                'name' => "UMKM {$status}",
                'slug' => "umkm-{$status}",
                'status' => $status,
            ]);
        }

        foreach ($statuses as $status) {
            $this->assertDatabaseHas('umkms', ['status' => $status]);
        }
    }

    public function test_status_defaults_to_draft(): void
    {
        $umkm = Umkm::create([
            'user_id' => $this->owner()->id,
            'category_id' => $this->umkmCategory()->id,
            'name' => 'UMKM Tanpa Status',
            'slug' => 'umkm-tanpa-status',
        ]);

        $fresh = Umkm::where('slug', 'umkm-tanpa-status')->first();

        $this->assertSame('draft', $fresh->status);
    }

    public function test_invalid_status_is_rejected(): void
    {
        $this->assertThrows(
            fn () => DB::table('umkms')->insert([
                'user_id' => $this->owner()->id,
                'category_id' => $this->umkmCategory()->id,
                'name' => 'UMKM Invalid',
                'slug' => 'umkm-invalid',
                'status' => 'active',
            ]),
            QueryException::class,
        );

        $this->assertDatabaseCount('umkms', 0);
    }

    public function test_product_category_is_rejected_for_umkm(): void
    {
        $this->assertThrows(
            fn () => Umkm::create([
                'user_id' => $this->owner()->id,
                'category_id' => $this->productCategory()->id,
                'name' => 'UMKM Kategori Salah',
                'slug' => 'umkm-kategori-salah',
            ]),
            LogicException::class,
        );

        $this->assertDatabaseCount('umkms', 0);
    }

    public function test_optional_fields_are_nullable(): void
    {
        $umkm = Umkm::create([
            'user_id' => $this->owner()->id,
            'category_id' => $this->umkmCategory()->id,
            'name' => 'UMKM Minimal',
            'slug' => 'umkm-minimal',
        ]);

        $fresh = $umkm->fresh();

        $this->assertNull($fresh->description);
        $this->assertNull($fresh->address);
        $this->assertNull($fresh->google_maps);
        $this->assertNull($fresh->phone);
        $this->assertNull($fresh->email);
        $this->assertNull($fresh->website);
        $this->assertNull($fresh->instagram);
        $this->assertNull($fresh->facebook);
        $this->assertNull($fresh->tiktok);
        $this->assertNull($fresh->operational_hours);
    }

    public function test_deleting_owner_with_umkm_is_blocked(): void
    {
        $user = $this->owner();

        Umkm::create([
            'user_id' => $user->id,
            'category_id' => $this->umkmCategory()->id,
            'name' => 'UMKM Terikat',
            'slug' => 'umkm-terikat',
        ]);

        $this->assertThrows(
            fn () => $user->delete(),
            QueryException::class,
        );

        $this->assertDatabaseHas('umkms', ['name' => 'UMKM Terikat']);
    }

    public function test_deleting_category_with_umkm_is_blocked(): void
    {
        $category = $this->umkmCategory();

        Umkm::create([
            'user_id' => $this->owner()->id,
            'category_id' => $category->id,
            'name' => 'UMKM Terikat Kategori',
            'slug' => 'umkm-terikat-kategori',
        ]);

        $this->assertThrows(
            fn () => $category->delete(),
            QueryException::class,
        );

        $this->assertDatabaseHas('umkms', ['name' => 'UMKM Terikat Kategori']);
    }

    public function test_duplicate_slug_is_rejected(): void
    {
        $category = $this->umkmCategory();

        Umkm::create([
            'user_id' => $this->owner()->id,
            'category_id' => $category->id,
            'name' => 'Warung Nasi Bu Siti',
            'slug' => 'warung-nasi-bu-siti',
        ]);

        $this->assertThrows(
            fn () => Umkm::create([
                'user_id' => $this->owner()->id,
                'category_id' => $category->id,
                'name' => 'Warung Nasi Lainnya',
                'slug' => 'warung-nasi-bu-siti',
            ]),
            QueryException::class,
        );

        $this->assertDatabaseCount('umkms', 1);
    }
}
