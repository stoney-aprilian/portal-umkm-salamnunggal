<?php

namespace Tests\Feature\CoreBusiness;

use App\Models\Category;
use App\Models\Product;
use App\Models\Umkm;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use LogicException;
use Tests\TestCase;

class ProductTest extends TestCase
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

    private function umkm(): Umkm
    {
        return Umkm::firstOrCreate(
            ['slug' => 'warung-nasi-bu-siti'],
            [
                'user_id' => User::factory()->create()->id,
                'category_id' => $this->umkmCategory()->id,
                'name' => 'Warung Nasi Bu Siti',
            ],
        );
    }

    public function test_migration_creates_expected_table_and_columns(): void
    {
        $this->assertTrue(Schema::hasTable('products'));

        $columns = ['id', 'umkm_id', 'category_id', 'name', 'slug', 'description', 'price', 'status', 'created_at', 'updated_at'];

        foreach ($columns as $column) {
            $this->assertTrue(Schema::hasColumn('products', $column), "missing column: {$column}");
        }

        $this->assertFalse(Schema::hasColumn('products', 'image'));
        $this->assertFalse(Schema::hasColumn('products', 'logo'));
        $this->assertFalse(Schema::hasColumn('products', 'banner'));
        $this->assertFalse(Schema::hasColumn('products', 'media_id'));
        $this->assertFalse(Schema::hasColumn('products', 'verification_id'));
        $this->assertFalse(Schema::hasColumn('products', 'stock'));
        $this->assertFalse(Schema::hasColumn('products', 'quantity'));
    }

    public function test_foreign_keys_to_umkms_and_categories_exist(): void
    {
        $foreignKeys = Schema::getForeignKeys('products');

        $referencedTables = array_map(fn ($fk) => $fk['foreign_table'], $foreignKeys);

        $this->assertContains('umkms', $referencedTables);
        $this->assertContains('categories', $referencedTables);
    }

    public function test_valid_product_can_be_created_for_umkm(): void
    {
        $umkm = $this->umkm();
        $category = $this->productCategory();

        $product = Product::create([
            'umkm_id' => $umkm->id,
            'category_id' => $category->id,
            'name' => 'Nasi Uduk Komplit',
            'slug' => 'nasi-uduk-komplit',
            'price' => 15000,
        ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'umkm_id' => $umkm->id,
            'category_id' => $category->id,
            'name' => 'Nasi Uduk Komplit',
            'slug' => 'nasi-uduk-komplit',
            'price' => 15000.00,
        ]);
    }

    public function test_product_belongs_to_correct_umkm(): void
    {
        $umkm = $this->umkm();

        $product = Product::create([
            'umkm_id' => $umkm->id,
            'category_id' => $this->productCategory()->id,
            'name' => 'Es Teh Manis',
            'slug' => 'es-teh-manis',
            'price' => 5000,
        ]);

        $this->assertSame($umkm->id, $product->umkm->id);
        $this->assertSame($umkm->name, $product->umkm->name);
    }

    public function test_product_belongs_to_correct_category(): void
    {
        $category = $this->productCategory();

        $product = Product::create([
            'umkm_id' => $this->umkm()->id,
            'category_id' => $category->id,
            'name' => 'Kopi Arabika',
            'slug' => 'kopi-arabika',
            'price' => 25000,
        ]);

        $this->assertSame($category->id, $product->category->id);
        $this->assertSame('product', $product->category->type);
    }

    public function test_multiple_products_can_belong_to_one_umkm(): void
    {
        $umkm = $this->umkm();
        $category = $this->productCategory();

        Product::create(['umkm_id' => $umkm->id, 'category_id' => $category->id, 'name' => 'Produk Satu', 'slug' => 'produk-satu', 'price' => 10000]);
        Product::create(['umkm_id' => $umkm->id, 'category_id' => $category->id, 'name' => 'Produk Dua', 'slug' => 'produk-dua', 'price' => 20000]);

        $this->assertDatabaseCount('products', 2);
    }

    public function test_umkm_category_is_rejected_for_product(): void
    {
        $this->assertThrows(
            fn () => Product::create([
                'umkm_id' => $this->umkm()->id,
                'category_id' => $this->umkmCategory()->id,
                'name' => 'Produk Kategori Salah',
                'slug' => 'produk-kategori-salah',
                'price' => 10000,
            ]),
            LogicException::class,
        );

        $this->assertDatabaseCount('products', 0);
    }

    public function test_all_documented_status_values_are_valid(): void
    {
        $statuses = ['draft', 'pending', 'approved', 'needs_revision', 'rejected'];

        foreach ($statuses as $status) {
            Product::create([
                'umkm_id' => $this->umkm()->id,
                'category_id' => $this->productCategory()->id,
                'name' => "Produk {$status}",
                'slug' => "produk-{$status}",
                'price' => 10000,
                'status' => $status,
            ]);
        }

        foreach ($statuses as $status) {
            $this->assertDatabaseHas('products', ['status' => $status]);
        }
    }

    public function test_status_defaults_to_draft(): void
    {
        Product::create([
            'umkm_id' => $this->umkm()->id,
            'category_id' => $this->productCategory()->id,
            'name' => 'Produk Tanpa Status',
            'slug' => 'produk-tanpa-status',
            'price' => 10000,
        ]);

        $fresh = Product::where('slug', 'produk-tanpa-status')->first();

        $this->assertSame('draft', $fresh->status);
    }

    public function test_invalid_status_is_rejected(): void
    {
        $this->assertThrows(
            fn () => DB::table('products')->insert([
                'umkm_id' => $this->umkm()->id,
                'category_id' => $this->productCategory()->id,
                'name' => 'Produk Invalid',
                'slug' => 'produk-invalid',
                'price' => 10000,
                'status' => 'hidden',
            ]),
            QueryException::class,
        );

        $this->assertDatabaseCount('products', 0);
    }

    public function test_description_is_optional(): void
    {
        Product::create([
            'umkm_id' => $this->umkm()->id,
            'category_id' => $this->productCategory()->id,
            'name' => 'Produk Minimal',
            'slug' => 'produk-minimal',
            'price' => 10000,
        ]);

        $this->assertNull(Product::where('slug', 'produk-minimal')->first()->description);
    }

    public function test_price_is_required_and_stored_as_decimal(): void
    {
        $product = Product::create([
            'umkm_id' => $this->umkm()->id,
            'category_id' => $this->productCategory()->id,
            'name' => 'Produk Mahal',
            'slug' => 'produk-mahal',
            'price' => 99999.99,
        ]);

        $this->assertSame(99999.99, (float) $product->fresh()->price);

        $this->assertThrows(
            fn () => DB::table('products')->insert([
                'umkm_id' => $this->umkm()->id,
                'category_id' => $this->productCategory()->id,
                'name' => 'Produk Tanpa Harga',
                'slug' => 'produk-tanpa-harga',
                'price' => null,
            ]),
            QueryException::class,
        );
    }

    public function test_deleting_umkm_with_products_is_blocked(): void
    {
        $umkm = $this->umkm();

        Product::create([
            'umkm_id' => $umkm->id,
            'category_id' => $this->productCategory()->id,
            'name' => 'Produk Terikat',
            'slug' => 'produk-terikat',
            'price' => 10000,
        ]);

        $this->assertThrows(
            fn () => $umkm->delete(),
            QueryException::class,
        );

        $this->assertDatabaseHas('products', ['name' => 'Produk Terikat']);
    }

    public function test_deleting_category_with_products_is_blocked(): void
    {
        $category = $this->productCategory();

        Product::create([
            'umkm_id' => $this->umkm()->id,
            'category_id' => $category->id,
            'name' => 'Produk Terikat Kategori',
            'slug' => 'produk-terikat-kategori',
            'price' => 10000,
        ]);

        $this->assertThrows(
            fn () => $category->delete(),
            QueryException::class,
        );

        $this->assertDatabaseHas('products', ['name' => 'Produk Terikat Kategori']);
    }

    public function test_duplicate_slug_is_rejected(): void
    {
        $umkm = $this->umkm();
        $category = $this->productCategory();

        Product::create([
            'umkm_id' => $umkm->id,
            'category_id' => $category->id,
            'name' => 'Nasi Uduk Komplit',
            'slug' => 'nasi-uduk-komplit',
            'price' => 15000,
        ]);

        $this->assertThrows(
            fn () => Product::create([
                'umkm_id' => $umkm->id,
                'category_id' => $category->id,
                'name' => 'Nasi Uduk Komplit 2',
                'slug' => 'nasi-uduk-komplit',
                'price' => 15000,
            ]),
            QueryException::class,
        );

        $this->assertDatabaseCount('products', 1);
    }
}
