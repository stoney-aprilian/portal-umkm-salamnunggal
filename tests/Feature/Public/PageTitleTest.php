<?php

namespace Tests\Feature\Public;

use App\Models\Category;
use App\Models\Product;
use App\Models\Umkm;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageTitleTest extends TestCase
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
        $this->seed(DatabaseSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('owner');

        return $user;
    }

    private function titleOf(string $url): string
    {
        $html = $this->get($url)->getContent();

        preg_match('/<title>(.*?)<\/title>/s', $html, $matches);

        return trim($matches[1] ?? '');
    }

    public function test_homepage_title_is_app_name(): void
    {
        $this->assertSame(config('app.name'), $this->titleOf('/'));
    }

    public function test_public_pages_include_page_specific_titles(): void
    {
        $expected = fn (string $title) => $title . ' — ' . config('app.name');

        $this->assertSame($expected('Katalog UMKM'), $this->titleOf(route('public.umkm.index')));
        $this->assertSame($expected('Katalog Produk'), $this->titleOf(route('public.product.index')));
        $this->assertSame($expected('Cari'), $this->titleOf(route('public.search')));
        $this->assertSame($expected('Tentang'), $this->titleOf(route('public.about')));
        $this->assertSame($expected('Kontak'), $this->titleOf(route('public.contact')));
    }

    public function test_detail_pages_include_entity_names_in_title(): void
    {
        $owner = $this->owner();
        $umkm = Umkm::create([
            'user_id' => $owner->id,
            'category_id' => $this->umkmCategory()->id,
            'name' => 'Warung Maju',
            'slug' => Umkm::generateUniqueSlug('Warung Maju'),
            'status' => 'approved',
        ]);
        $product = Product::create([
            'umkm_id' => $umkm->id,
            'category_id' => $this->productCategory()->id,
            'name' => 'Kopi Arabika',
            'slug' => 'kopi-arabika',
            'price' => 15000,
            'status' => 'approved',
        ]);

        $this->assertSame('Warung Maju — ' . config('app.name'), $this->titleOf(route('public.umkm.show', $umkm)));
        $this->assertSame('Kopi Arabika — ' . config('app.name'), $this->titleOf(route('public.product.show', $product)));
    }

    public function test_category_pages_include_category_names_in_title(): void
    {
        $umkmCategory = $this->umkmCategory();
        $productCategory = $this->productCategory();

        $this->assertSame(
            $umkmCategory->name . ' — UMKM — ' . config('app.name'),
            $this->titleOf(route('public.category.umkm', $umkmCategory))
        );

        $this->assertSame(
            $productCategory->name . ' — Produk — ' . config('app.name'),
            $this->titleOf(route('public.category.product', $productCategory))
        );
    }

    public function test_auth_pages_include_page_specific_titles(): void
    {
        $expected = fn (string $title) => $title . ' — ' . config('app.name');

        $this->assertSame($expected('Masuk'), $this->titleOf(route('login')));
        $this->assertSame($expected('Daftar'), $this->titleOf(route('register')));
        $this->assertSame($expected('Lupa Kata Sandi'), $this->titleOf(route('password.request')));
    }

    public function test_dashboard_pages_include_page_specific_titles(): void
    {
        $owner = $this->owner();
        $owner->assignRole('owner');

        $this->actingAs($owner);
        $this->assertSame('Dashboard — ' . config('app.name'), $this->titleOf(route('dashboard')));

        $this->seed(DatabaseSeeder::class);

        $admin = User::factory()->create();
        $admin->assignRole('administrator');

        $this->actingAs($admin);
        $this->assertSame('Dashboard Administrator — ' . config('app.name'), $this->titleOf(route('admin.dashboard')));
    }

    public function test_public_pages_include_meta_description(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('name="description"', false)
            ->assertSee('Portal UMKM Desa Salamnunggal', false);
    }
}
