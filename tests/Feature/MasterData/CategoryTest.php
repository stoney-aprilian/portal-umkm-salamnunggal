<?php

namespace Tests\Feature\MasterData;

use App\Models\Category;
use Database\Seeders\CategorySeeder;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_migration_creates_expected_table_and_columns(): void
    {
        $this->assertTrue(Schema::hasTable('categories'));

        $columns = ['id', 'type', 'name', 'slug', 'description', 'created_at', 'updated_at'];

        foreach ($columns as $column) {
            $this->assertTrue(Schema::hasColumn('categories', $column), "missing column: {$column}");
        }

        $this->assertFalse(Schema::hasColumn('categories', 'status'));
        $this->assertFalse(Schema::hasColumn('categories', 'sort_order'));
        $this->assertFalse(Schema::hasColumn('categories', 'is_active'));
    }

    public function test_valid_umkm_category_can_be_created(): void
    {
        $category = Category::create([
            'type' => 'umkm',
            'name' => 'Kuliner',
            'slug' => 'kuliner',
        ]);

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'type' => 'umkm',
            'name' => 'Kuliner',
            'slug' => 'kuliner',
        ]);
    }

    public function test_valid_product_category_can_be_created(): void
    {
        $category = Category::create([
            'type' => 'product',
            'name' => 'Makanan',
            'slug' => 'makanan',
        ]);

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'type' => 'product',
            'name' => 'Makanan',
            'slug' => 'makanan',
        ]);
    }

    public function test_description_is_optional(): void
    {
        Category::create([
            'type' => 'umkm',
            'name' => 'Tanpa Deskripsi',
            'slug' => 'tanpa-deskripsi',
        ]);

        $this->assertNull(Category::where('slug', 'tanpa-deskripsi')->first()->description);
    }

    public function test_invalid_category_type_is_rejected(): void
    {
        $this->assertThrows(
            fn () => DB::table('categories')->insert([
                'type' => 'food',
                'name' => 'Invalid',
                'slug' => 'invalid',
            ]),
            QueryException::class,
        );

        $this->assertDatabaseCount('categories', 0);
    }

    public function test_seeder_creates_expected_records(): void
    {
        $this->seed(CategorySeeder::class);

        $this->assertDatabaseCount('categories', 2);
        $this->assertDatabaseHas('categories', ['type' => 'umkm', 'name' => 'Kuliner', 'slug' => 'kuliner']);
        $this->assertDatabaseHas('categories', ['type' => 'product', 'name' => 'Makanan', 'slug' => 'makanan']);
    }

    public function test_seeder_is_idempotent(): void
    {
        $this->seed(CategorySeeder::class);
        $this->seed(CategorySeeder::class);

        $this->assertDatabaseCount('categories', 2);
    }

    public function test_duplicate_slug_is_rejected(): void
    {
        Category::create([
            'type' => 'umkm',
            'name' => 'Kuliner',
            'slug' => 'kuliner',
        ]);

        $this->assertThrows(
            fn () => Category::create([
                'type' => 'product',
                'name' => 'Kuliner Baru',
                'slug' => 'kuliner',
            ]),
            QueryException::class,
        );

        $this->assertDatabaseCount('categories', 1);
    }
}
