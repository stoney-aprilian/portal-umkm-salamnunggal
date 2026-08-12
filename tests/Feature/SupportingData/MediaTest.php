<?php

namespace Tests\Feature\SupportingData;

use App\Models\Category;
use App\Models\Media;
use App\Models\Product;
use App\Models\Umkm;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class MediaTest extends TestCase
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

    private function product(): Product
    {
        return Product::create([
            'umkm_id' => $this->umkm()->id,
            'category_id' => $this->productCategory()->id,
            'name' => 'Nasi Uduk Komplit',
            'slug' => 'nasi-uduk-komplit',
            'price' => 15000,
        ]);
    }

    private function makeMedia(array $attributes = []): Media
    {
        return Media::create(array_merge([
            'disk' => 'public',
            'path' => 'umkms/1/logo.png',
            'collection' => 'logo',
        ], $attributes));
    }

    public function test_migration_creates_expected_table_and_columns(): void
    {
        $this->assertTrue(Schema::hasTable('media'));

        $columns = ['id', 'disk', 'path', 'collection', 'mediable_type', 'mediable_id', 'sort_order', 'created_at', 'updated_at'];

        foreach ($columns as $column) {
            $this->assertTrue(Schema::hasColumn('media', $column), "missing column: {$column}");
        }
    }

    public function test_polymorphic_columns_are_indexed(): void
    {
        $this->assertTrue(Schema::hasIndex('media', 'media_mediable_type_mediable_id_index'));
    }

    public function test_no_direct_entity_foreign_key_columns_exist(): void
    {
        $this->assertFalse(Schema::hasColumn('media', 'umkm_id'));
        $this->assertFalse(Schema::hasColumn('media', 'product_id'));
    }

    public function test_no_unrelated_image_metadata_columns_were_invented(): void
    {
        $columns = ['filename', 'mime_type', 'size', 'width', 'height', 'alt_text', 'caption', 'url', 'user_id'];

        foreach ($columns as $column) {
            $this->assertFalse(Schema::hasColumn('media', $column), "unexpected column: {$column}");
        }
    }

    public function test_media_can_belong_to_an_umkm(): void
    {
        $umkm = $this->umkm();

        $media = $umkm->media()->create([
            'disk' => 'public',
            'path' => "umkms/{$umkm->id}/logo.png",
            'collection' => 'logo',
        ]);

        $this->assertDatabaseHas('media', [
            'id' => $media->id,
            'mediable_type' => Umkm::class,
            'mediable_id' => $umkm->id,
        ]);
    }

    public function test_media_can_belong_to_a_product(): void
    {
        $product = $this->product();

        $media = $product->media()->create([
            'disk' => 'public',
            'path' => "products/{$product->id}/foto.png",
            'collection' => 'product',
        ]);

        $this->assertDatabaseHas('media', [
            'id' => $media->id,
            'mediable_type' => Product::class,
            'mediable_id' => $product->id,
        ]);
    }

    public function test_mediable_resolves_to_the_correct_umkm(): void
    {
        $umkm = $this->umkm();
        $media = $this->makeMedia([
            'mediable_type' => Umkm::class,
            'mediable_id' => $umkm->id,
        ]);

        $this->assertInstanceOf(Umkm::class, $media->mediable);
        $this->assertSame($umkm->id, $media->mediable->id);
    }

    public function test_mediable_resolves_to_the_correct_product(): void
    {
        $product = $this->product();
        $media = $this->makeMedia([
            'mediable_type' => Product::class,
            'mediable_id' => $product->id,
        ]);

        $this->assertInstanceOf(Product::class, $media->mediable);
        $this->assertSame($product->id, $media->mediable->id);
    }

    public function test_umkm_media_relation_returns_its_media(): void
    {
        $umkm = $this->umkm();

        $logo = $this->makeMedia(['mediable_type' => Umkm::class, 'mediable_id' => $umkm->id, 'collection' => 'logo']);
        $banner = $this->makeMedia(['mediable_type' => Umkm::class, 'mediable_id' => $umkm->id, 'collection' => 'banner']);

        $this->assertCount(2, $umkm->media);
        $this->assertTrue($umkm->media->contains($logo));
        $this->assertTrue($umkm->media->contains($banner));
    }

    public function test_product_media_relation_returns_its_media(): void
    {
        $product = $this->product();

        $media = $this->makeMedia(['mediable_type' => Product::class, 'mediable_id' => $product->id, 'collection' => 'product']);

        $this->assertCount(1, $product->media);
        $this->assertTrue($product->media->contains($media));
    }

    public function test_all_documented_collection_values_can_be_stored(): void
    {
        $umkm = $this->umkm();
        $collections = ['logo', 'banner', 'gallery', 'product'];

        foreach ($collections as $collection) {
            $this->makeMedia([
                'mediable_type' => Umkm::class,
                'mediable_id' => $umkm->id,
                'collection' => $collection,
            ]);
        }

        foreach ($collections as $collection) {
            $this->assertDatabaseHas('media', ['collection' => $collection]);
        }
    }

    public function test_sort_order_defaults_to_zero(): void
    {
        $umkm = $this->umkm();

        $media = $this->makeMedia([
            'mediable_type' => Umkm::class,
            'mediable_id' => $umkm->id,
        ]);

        $this->assertSame(0, $media->fresh()->sort_order);
    }

    public function test_sort_order_can_be_stored(): void
    {
        $umkm = $this->umkm();

        $media = $this->makeMedia([
            'mediable_type' => Umkm::class,
            'mediable_id' => $umkm->id,
            'collection' => 'gallery',
            'sort_order' => 3,
        ]);

        $this->assertSame(3, $media->fresh()->sort_order);
    }

    public function test_media_requires_an_attached_owner(): void
    {
        $this->assertThrows(
            fn () => $this->makeMedia(),
            QueryException::class,
        );

        $this->assertDatabaseCount('media', 0);
    }
}
