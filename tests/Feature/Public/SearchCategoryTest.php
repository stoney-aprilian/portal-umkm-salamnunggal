<?php

namespace Tests\Feature\Public;

use App\Models\Category;
use App\Models\Product;
use App\Models\Umkm;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class SearchCategoryTest extends TestCase
{
    use RefreshDatabase;

    private function umkmCategory(?string $name = null, ?string $description = null): Category
    {
        return Category::create([
            'type' => 'umkm',
            'name' => $name ?? 'Kuliner',
            'slug' => $name ? Str::slug($name) : 'kuliner',
            'description' => $description,
        ]);
    }

    private function productCategory(?string $name = null): Category
    {
        return Category::create([
            'type' => 'product',
            'name' => $name ?? 'Makanan',
            'slug' => $name ? Str::slug($name) : 'makanan',
        ]);
    }

    private function owner(): User
    {
        $this->seed(DatabaseSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('owner');

        return $user;
    }

    private function umkmFor(Category $category, string $status = 'approved', ?string $name = null): Umkm
    {
        $name = $name ?? 'Warung Maju';

        return Umkm::create([
            'user_id' => $this->owner()->id,
            'category_id' => $category->id,
            'name' => $name,
            'slug' => Umkm::generateUniqueSlug($name),
            'status' => $status,
        ]);
    }

    private function productFor(Category $category, Umkm $umkm, string $status = 'approved', ?string $name = null): Product
    {
        $name = $name ?? 'Kopi Arabika';

        return Product::create([
            'umkm_id' => $umkm->id,
            'category_id' => $category->id,
            'name' => $name,
            'slug' => Product::generateUniqueSlug($name),
            'description' => 'Kopi asli Gunung Papandayan.',
            'price' => 15000,
            'status' => $status,
        ]);
    }

    public function test_search_shows_matching_umkm_category(): void
    {
        $category = $this->umkmCategory('Kuliner');
        $this->umkmFor($category);

        $this->get(route('public.search', ['q' => 'Kuliner']))
            ->assertOk()
            ->assertSee('Kategori')
            ->assertSee(route('public.category.umkm', $category));
    }

    public function test_search_shows_matching_product_category(): void
    {
        $category = $this->productCategory('Minuman');
        $umkm = $this->umkmFor($this->umkmCategory());
        $this->productFor($category, $umkm);

        $this->get(route('public.search', ['q' => 'Minuman']))
            ->assertOk()
            ->assertSee('Kategori')
            ->assertSee(route('public.category.product', $category));
    }

    public function test_search_hides_category_without_approved_data(): void
    {
        $category = $this->umkmCategory('Kuliner');
        $this->umkmFor($category, 'draft', 'Warung Draft');
        $this->umkmFor($category, 'pending', 'Warung Pending');
        $this->umkmFor($category, 'rejected', 'Warung Ditolak');

        $this->get(route('public.search', ['q' => 'Kuliner']))
            ->assertOk()
            ->assertDontSee('Kategori');
    }

    public function test_search_hides_category_without_approved_products(): void
    {
        $category = $this->productCategory('Minuman');
        $umkm = $this->umkmFor($this->umkmCategory());
        $this->productFor($category, $umkm, 'pending', 'Kopi Pending');

        $this->get(route('public.search', ['q' => 'Minuman']))
            ->assertOk()
            ->assertDontSee('Kategori');
    }

    public function test_search_matches_category_by_description(): void
    {
        $category = $this->umkmCategory('Kuliner', 'Makanan khas Desa Salamnunggal');
        $this->umkmFor($category);

        $this->get(route('public.search', ['q' => 'khas']))
            ->assertOk()
            ->assertSee('Kategori')
            ->assertSee(route('public.category.umkm', $category));
    }

    public function test_search_category_group_links_to_matching_type_page(): void
    {
        $umkmCategory = $this->umkmCategory('Kerajinan');
        $productCategory = $this->productCategory('Kerajinan Tangan');
        $this->umkmFor($umkmCategory);
        $umkm = $this->umkmFor($this->umkmCategory('Makanan Ringan'));
        $this->productFor($productCategory, $umkm);

        $this->get(route('public.search', ['q' => 'Kerajinan']))
            ->assertOk()
            ->assertSee(route('public.category.umkm', $umkmCategory))
            ->assertSee(route('public.category.product', $productCategory));
    }

    public function test_search_with_category_only_results_shows_no_umkm_sections(): void
    {
        $category = $this->umkmCategory('Kuliner');
        $this->umkmFor($category, 'approved', 'Warung Maju');

        $this->get(route('public.search', ['q' => 'Kuliner']))
            ->assertOk()
            ->assertSee('Kategori')
            ->assertSee(route('public.category.umkm', $category))
            ->assertDontSee('Warung Maju');
    }
}
