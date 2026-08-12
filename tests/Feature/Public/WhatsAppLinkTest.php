<?php

namespace Tests\Feature\Public;

use App\Models\Category;
use App\Models\Product;
use App\Models\Umkm;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WhatsAppLinkTest extends TestCase
{
    use RefreshDatabase;

    private function owner(): User
    {
        $this->seed(DatabaseSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('owner');

        return $user;
    }

    private function umkmFor(string $phone): Umkm
    {
        return Umkm::create([
            'user_id' => $this->owner()->id,
            'category_id' => Category::firstOrCreate(['type' => 'umkm', 'name' => 'Kuliner', 'slug' => 'kuliner'])->id,
            'name' => 'Kedai Kopi Senja',
            'slug' => 'kedai-kopi-senja',
            'phone' => $phone,
            'status' => 'approved',
        ]);
    }

    private function productFor(Umkm $umkm): Product
    {
        return Product::create([
            'umkm_id' => $umkm->id,
            'category_id' => Category::firstOrCreate(['type' => 'product', 'name' => 'Makanan', 'slug' => 'makanan'])->id,
            'name' => 'Kopi Arabika',
            'slug' => 'kopi-arabika',
            'description' => 'Kopi asli Gunung Papandayan.',
            'price' => 15000,
            'status' => 'approved',
        ]);
    }

    public function test_umkm_detail_local_phone_is_normalized_to_international(): void
    {
        $umkm = $this->umkmFor('081234567890');

        $this->get(route('public.umkm.show', $umkm))
            ->assertOk()
            ->assertSee('href="https://wa.me/6281234567890"', false);
    }

    public function test_umkm_detail_international_phone_stays_unchanged(): void
    {
        $umkm = $this->umkmFor('6281234567890');

        $this->get(route('public.umkm.show', $umkm))
            ->assertOk()
            ->assertSee('href="https://wa.me/6281234567890"', false);
    }

    public function test_umkm_detail_plus_phone_loses_the_plus(): void
    {
        $umkm = $this->umkmFor('+6281234567890');

        $this->get(route('public.umkm.show', $umkm))
            ->assertOk()
            ->assertSee('href="https://wa.me/6281234567890"', false);
    }

    public function test_umkm_detail_formatted_local_phone_is_normalized(): void
    {
        $umkm = $this->umkmFor('0812-3456-7890');

        $this->get(route('public.umkm.show', $umkm))
            ->assertOk()
            ->assertSee('href="https://wa.me/6281234567890"', false);
    }

    public function test_umkm_phone_value_in_database_is_not_mutated(): void
    {
        $umkm = $this->umkmFor('081234567890');

        $this->get(route('public.umkm.show', $umkm))->assertOk();

        $this->assertSame('081234567890', $umkm->fresh()->phone);
    }

    public function test_product_detail_local_phone_is_normalized_to_international(): void
    {
        $umkm = $this->umkmFor('081234567890');
        $product = $this->productFor($umkm);

        $this->get(route('public.product.show', $product))
            ->assertOk()
            ->assertSee('href="https://wa.me/6281234567890"', false);
    }

    public function test_product_detail_international_phone_stays_unchanged(): void
    {
        $umkm = $this->umkmFor('6281234567890');
        $product = $this->productFor($umkm);

        $this->get(route('public.product.show', $product))
            ->assertOk()
            ->assertSee('href="https://wa.me/6281234567890"', false);
    }

    public function test_product_detail_plus_phone_loses_the_plus(): void
    {
        $umkm = $this->umkmFor('+6281234567890');
        $product = $this->productFor($umkm);

        $this->get(route('public.product.show', $product))
            ->assertOk()
            ->assertSee('href="https://wa.me/6281234567890"', false);
    }
}
