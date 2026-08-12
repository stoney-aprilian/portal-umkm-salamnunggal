<?php

namespace Tests\Feature\Public;

use App\Models\Category;
use App\Models\Product;
use App\Models\Umkm;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SimilarProductTest extends TestCase
{
    use RefreshDatabase;

    private function category(): Category
    {
        return Category::firstOrCreate([
            'type' => 'product',
            'name' => 'Makanan',
            'slug' => 'makanan',
        ]);
    }

    private function otherCategory(): Category
    {
        return Category::firstOrCreate([
            'type' => 'product',
            'name' => 'Minuman',
            'slug' => 'minuman',
        ]);
    }

    private function owner(): User
    {
        $this->seed(DatabaseSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('owner');

        return $user;
    }

    private function approvedUmkm(): Umkm
    {
        return Umkm::create([
            'user_id' => $this->owner()->id,
            'category_id' => Category::firstOrCreate(['type' => 'umkm', 'name' => 'Kuliner', 'slug' => 'kuliner'])->id,
            'name' => 'Kedai Kopi Senja',
            'slug' => 'kedai-kopi-senja',
            'status' => 'approved',
        ]);
    }

    private function productFor(Umkm $umkm, Category $category, string $status = 'approved', string $name = 'Kopi Arabika'): Product
    {
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

    public function test_similar_products_share_the_same_category(): void
    {
        $umkm = $this->approvedUmkm();
        $this->productFor($umkm, $this->category(), 'approved', 'Kopi Arabika');
        $this->productFor($umkm, $this->category(), 'approved', 'Kopi Robusta');
        $this->productFor($umkm, $this->category(), 'approved', 'Kopi Luwak');

        $this->get(route('public.product.show', 'kopi-arabika'))
            ->assertOk()
            ->assertSee('Produk Serupa')
            ->assertSee('Kopi Robusta')
            ->assertSee('Kopi Luwak');
    }

    public function test_similar_products_exclude_products_under_non_approved_umkm(): void
    {
        $umkm = $this->approvedUmkm();
        $this->productFor($umkm, $this->category(), 'approved', 'Kopi Arabika');

        $draftUmkm = Umkm::create([
            'user_id' => $this->owner()->id,
            'category_id' => Category::firstOrCreate(['type' => 'umkm', 'name' => 'Kuliner', 'slug' => 'kuliner'])->id,
            'name' => 'Warung Draft',
            'slug' => 'warung-draft',
            'status' => 'draft',
        ]);
        $this->productFor($draftUmkm, $this->category(), 'approved', 'Kopi Gelap');

        $this->get(route('public.product.show', 'kopi-arabika'))
            ->assertOk()
            ->assertDontSee('Produk Serupa')
            ->assertDontSee('Kopi Gelap');
    }

    public function test_similar_products_exclude_non_approved_products(): void
    {
        $umkm = $this->approvedUmkm();
        $this->productFor($umkm, $this->category(), 'approved', 'Kopi Arabika');
        $this->productFor($umkm, $this->category(), 'draft', 'Kopi Draft');
        $this->productFor($umkm, $this->category(), 'pending', 'Kopi Pending');
        $this->productFor($umkm, $this->category(), 'rejected', 'Kopi Ditolak');

        $this->get(route('public.product.show', 'kopi-arabika'))
            ->assertOk()
            ->assertDontSee('Produk Serupa')
            ->assertDontSee('Kopi Draft')
            ->assertDontSee('Kopi Pending')
            ->assertDontSee('Kopi Ditolak');
    }

    public function test_similar_products_exclude_other_categories(): void
    {
        $umkm = $this->approvedUmkm();
        $this->productFor($umkm, $this->category(), 'approved', 'Kopi Arabika');
        $this->productFor($umkm, $this->otherCategory(), 'approved', 'Es Teh Manis');

        $this->get(route('public.product.show', 'kopi-arabika'))
            ->assertOk()
            ->assertDontSee('Produk Serupa')
            ->assertDontSee('Es Teh Manis');
    }

    public function test_similar_products_are_limited_to_four(): void
    {
        $umkm = $this->approvedUmkm();
        $this->productFor($umkm, $this->category(), 'approved', 'Kopi Arabika');

        foreach (['Kopi A', 'Kopi B', 'Kopi C', 'Kopi D', 'Kopi E', 'Kopi F'] as $name) {
            $this->productFor($umkm, $this->category(), 'approved', $name);
        }

        $response = $this->get(route('public.product.show', 'kopi-arabika'));
        $response->assertOk()->assertSee('Produk Serupa');

        $this->assertSame(4, substr_count($response->getContent(), 'Lihat Detail'));
    }
}
