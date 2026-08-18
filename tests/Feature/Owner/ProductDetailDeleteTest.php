<?php

namespace Tests\Feature\Owner;

use App\Models\Category;
use App\Models\Product;
use App\Models\Umkm;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductDetailDeleteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
    }

    private function owner(): User
    {
        $this->seed(DatabaseSeeder::class);

        $user = User::factory()->create(['status' => 'approved']);
        $user->assignRole('owner');

        return $user;
    }

    private function administrator(): User
    {
        $this->seed(DatabaseSeeder::class);

        $user = User::factory()->create(['status' => 'approved']);
        $user->assignRole('administrator');

        return $user;
    }

    private function umkmFor(User $owner, string $status = 'approved'): Umkm
    {
        return Umkm::create([
            'user_id' => $owner->id,
            'category_id' => Category::firstOrCreate(['type' => 'umkm', 'name' => 'Kerajinan', 'slug' => 'kerajinan'])->id,
            'name' => 'Warung Maju',
            'slug' => Umkm::generateUniqueSlug('Warung Maju'),
            'status' => $status,
        ]);
    }

    private function productFor(User $owner, string $status = 'draft', ?Umkm $umkm = null): Product
    {
        return Product::create([
            'umkm_id' => ($umkm ?? $this->umkmFor($owner))->id,
            'category_id' => Category::firstOrCreate(['type' => 'product', 'name' => 'Makanan', 'slug' => 'makanan'])->id,
            'name' => 'Kopi Arabika',
            'slug' => 'kopi-arabika',
            'description' => 'Kopi asli Gunung Papandayan.',
            'price' => 25000,
            'status' => $status,
        ]);
    }

    public function test_guest_cannot_access_product_detail(): void
    {
        $product = $this->productFor($this->owner());

        $this->get(route('owner.products.show', $product))
            ->assertRedirect(route('login'));
    }

    public function test_guest_cannot_delete_product(): void
    {
        $product = $this->productFor($this->owner());

        $this->delete(route('owner.products.destroy', $product))
            ->assertRedirect(route('login'));

        $this->assertDatabaseHas('products', ['id' => $product->id]);
    }

    public function test_administrator_cannot_access_owner_product_detail(): void
    {
        $product = $this->productFor($this->owner());

        $this->actingAs($this->administrator())
            ->get(route('owner.products.show', $product))
            ->assertForbidden();
    }

    public function test_administrator_cannot_delete_via_owner_route(): void
    {
        $product = $this->productFor($this->owner());

        $this->actingAs($this->administrator())
            ->delete(route('owner.products.destroy', $product))
            ->assertForbidden();

        $this->assertDatabaseHas('products', ['id' => $product->id]);
    }

    public function test_owner_can_view_own_product_detail(): void
    {
        $owner = $this->owner();
        $product = $this->productFor($owner);

        $this->actingAs($owner)
            ->get(route('owner.products.show', $product))
            ->assertOk()
            ->assertSee($product->name)
            ->assertSee('Rp 25.000')
            ->assertSee('Makanan')
            ->assertSee('Kopi asli Gunung Papandayan.')
            ->assertSee('Draft');
    }

    public function test_owner_cannot_view_another_owner_product_detail(): void
    {
        $other = $this->owner();
        $product = $this->productFor($other);

        $this->actingAs($this->owner())
            ->get(route('owner.products.show', $product))
            ->assertForbidden();
    }

    public function test_detail_shows_product_photo_and_status(): void
    {
        $owner = $this->owner();
        $product = $this->productFor($owner, 'needs_revision');
        $media = $product->media()->create([
            'disk' => 'public',
            'path' => 'products/'.$product->id.'/foto.png',
            'collection' => 'product',
            'sort_order' => 0,
        ]);
        Storage::disk('public')->put($media->path, 'content');

        $product->verificationRequests()->create([
            'user_id' => $owner->id,
            'reviewer_id' => $this->administrator()->id,
            'status' => 'needs_revision',
            'notes' => 'Tambahkan foto produk.',
            'reviewed_at' => now(),
        ]);

        $this->actingAs($owner)
            ->get(route('owner.products.show', $product))
            ->assertOk()
            ->assertSee('/storage/'.$media->path)
            ->assertSee('Perlu Revisi')
            ->assertSee('Catatan Administrator: Tambahkan foto produk.');
    }

    public function test_owner_can_delete_draft_product(): void
    {
        $owner = $this->owner();
        $product = $this->productFor($owner);

        $this->actingAs($owner)
            ->delete(route('owner.products.destroy', $product))
            ->assertRedirect(route('owner.products.index', $product->umkm))
            ->assertSessionHas('status');

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_owner_can_delete_needs_revision_and_rejected_products(): void
    {
        foreach (['needs_revision', 'rejected'] as $status) {
            $owner = $this->owner();
            $product = $this->productFor($owner, $status);

            $this->actingAs($owner)
                ->delete(route('owner.products.destroy', $product))
                ->assertRedirect(route('owner.products.index', $product->umkm))
                ->assertSessionHas('status');

            $this->assertDatabaseMissing('products', ['id' => $product->id]);
        }
    }

    public function test_owner_cannot_delete_pending_product(): void
    {
        $owner = $this->owner();
        $product = $this->productFor($owner, 'pending');

        $this->actingAs($owner)
            ->from(route('owner.products.show', $product))
            ->delete(route('owner.products.destroy', $product))
            ->assertRedirect(route('owner.products.show', $product))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('products', ['id' => $product->id]);
    }

    public function test_owner_cannot_delete_approved_product(): void
    {
        $owner = $this->owner();
        $product = $this->productFor($owner, 'approved');

        $this->actingAs($owner)
            ->from(route('owner.products.show', $product))
            ->delete(route('owner.products.destroy', $product))
            ->assertRedirect(route('owner.products.show', $product))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('products', ['id' => $product->id]);
    }

    public function test_owner_cannot_delete_another_owner_product(): void
    {
        $other = $this->owner();
        $product = $this->productFor($other);

        $this->actingAs($this->owner())
            ->delete(route('owner.products.destroy', $product))
            ->assertForbidden();

        $this->assertDatabaseHas('products', ['id' => $product->id]);
    }

    public function test_delete_cleans_media_files_and_verification_requests(): void
    {
        $owner = $this->owner();
        $product = $this->productFor($owner);

        $media = $product->media()->create([
            'disk' => 'public',
            'path' => 'products/'.$product->id.'/foto.jpg',
            'collection' => 'product',
            'sort_order' => 0,
        ]);
        Storage::disk('public')->put($media->path, 'content');

        $product->verificationRequests()->create([
            'user_id' => $owner->id,
            'status' => 'pending',
            'reviewed_at' => null,
        ]);

        $this->actingAs($owner)
            ->delete(route('owner.products.destroy', $product))
            ->assertRedirect(route('owner.products.index', $product->umkm));

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
        $this->assertDatabaseCount('media', 0);
        $this->assertDatabaseCount('verification_requests', 0);
        Storage::disk('public')->assertMissing($media->path);
    }

    public function test_delete_records_activity_log(): void
    {
        $owner = $this->owner();
        $product = $this->productFor($owner);

        $this->actingAs($owner)
            ->delete(route('owner.products.destroy', $product));

        $this->assertDatabaseHas('activity_log', [
            'event' => 'product_deleted',
            'causer_id' => $owner->id,
            'causer_type' => User::class,
            'subject_id' => $product->id,
            'subject_type' => Product::class,
        ]);
    }

    public function test_detail_page_hides_delete_and_edit_actions_for_pending_product(): void
    {
        $owner = $this->owner();
        $product = $this->productFor($owner, 'pending');

        $this->actingAs($owner)
            ->get(route('owner.products.show', $product))
            ->assertOk()
            ->assertDontSee('Hapus Produk')
            ->assertDontSee('Edit Produk')
            ->assertDontSee('Kirim Pengajuan');
    }

    public function test_detail_page_shows_actions_for_draft_product(): void
    {
        $owner = $this->owner();
        $product = $this->productFor($owner);

        $this->actingAs($owner)
            ->get(route('owner.products.show', $product))
            ->assertOk()
            ->assertSee('Edit Produk')
            ->assertSee('Kirim Pengajuan')
            ->assertSee('Hapus Produk');
    }

    public function test_detail_page_links_to_public_page_for_approved_product(): void
    {
        $owner = $this->owner();
        $product = $this->productFor($owner, 'approved');

        $this->actingAs($owner)
            ->get(route('owner.products.show', $product))
            ->assertOk()
            ->assertSee('Lihat di Portal')
            ->assertSee(route('public.product.show', $product));
    }
}