<?php

namespace Tests\Feature\Owner;

use App\Models\Category;
use App\Models\Product;
use App\Models\Umkm;
use App\Models\User;
use App\Models\VerificationRequest;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductManagementTest extends TestCase
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

    private function administrator(): User
    {
        $this->seed(DatabaseSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('administrator');

        return $user;
    }

    private function umkmFor(User $owner, string $status = 'approved'): Umkm
    {
        return Umkm::create([
            'user_id' => $owner->id,
            'category_id' => $this->umkmCategory()->id,
            'name' => 'Warung Maju',
            'slug' => Umkm::generateUniqueSlug('Warung Maju'),
            'status' => $status,
        ]);
    }

    private function approvedUmkmFor(User $owner): Umkm
    {
        return $this->umkmFor($owner, 'approved');
    }

    private function productFor(
        User $owner,
        string $status = 'draft',
        ?Umkm $umkm = null,
        string $name = 'Kopi Arabika',
        string $slug = 'kopi-arabika',
    ): Product {
        return Product::create([
            'umkm_id' => ($umkm ?? $this->approvedUmkmFor($owner))->id,
            'category_id' => $this->productCategory()->id,
            'name' => $name,
            'slug' => $slug,
            'description' => 'Kopi asli Gunung Papandayan.',
            'price' => 50000,
            'status' => $status,
        ]);
    }

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'category_id' => $this->productCategory()->id,
            'name' => 'Kopi Arabika',
            'description' => 'Kopi asli Gunung Papandayan.',
            'price' => '50000',
        ], $overrides);
    }

    public function test_guest_cannot_access_product_index(): void
    {
        $owner = $this->owner();
        $umkm = $this->approvedUmkmFor($owner);

        $this->get(route('owner.products.index', $umkm))
            ->assertRedirect(route('login'));
    }

    public function test_administrator_cannot_access_product_index(): void
    {
        $owner = $this->owner();
        $umkm = $this->approvedUmkmFor($owner);

        $this->actingAs($this->administrator())
            ->get(route('owner.products.index', $umkm))
            ->assertForbidden();
    }

    public function test_owner_can_access_own_umkm_product_list(): void
    {
        $owner = $this->owner();
        $umkm = $this->approvedUmkmFor($owner);
        $product = $this->productFor($owner, 'draft', $umkm);

        $this->actingAs($owner)
            ->get(route('owner.products.index', $umkm))
            ->assertOk()
            ->assertSee($product->name);
    }

    public function test_owner_cannot_access_another_owner_umkm_products(): void
    {
        $other = $this->owner();
        $otherUmkm = $this->approvedUmkmFor($other);

        $this->actingAs($this->owner())
            ->get(route('owner.products.index', $otherUmkm))
            ->assertForbidden();
    }

    public function test_owner_cannot_edit_another_owner_product(): void
    {
        $other = $this->owner();
        $product = $this->productFor($other);

        $this->actingAs($this->owner())
            ->get(route('owner.products.edit', $product))
            ->assertForbidden();
    }

    public function test_owner_cannot_submit_another_owner_product(): void
    {
        $other = $this->owner();
        $product = $this->productFor($other);

        $this->actingAs($this->owner())
            ->post(route('owner.products.submit', $product))
            ->assertForbidden();

        $this->assertSame('draft', $product->fresh()->status);
    }

    public function test_owner_cannot_create_product_for_draft_umkm(): void
    {
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner, 'draft');

        $this->actingAs($owner)
            ->get(route('owner.products.create', $umkm))
            ->assertRedirect()
            ->assertSessionHas('error');
    }

    public function test_owner_cannot_create_product_for_pending_umkm(): void
    {
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner, 'pending');

        $this->actingAs($owner)
            ->get(route('owner.products.create', $umkm))
            ->assertRedirect()
            ->assertSessionHas('error');
    }

    public function test_owner_cannot_create_product_for_needs_revision_umkm(): void
    {
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner, 'needs_revision');

        $this->actingAs($owner)
            ->get(route('owner.products.create', $umkm))
            ->assertRedirect()
            ->assertSessionHas('error');
    }

    public function test_owner_can_create_product_for_approved_umkm(): void
    {
        $owner = $this->owner();
        $umkm = $this->approvedUmkmFor($owner);

        $this->actingAs($owner)
            ->get(route('owner.products.create', $umkm))
            ->assertOk();
    }

    public function test_owner_cannot_store_product_for_draft_umkm(): void
    {
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner, 'draft');

        $this->actingAs($owner)
            ->from(route('dashboard'))
            ->post(route('owner.products.store', $umkm), $this->validPayload())
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('error');

        $this->assertDatabaseCount('products', 0);
    }

    public function test_owner_cannot_submit_product_when_umkm_not_approved(): void
    {
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner, 'needs_revision');
        $product = $this->productFor($owner, 'draft', $umkm);

        $this->actingAs($owner)
            ->from(route('dashboard'))
            ->post(route('owner.products.submit', $product))
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('error');

        $this->assertSame('draft', $product->fresh()->status);
        $this->assertDatabaseCount('verification_requests', 0);
    }

    public function test_owner_can_store_product_draft(): void
    {
        $owner = $this->owner();
        $umkm = $this->approvedUmkmFor($owner);

        $this->actingAs($owner)
            ->post(route('owner.products.store', $umkm), $this->validPayload())
            ->assertRedirect(route('owner.products.index', $umkm))
            ->assertSessionHas('status');

        $this->assertDatabaseHas('products', [
            'umkm_id' => $umkm->id,
            'category_id' => $this->productCategory()->id,
            'name' => 'Kopi Arabika',
            'description' => 'Kopi asli Gunung Papandayan.',
            'price' => '50000',
            'status' => 'draft',
        ]);
    }

    public function test_store_ignores_forged_umkm_id(): void
    {
        $owner = $this->owner();
        $umkm = $this->approvedUmkmFor($owner);
        $otherUmkm = $this->approvedUmkmFor($this->owner());

        $this->actingAs($owner)
            ->post(route('owner.products.store', $umkm), $this->validPayload(['umkm_id' => $otherUmkm->id]))
            ->assertRedirect(route('owner.products.index', $umkm));

        $this->assertDatabaseHas('products', ['umkm_id' => $umkm->id]);
        $this->assertDatabaseMissing('products', ['umkm_id' => $otherUmkm->id]);
    }

    public function test_store_ignores_forged_status(): void
    {
        $owner = $this->owner();
        $umkm = $this->approvedUmkmFor($owner);

        $this->actingAs($owner)
            ->post(route('owner.products.store', $umkm), $this->validPayload(['status' => 'approved']))
            ->assertRedirect(route('owner.products.index', $umkm));

        $this->assertDatabaseHas('products', ['status' => 'draft']);
    }

    public function test_store_rejects_umkm_type_category(): void
    {
        $owner = $this->owner();
        $umkm = $this->approvedUmkmFor($owner);

        $this->actingAs($owner)
            ->post(route('owner.products.store', $umkm), $this->validPayload(['category_id' => $this->umkmCategory()->id]))
            ->assertSessionHasErrors('category_id');

        $this->assertDatabaseCount('products', 0);
    }

    public function test_store_rejects_negative_price(): void
    {
        $owner = $this->owner();
        $umkm = $this->approvedUmkmFor($owner);

        $this->actingAs($owner)
            ->post(route('owner.products.store', $umkm), $this->validPayload(['price' => '-1']))
            ->assertSessionHasErrors('price');

        $this->assertDatabaseCount('products', 0);
    }

    public function test_store_rejects_price_with_more_than_two_decimals(): void
    {
        $owner = $this->owner();
        $umkm = $this->approvedUmkmFor($owner);

        $this->actingAs($owner)
            ->post(route('owner.products.store', $umkm), $this->validPayload(['price' => '50000.123']))
            ->assertSessionHasErrors('price');

        $this->assertDatabaseCount('products', 0);
    }

    public function test_store_accepts_nullable_description(): void
    {
        $owner = $this->owner();
        $umkm = $this->approvedUmkmFor($owner);

        $this->actingAs($owner)
            ->post(route('owner.products.store', $umkm), $this->validPayload(['description' => null]))
            ->assertRedirect(route('owner.products.index', $umkm));

        $this->assertDatabaseHas('products', ['description' => null]);
    }

    public function test_slug_is_generated_server_side(): void
    {
        $owner = $this->owner();
        $umkm = $this->approvedUmkmFor($owner);

        $this->actingAs($owner)
            ->post(route('owner.products.store', $umkm), $this->validPayload(['slug' => 'dibajak']));

        $this->assertDatabaseHas('products', ['slug' => 'kopi-arabika']);
    }

    public function test_duplicate_slug_gets_unique_suffix(): void
    {
        $owner = $this->owner();
        $umkm = $this->approvedUmkmFor($owner);
        $this->productFor($owner, 'draft', $umkm);

        $this->actingAs($owner)
            ->post(route('owner.products.store', $umkm), $this->validPayload())
            ->assertRedirect(route('owner.products.index', $umkm));

        $this->assertDatabaseHas('products', ['slug' => 'kopi-arabika-2']);
    }

    public function test_owner_can_edit_draft_product_and_it_stays_draft(): void
    {
        $owner = $this->owner();
        $umkm = $this->approvedUmkmFor($owner);
        $product = $this->productFor($owner, 'draft', $umkm);

        $this->actingAs($owner)
            ->put(route('owner.products.update', $product), $this->validPayload([
                'name' => 'Kopi Robusta',
                'price' => '60000',
            ]))
            ->assertRedirect(route('owner.products.index', $umkm))
            ->assertSessionHas('status');

        $product->refresh();
        $this->assertSame('Kopi Robusta', $product->name);
        $this->assertSame('60000', (string) $product->price);
        $this->assertSame('draft', $product->status);
    }

    public function test_owner_cannot_edit_pending_product(): void
    {
        $owner = $this->owner();
        $umkm = $this->approvedUmkmFor($owner);
        $product = $this->productFor($owner, 'pending', $umkm);

        $this->actingAs($owner)
            ->from(route('owner.products.index', $umkm))
            ->put(route('owner.products.update', $product), $this->validPayload(['name' => 'Nama Baru']))
            ->assertRedirect(route('owner.products.index', $umkm))
            ->assertSessionHas('error');

        $this->assertSame('pending', $product->fresh()->status);
        $this->assertSame('Kopi Arabika', $product->fresh()->name);
    }

    public function test_owner_cannot_edit_approved_product(): void
    {
        $owner = $this->owner();
        $umkm = $this->approvedUmkmFor($owner);
        $product = $this->productFor($owner, 'approved', $umkm);

        $this->actingAs($owner)
            ->from(route('owner.products.index', $umkm))
            ->put(route('owner.products.update', $product), $this->validPayload(['name' => 'Nama Baru']))
            ->assertRedirect(route('owner.products.index', $umkm))
            ->assertSessionHas('error');

        $this->assertSame('approved', $product->fresh()->status);
        $this->assertSame('Kopi Arabika', $product->fresh()->name);
    }

    public function test_owner_can_edit_rejected_product_and_it_returns_to_draft(): void
    {
        $owner = $this->owner();
        $umkm = $this->approvedUmkmFor($owner);
        $product = $this->productFor($owner, 'rejected', $umkm);

        $this->actingAs($owner)
            ->put(route('owner.products.update', $product), $this->validPayload(['name' => 'Nama Baru']))
            ->assertRedirect(route('owner.products.index', $umkm))
            ->assertSessionHas('status');

        $this->assertSame('draft', $product->fresh()->status);
        $this->assertSame('Nama Baru', $product->fresh()->name);
    }

    public function test_rejected_product_can_be_resubmitted(): void
    {
        $owner = $this->owner();
        $umkm = $this->approvedUmkmFor($owner);
        $product = $this->productFor($owner, 'rejected', $umkm);

        $product->verificationRequests()->create([
            'user_id' => $owner->id,
            'reviewer_id' => $this->administrator()->id,
            'status' => 'rejected',
            'notes' => 'Foto produk tidak jelas.',
            'reviewed_at' => now(),
        ]);

        $this->actingAs($owner)
            ->put(route('owner.products.update', $product), $this->validPayload(['name' => 'Kopi Arabika Premium']));

        $this->actingAs($owner)
            ->post(route('owner.products.submit', $product))
            ->assertRedirect(route('owner.products.index', $umkm));

        $this->assertSame('pending', $product->fresh()->status);
        $this->assertDatabaseCount('verification_requests', 2);

        $latest = $product->verificationRequests()->latest('id')->firstOrFail();
        $this->assertSame('pending', $latest->status);
        $this->assertNull($latest->reviewer_id);

        $this->assertDatabaseHas('verification_requests', [
            'status' => 'rejected',
            'notes' => 'Foto produk tidak jelas.',
        ]);
    }

    public function test_owner_can_edit_needs_revision_product(): void
    {
        $owner = $this->owner();
        $umkm = $this->approvedUmkmFor($owner);
        $product = $this->productFor($owner, 'needs_revision', $umkm);

        $this->actingAs($owner)
            ->get(route('owner.products.edit', $product))
            ->assertOk();
    }

    public function test_needs_revision_product_update_returns_to_draft(): void
    {
        $owner = $this->owner();
        $umkm = $this->approvedUmkmFor($owner);
        $product = $this->productFor($owner, 'needs_revision', $umkm);

        $this->actingAs($owner)
            ->put(route('owner.products.update', $product), $this->validPayload())
            ->assertRedirect(route('owner.products.index', $umkm));

        $this->assertSame('draft', $product->fresh()->status);
    }

    public function test_edit_keeps_previous_verification_request_history(): void
    {
        $owner = $this->owner();
        $umkm = $this->approvedUmkmFor($owner);
        $product = $this->productFor($owner, 'needs_revision', $umkm);

        $product->verificationRequests()->create([
            'user_id' => $owner->id,
            'reviewer_id' => $this->administrator()->id,
            'status' => 'needs_revision',
            'notes' => 'Tambahkan deskripsi produk.',
            'reviewed_at' => now(),
        ]);

        $this->actingAs($owner)
            ->put(route('owner.products.update', $product), $this->validPayload())
            ->assertRedirect(route('owner.products.index', $umkm));

        $this->assertDatabaseHas('verification_requests', [
            'status' => 'needs_revision',
            'notes' => 'Tambahkan deskripsi produk.',
        ]);
        $this->assertSame('draft', $product->fresh()->status);
    }

    public function test_update_keeps_same_slug_when_name_unchanged(): void
    {
        $owner = $this->owner();
        $umkm = $this->approvedUmkmFor($owner);
        $product = $this->productFor($owner, 'draft', $umkm);

        $this->actingAs($owner)
            ->put(route('owner.products.update', $product), $this->validPayload());

        $this->assertSame('kopi-arabika', $product->fresh()->slug);
    }

    public function test_update_slug_avoids_collision_with_other_product(): void
    {
        $owner = $this->owner();
        $umkm = $this->approvedUmkmFor($owner);
        $this->productFor($owner, 'draft', $umkm);
        $product = $this->productFor($owner, 'draft', $umkm, 'Kopi Robusta', 'kopi-robusta');

        $this->actingAs($owner)
            ->put(route('owner.products.update', $product), $this->validPayload(['name' => 'Kopi Arabika']))
            ->assertRedirect(route('owner.products.index', $umkm));

        $this->assertSame('kopi-arabika-2', $product->fresh()->slug);
    }

    public function test_owner_can_submit_draft_product(): void
    {
        $owner = $this->owner();
        $umkm = $this->approvedUmkmFor($owner);
        $product = $this->productFor($owner, 'draft', $umkm);

        $this->actingAs($owner)
            ->post(route('owner.products.submit', $product))
            ->assertRedirect(route('owner.products.index', $umkm))
            ->assertSessionHas('status');

        $this->assertSame('pending', $product->fresh()->status);
    }

    public function test_submit_creates_pending_verification_request(): void
    {
        $owner = $this->owner();
        $umkm = $this->approvedUmkmFor($owner);
        $product = $this->productFor($owner, 'draft', $umkm);

        $this->actingAs($owner)->post(route('owner.products.submit', $product));

        $this->assertDatabaseCount('verification_requests', 1);

        $request = VerificationRequest::first();

        $this->assertSame($owner->id, $request->user_id);
        $this->assertNull($request->reviewer_id);
        $this->assertSame('pending', $request->status);
        $this->assertNull($request->notes);
        $this->assertNull($request->reviewed_at);
        $this->assertSame('App\\Models\\Product', $request->verifiable_type);
        $this->assertSame($product->id, $request->verifiable_id);
        $this->assertTrue($request->verifiable->is($product));
    }

    public function test_duplicate_submission_is_blocked(): void
    {
        $owner = $this->owner();
        $umkm = $this->approvedUmkmFor($owner);
        $product = $this->productFor($owner, 'pending', $umkm);

        $this->actingAs($owner)
            ->from(route('owner.products.index', $umkm))
            ->post(route('owner.products.submit', $product))
            ->assertRedirect(route('owner.products.index', $umkm))
            ->assertSessionHas('error');

        $this->assertSame('pending', $product->fresh()->status);
        $this->assertDatabaseCount('verification_requests', 0);
    }

    public function test_submission_is_atomic_when_verification_request_creation_fails(): void
    {
        $owner = $this->owner();
        $umkm = $this->approvedUmkmFor($owner);
        $product = $this->productFor($owner, 'draft', $umkm);

        VerificationRequest::creating(function () {
            throw new \RuntimeException('forced failure');
        });

        try {
            $this->actingAs($owner)
                ->post(route('owner.products.submit', $product))
                ->assertStatus(500);
        } finally {
            VerificationRequest::getEventDispatcher()
                ->forget('eloquent.creating: '.VerificationRequest::class);
        }

        $this->assertSame('draft', $product->fresh()->status);
        $this->assertDatabaseCount('verification_requests', 0);
    }

    public function test_resubmission_creates_second_request_and_keeps_history(): void
    {
        $owner = $this->owner();
        $umkm = $this->approvedUmkmFor($owner);
        $product = $this->productFor($owner, 'needs_revision', $umkm);

        $product->verificationRequests()->create([
            'user_id' => $owner->id,
            'reviewer_id' => $this->administrator()->id,
            'status' => 'needs_revision',
            'notes' => 'Perbaiki harga produk.',
            'reviewed_at' => now(),
        ]);

        $this->actingAs($owner)
            ->put(route('owner.products.update', $product), $this->validPayload(['price' => '75000']));

        $this->actingAs($owner)
            ->post(route('owner.products.submit', $product))
            ->assertRedirect(route('owner.products.index', $umkm));

        $this->assertSame('pending', $product->fresh()->status);
        $this->assertDatabaseCount('verification_requests', 2);

        $latest = $product->verificationRequests()->latest('id')->firstOrFail();
        $this->assertSame($owner->id, $latest->user_id);
        $this->assertNull($latest->reviewer_id);
        $this->assertSame('pending', $latest->status);
        $this->assertNull($latest->notes);
        $this->assertNull($latest->reviewed_at);

        $this->assertDatabaseHas('verification_requests', [
            'status' => 'needs_revision',
            'notes' => 'Perbaiki harga produk.',
        ]);
    }

    public function test_draft_product_shows_submit_action(): void
    {
        $owner = $this->owner();
        $umkm = $this->approvedUmkmFor($owner);
        $this->productFor($owner, 'draft', $umkm);

        $this->actingAs($owner)
            ->get(route('owner.products.index', $umkm))
            ->assertSee('Kirim Pengajuan');
    }

    public function test_pending_product_hides_submit_action(): void
    {
        $owner = $this->owner();
        $umkm = $this->approvedUmkmFor($owner);
        $this->productFor($owner, 'pending', $umkm);

        $this->actingAs($owner)
            ->get(route('owner.products.index', $umkm))
            ->assertDontSee('Kirim Pengajuan');
    }

    public function test_approved_product_hides_submit_action(): void
    {
        $owner = $this->owner();
        $umkm = $this->approvedUmkmFor($owner);
        $this->productFor($owner, 'approved', $umkm);

        $this->actingAs($owner)
            ->get(route('owner.products.index', $umkm))
            ->assertDontSee('Kirim Pengajuan');
    }

    public function test_rejected_product_shows_fix_action_but_hides_submit_action(): void
    {
        $owner = $this->owner();
        $umkm = $this->approvedUmkmFor($owner);
        $this->productFor($owner, 'rejected', $umkm);

        $this->actingAs($owner)
            ->get(route('owner.products.index', $umkm))
            ->assertSee('Perbaiki Produk')
            ->assertDontSee('Kirim Pengajuan');
    }

    public function test_rejected_product_shows_rejection_reason(): void
    {
        $owner = $this->owner();
        $umkm = $this->approvedUmkmFor($owner);
        $product = $this->productFor($owner, 'rejected', $umkm);

        $product->verificationRequests()->create([
            'user_id' => $owner->id,
            'reviewer_id' => $this->administrator()->id,
            'status' => 'rejected',
            'notes' => 'Harga produk tidak sesuai.',
            'reviewed_at' => now(),
        ]);

        $this->actingAs($owner)
            ->get(route('owner.products.index', $umkm))
            ->assertSee('Alasan Penolakan: Harga produk tidak sesuai.');
    }

    public function test_needs_revision_product_shows_note_and_fix_action(): void
    {
        $owner = $this->owner();
        $umkm = $this->approvedUmkmFor($owner);
        $product = $this->productFor($owner, 'needs_revision', $umkm);

        $product->verificationRequests()->create([
            'user_id' => $owner->id,
            'reviewer_id' => $this->administrator()->id,
            'status' => 'needs_revision',
            'notes' => 'Tambahkan foto produk.',
            'reviewed_at' => now(),
        ]);

        $this->actingAs($owner)
            ->get(route('owner.products.index', $umkm))
            ->assertSee('Catatan Administrator: Tambahkan foto produk.')
            ->assertSee('Perbaiki Produk');
    }

    public function test_empty_product_list_shows_empty_state(): void
    {
        $owner = $this->owner();
        $umkm = $this->approvedUmkmFor($owner);

        $this->actingAs($owner)
            ->get(route('owner.products.index', $umkm))
            ->assertOk()
            ->assertSee('Belum ada produk');
    }

    public function test_product_list_shows_add_product_button(): void
    {
        $owner = $this->owner();
        $umkm = $this->approvedUmkmFor($owner);

        $this->actingAs($owner)
            ->get(route('owner.products.index', $umkm))
            ->assertSee('Tambah Produk');
    }

    public function test_dashboard_shows_product_management_link_for_approved_umkm(): void
    {
        $owner = $this->owner();
        $umkm = $this->approvedUmkmFor($owner);

        $this->actingAs($owner)
            ->get(route('dashboard'))
            ->assertSee('Kelola Produk')
            ->assertSee(route('owner.products.index', $umkm));
    }

    public function test_dashboard_hides_product_management_link_for_non_approved_umkm(): void
    {
        $owner = $this->owner();
        $this->umkmFor($owner, 'pending');

        $this->actingAs($owner)
            ->get(route('dashboard'))
            ->assertDontSee('Kelola Produk');
    }

    public function test_edit_form_prefills_whole_price_without_decimal_zeros(): void
    {
        $owner = $this->owner();
        $umkm = $this->approvedUmkmFor($owner);
        $product = $this->productFor($owner, 'draft', $umkm);
        $product->update(['price' => 15000.00]);

        $this->actingAs($owner)
            ->get(route('owner.products.edit', $product))
            ->assertOk()
            ->assertSee('value="15000"', false)
            ->assertDontSee('15000.00');
    }

    public function test_edit_form_preserves_fractional_price(): void
    {
        $owner = $this->owner();
        $umkm = $this->approvedUmkmFor($owner);
        $product = $this->productFor($owner, 'draft', $umkm);
        $product->update(['price' => 15000.50]);

        $this->actingAs($owner)
            ->get(route('owner.products.edit', $product))
            ->assertOk()
            ->assertSee('value="15000.5"', false);
    }

    public function test_submit_records_activity_log_with_correct_causer_and_subject(): void
    {
        $owner = $this->owner();
        $umkm = $this->approvedUmkmFor($owner);
        $product = $this->productFor($owner, 'draft', $umkm);

        $this->actingAs($owner)
            ->post(route('owner.products.submit', $product))
            ->assertRedirect(route('owner.products.index', $umkm));

        $activity = \Spatie\Activitylog\Models\Activity::where('event', 'submitted')
            ->where('subject_type', Product::class)
            ->where('subject_id', $product->id)
            ->firstOrFail();

        $this->assertSame('submitted', $activity->event);
        $this->assertSame($owner->id, $activity->causer_id);
        $this->assertSame(User::class, $activity->causer_type);
        $this->assertSame($product->id, $activity->subject_id);
        $this->assertSame(Product::class, $activity->subject_type);
        $this->assertStringContainsString('Kopi Arabika', $activity->description);
        $this->assertStringNotContainsString('password', strtolower($activity->description));
        $this->assertStringNotContainsString('password', strtolower(json_encode($activity->properties)));
    }
}
