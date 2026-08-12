<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Product;
use App\Models\Umkm;
use App\Models\User;
use App\Models\VerificationRequest;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ProductVerificationTest extends TestCase
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

    private function productFor(
        User $owner,
        string $status = 'pending',
        ?Umkm $umkm = null,
        string $name = 'Kopi Arabika',
        string $slug = 'kopi-arabika',
    ): Product {
        return Product::create([
            'umkm_id' => ($umkm ?? $this->umkmFor($owner))->id,
            'category_id' => $this->productCategory()->id,
            'name' => $name,
            'slug' => $slug,
            'description' => 'Kopi asli Gunung Papandayan.',
            'price' => 15000,
            'status' => $status,
        ]);
    }

    private function productRequestFor(
        Product $product,
        User $owner,
        string $status = 'pending',
        string $note = 'Perbaiki harga produk.',
    ): VerificationRequest {
        return $product->verificationRequests()->create([
            'user_id' => $owner->id,
            'reviewer_id' => $status === 'pending' ? null : $this->administrator()->id,
            'status' => $status,
            'notes' => $status === 'pending' ? null : $note,
            'reviewed_at' => $status === 'pending' ? null : now(),
        ]);
    }

    private function umkmRequestFor(Umkm $umkm, User $owner): VerificationRequest
    {
        return $umkm->verificationRequests()->create([
            'user_id' => $owner->id,
            'reviewer_id' => null,
            'status' => 'pending',
            'notes' => null,
            'reviewed_at' => null,
        ]);
    }

    public function test_guest_cannot_access_product_queue(): void
    {
        $this->get(route('admin.products.verification.index'))
            ->assertRedirect(route('login'));
    }

    public function test_owner_cannot_access_product_queue(): void
    {
        $owner = $this->owner();

        $this->actingAs($owner)
            ->get(route('admin.products.verification.index'))
            ->assertForbidden();
    }

    public function test_administrator_can_access_product_queue(): void
    {
        $admin = $this->administrator();

        $this->actingAs($admin)
            ->get(route('admin.products.verification.index'))
            ->assertOk()
            ->assertSee('Verifikasi Produk');
    }

    public function test_owner_cannot_open_product_review_detail(): void
    {
        $owner = $this->owner();
        $product = $this->productFor($owner);
        $request = $this->productRequestFor($product, $owner);

        $this->actingAs($owner)
            ->get(route('admin.products.verification.show', $request))
            ->assertForbidden();
    }

    public function test_administrator_can_open_pending_product_review(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $product = $this->productFor($owner);
        $request = $this->productRequestFor($product, $owner);

        $this->actingAs($admin)
            ->get(route('admin.products.verification.show', $request))
            ->assertOk()
            ->assertSee('Kopi Arabika')
            ->assertSee($owner->name)
            ->assertSee('Setujui')
            ->assertSee('Tolak')
            ->assertSee('Perlu Revisi');
    }

    public function test_umkm_request_cannot_be_reviewed_through_product_endpoints(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner, 'pending');
        $request = $this->umkmRequestFor($umkm, $owner);

        $this->actingAs($admin)
            ->get(route('admin.products.verification.show', $request))
            ->assertNotFound();

        $this->actingAs($admin)
            ->post(route('admin.products.verification.approve', $request))
            ->assertNotFound();

        $this->assertSame('pending', $request->fresh()->status);
        $this->assertSame('pending', $umkm->fresh()->status);
    }

    public function test_pending_product_requests_appear_in_queue(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $product = $this->productFor($owner);
        $this->productRequestFor($product, $owner);

        $this->actingAs($admin)
            ->get(route('admin.products.verification.index'))
            ->assertOk()
            ->assertSee('Kopi Arabika')
            ->assertSee('Menunggu Pemeriksaan')
            ->assertSee('Periksa');
    }

    public function test_queue_shows_product_umkm_owner_category_and_price(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner);
        $product = $this->productFor($owner, 'pending', $umkm);
        $this->productRequestFor($product, $owner);

        $this->actingAs($admin)
            ->get(route('admin.products.verification.index'))
            ->assertSee('Kopi Arabika')
            ->assertSee('Warung Maju')
            ->assertSee($owner->name)
            ->assertSee('Makanan')
            ->assertSee('Rp 15.000');
    }

    public function test_approved_product_requests_do_not_appear_in_queue(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $product = $this->productFor($owner, 'approved');
        $this->productRequestFor($product, $owner, 'approved');

        $this->actingAs($admin)
            ->get(route('admin.products.verification.index'))
            ->assertOk()
            ->assertDontSee('Kopi Arabika');
    }

    public function test_rejected_product_requests_do_not_appear_in_queue(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $product = $this->productFor($owner, 'rejected');
        $this->productRequestFor($product, $owner, 'rejected');

        $this->actingAs($admin)
            ->get(route('admin.products.verification.index'))
            ->assertOk()
            ->assertDontSee('Kopi Arabika');
    }

    public function test_needs_revision_product_requests_do_not_appear_in_queue(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $product = $this->productFor($owner, 'needs_revision');
        $this->productRequestFor($product, $owner, 'needs_revision');

        $this->actingAs($admin)
            ->get(route('admin.products.verification.index'))
            ->assertOk()
            ->assertDontSee('Kopi Arabika');
    }

    public function test_umkm_requests_do_not_appear_in_product_queue(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner, 'pending');
        $this->umkmRequestFor($umkm, $owner);

        $this->actingAs($admin)
            ->get(route('admin.products.verification.index'))
            ->assertOk()
            ->assertDontSee('Warung Maju');
    }

    public function test_newest_requests_appear_first(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner);

        $first = $this->productFor($owner, 'pending', $umkm, 'Produk Pertama', 'produk-pertama');
        $second = $this->productFor($owner, 'pending', $umkm, 'Produk Kedua', 'produk-kedua');

        $this->productRequestFor($first, $owner);
        VerificationRequest::where('verifiable_id', $first->id)
            ->where('verifiable_type', Product::class)
            ->update(['created_at' => now()->subHour()]);

        $this->productRequestFor($second, $owner);

        $this->actingAs($admin)
            ->get(route('admin.products.verification.index'))
            ->assertSeeInOrder(['Produk Kedua', 'Produk Pertama']);
    }

    public function test_empty_queue_shows_empty_state(): void
    {
        $admin = $this->administrator();

        $this->actingAs($admin)
            ->get(route('admin.products.verification.index'))
            ->assertOk()
            ->assertSee('Pengajuan Produk')
            ->assertSee('Tidak ada pengajuan produk yang menunggu pemeriksaan.');
    }

    public function test_product_detail_shows_product_information(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $product = $this->productFor($owner);
        $request = $this->productRequestFor($product, $owner);

        $this->actingAs($admin)
            ->get(route('admin.products.verification.show', $request))
            ->assertSee('Kopi Arabika')
            ->assertSee('Makanan')
            ->assertSee('Rp 15.000')
            ->assertSee('Kopi asli Gunung Papandayan.')
            ->assertSee('Warung Maju');
    }

    public function test_product_detail_shows_owner_information(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $product = $this->productFor($owner);
        $request = $this->productRequestFor($product, $owner);

        $this->actingAs($admin)
            ->get(route('admin.products.verification.show', $request))
            ->assertSee($owner->name)
            ->assertSee($owner->email);
    }

    public function test_review_actions_hidden_for_non_pending_requests(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $product = $this->productFor($owner, 'approved');
        $request = $this->productRequestFor($product, $owner, 'approved');

        $this->actingAs($admin)
            ->get(route('admin.products.verification.show', $request))
            ->assertOk()
            ->assertDontSee('Setujui')
            ->assertDontSee('Tolak')
            ->assertDontSee('Perlu Revisi');
    }

    public function test_administrator_can_approve_pending_product(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $product = $this->productFor($owner);
        $request = $this->productRequestFor($product, $owner);

        $this->actingAs($admin)
            ->post(route('admin.products.verification.approve', $request))
            ->assertRedirect(route('admin.products.verification.index'))
            ->assertSessionHas('status');
    }

    public function test_product_becomes_approved_after_approval(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $product = $this->productFor($owner);
        $request = $this->productRequestFor($product, $owner);

        $this->actingAs($admin)->post(route('admin.products.verification.approve', $request));

        $this->assertSame('approved', $product->fresh()->status);
    }

    public function test_verification_request_becomes_approved_after_approval(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $product = $this->productFor($owner);
        $request = $this->productRequestFor($product, $owner);

        $this->actingAs($admin)->post(route('admin.products.verification.approve', $request));

        $this->assertSame('approved', $request->fresh()->status);
    }

    public function test_approval_sets_reviewer_id_to_authenticated_administrator(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $product = $this->productFor($owner);
        $request = $this->productRequestFor($product, $owner);

        $this->actingAs($admin)->post(route('admin.products.verification.approve', $request));

        $this->assertSame($admin->id, $request->fresh()->reviewer_id);
    }

    public function test_approval_populates_reviewed_at(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $product = $this->productFor($owner);
        $request = $this->productRequestFor($product, $owner);

        $this->actingAs($admin)->post(route('admin.products.verification.approve', $request));

        $this->assertNotNull($request->fresh()->reviewed_at);
    }

    public function test_approval_sets_notes_to_null(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $product = $this->productFor($owner);
        $request = $this->productRequestFor($product, $owner);

        $this->actingAs($admin)->post(route('admin.products.verification.approve', $request));

        $this->assertNull($request->fresh()->notes);
    }

    public function test_approval_does_not_change_other_product_fields(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner);
        $product = $this->productFor($owner, 'pending', $umkm);
        $request = $this->productRequestFor($product, $owner);

        $before = [
            'umkm_id' => $product->umkm_id,
            'category_id' => $product->category_id,
            'name' => $product->name,
            'slug' => $product->slug,
            'price' => $product->price,
            'description' => $product->description,
        ];

        $this->actingAs($admin)->post(route('admin.products.verification.approve', $request));

        $after = $product->fresh();
        $this->assertSame($before['umkm_id'], $after->umkm_id);
        $this->assertSame($before['category_id'], $after->category_id);
        $this->assertSame($before['name'], $after->name);
        $this->assertSame($before['slug'], $after->slug);
        $this->assertSame($before['price'], $after->price);
        $this->assertSame($before['description'], $after->description);
        $this->assertSame($owner->id, $after->umkm->user_id);
        $this->assertSame('pending', $owner->fresh()->status);
    }

    public function test_approval_does_not_create_media(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $product = $this->productFor($owner);
        $request = $this->productRequestFor($product, $owner);

        $this->actingAs($admin)->post(route('admin.products.verification.approve', $request));

        $this->assertDatabaseCount('media', 0);
    }

    public function test_rejection_requires_notes(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $product = $this->productFor($owner);
        $request = $this->productRequestFor($product, $owner);

        $this->actingAs($admin)
            ->from(route('admin.products.verification.show', $request))
            ->post(route('admin.products.verification.reject', $request), ['notes' => ''])
            ->assertSessionHasErrors('notes', null, 'reject');

        $this->assertSame('pending', $request->fresh()->status);
        $this->assertSame('pending', $product->fresh()->status);
    }

    public function test_notes_validation_error_is_displayed_on_review_page(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $product = $this->productFor($owner);
        $request = $this->productRequestFor($product, $owner);

        $this->actingAs($admin)
            ->from(route('admin.products.verification.show', $request))
            ->post(route('admin.products.verification.reject', $request), ['notes' => ''])
            ->assertSessionHasErrors('notes', null, 'reject');

        $this->withCookie(session()->getName(), session()->getId())
            ->get(route('admin.products.verification.show', $request))
            ->assertOk()
            ->assertSee('Catatan wajib diisi.');
    }

    public function test_rejection_requires_string_notes(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $product = $this->productFor($owner);
        $request = $this->productRequestFor($product, $owner);

        $this->actingAs($admin)
            ->post(route('admin.products.verification.reject', $request), ['notes' => 123])
            ->assertSessionHasErrors('notes', null, 'reject');

        $this->assertSame('pending', $request->fresh()->status);
    }

    public function test_rejection_notes_max_1000_characters(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $product = $this->productFor($owner);
        $request = $this->productRequestFor($product, $owner);

        $this->actingAs($admin)
            ->post(route('admin.products.verification.reject', $request), ['notes' => str_repeat('a', 1001)])
            ->assertSessionHasErrors('notes', null, 'reject');

        $this->assertSame('pending', $request->fresh()->status);
    }

    public function test_administrator_can_reject_with_notes(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $product = $this->productFor($owner);
        $request = $this->productRequestFor($product, $owner);

        $this->actingAs($admin)
            ->post(route('admin.products.verification.reject', $request), ['notes' => 'Harga produk tidak sesuai.'])
            ->assertRedirect(route('admin.products.verification.index'))
            ->assertSessionHas('status');
    }

    public function test_product_becomes_rejected_after_rejection(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $product = $this->productFor($owner);
        $request = $this->productRequestFor($product, $owner);

        $this->actingAs($admin)->post(route('admin.products.verification.reject', $request), ['notes' => 'Harga produk tidak sesuai.']);

        $this->assertSame('rejected', $product->fresh()->status);
    }

    public function test_verification_request_becomes_rejected_after_rejection(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $product = $this->productFor($owner);
        $request = $this->productRequestFor($product, $owner);

        $this->actingAs($admin)->post(route('admin.products.verification.reject', $request), ['notes' => 'Harga produk tidak sesuai.']);

        $this->assertSame('rejected', $request->fresh()->status);
    }

    public function test_rejection_sets_reviewer_id(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $product = $this->productFor($owner);
        $request = $this->productRequestFor($product, $owner);

        $this->actingAs($admin)->post(route('admin.products.verification.reject', $request), ['notes' => 'Harga produk tidak sesuai.']);

        $this->assertSame($admin->id, $request->fresh()->reviewer_id);
    }

    public function test_rejection_populates_reviewed_at(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $product = $this->productFor($owner);
        $request = $this->productRequestFor($product, $owner);

        $this->actingAs($admin)->post(route('admin.products.verification.reject', $request), ['notes' => 'Harga produk tidak sesuai.']);

        $this->assertNotNull($request->fresh()->reviewed_at);
    }

    public function test_rejection_persists_notes(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $product = $this->productFor($owner);
        $request = $this->productRequestFor($product, $owner);

        $this->actingAs($admin)->post(route('admin.products.verification.reject', $request), ['notes' => 'Harga produk tidak sesuai.']);

        $this->assertSame('Harga produk tidak sesuai.', $request->fresh()->notes);
    }

    public function test_administrator_can_mark_product_needs_revision(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $product = $this->productFor($owner);
        $request = $this->productRequestFor($product, $owner);

        $this->actingAs($admin)
            ->post(route('admin.products.verification.needs-revision', $request), ['notes' => 'Tambahkan deskripsi produk.'])
            ->assertRedirect(route('admin.products.verification.index'))
            ->assertSessionHas('status');
    }

    public function test_product_becomes_needs_revision(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $product = $this->productFor($owner);
        $request = $this->productRequestFor($product, $owner);

        $this->actingAs($admin)->post(route('admin.products.verification.needs-revision', $request), ['notes' => 'Tambahkan deskripsi produk.']);

        $this->assertSame('needs_revision', $product->fresh()->status);
    }

    public function test_verification_request_becomes_needs_revision(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $product = $this->productFor($owner);
        $request = $this->productRequestFor($product, $owner);

        $this->actingAs($admin)->post(route('admin.products.verification.needs-revision', $request), ['notes' => 'Tambahkan deskripsi produk.']);

        $this->assertSame('needs_revision', $request->fresh()->status);
    }

    public function test_needs_revision_sets_reviewer_id(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $product = $this->productFor($owner);
        $request = $this->productRequestFor($product, $owner);

        $this->actingAs($admin)->post(route('admin.products.verification.needs-revision', $request), ['notes' => 'Tambahkan deskripsi produk.']);

        $this->assertSame($admin->id, $request->fresh()->reviewer_id);
    }

    public function test_needs_revision_populates_reviewed_at(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $product = $this->productFor($owner);
        $request = $this->productRequestFor($product, $owner);

        $this->actingAs($admin)->post(route('admin.products.verification.needs-revision', $request), ['notes' => 'Tambahkan deskripsi produk.']);

        $this->assertNotNull($request->fresh()->reviewed_at);
    }

    public function test_needs_revision_persists_notes(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $product = $this->productFor($owner);
        $request = $this->productRequestFor($product, $owner);

        $this->actingAs($admin)->post(route('admin.products.verification.needs-revision', $request), ['notes' => 'Tambahkan deskripsi produk.']);

        $this->assertSame('Tambahkan deskripsi produk.', $request->fresh()->notes);
    }

    public function test_owner_sees_latest_revision_note(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner);
        $product = $this->productFor($owner, 'needs_revision', $umkm);
        $this->productRequestFor($product, $owner, 'needs_revision', 'Tambahkan foto produk.');

        $this->actingAs($owner)
            ->get(route('owner.products.index', $umkm))
            ->assertSee('Catatan Administrator: Tambahkan foto produk.')
            ->assertSee('Perbaiki Produk');
    }

    public function test_approved_request_cannot_be_reviewed_again(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $product = $this->productFor($owner, 'approved');
        $request = $this->productRequestFor($product, $owner, 'approved');

        $this->actingAs($admin)
            ->from(route('admin.products.verification.index'))
            ->post(route('admin.products.verification.approve', $request))
            ->assertRedirect(route('admin.products.verification.index'))
            ->assertSessionHas('error');

        $this->assertSame('approved', $request->fresh()->status);
        $this->assertSame('approved', $product->fresh()->status);
        $this->assertDatabaseCount('verification_requests', 1);
    }

    public function test_rejected_request_cannot_be_reviewed_again(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $product = $this->productFor($owner, 'rejected');
        $request = $this->productRequestFor($product, $owner, 'rejected');

        $this->actingAs($admin)
            ->from(route('admin.products.verification.index'))
            ->post(route('admin.products.verification.approve', $request))
            ->assertRedirect(route('admin.products.verification.index'))
            ->assertSessionHas('error');

        $this->assertSame('rejected', $request->fresh()->status);
        $this->assertSame('rejected', $product->fresh()->status);
    }

    public function test_needs_revision_request_cannot_be_reviewed_again(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $product = $this->productFor($owner, 'needs_revision');
        $request = $this->productRequestFor($product, $owner, 'needs_revision');

        $this->actingAs($admin)
            ->from(route('admin.products.verification.index'))
            ->post(route('admin.products.verification.approve', $request))
            ->assertRedirect(route('admin.products.verification.index'))
            ->assertSessionHas('error');

        $this->assertSame('needs_revision', $request->fresh()->status);
        $this->assertSame('needs_revision', $product->fresh()->status);
    }

    public function test_unsupported_polymorphic_target_cannot_be_processed(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();

        $requestId = DB::table('verification_requests')->insertGetId([
            'user_id' => $owner->id,
            'verifiable_type' => Category::class,
            'verifiable_id' => $this->productCategory()->id,
            'status' => 'pending',
            'notes' => null,
            'reviewed_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $request = VerificationRequest::findOrFail($requestId);

        $this->actingAs($admin)
            ->get(route('admin.products.verification.show', $request))
            ->assertNotFound();

        $this->actingAs($admin)
            ->post(route('admin.products.verification.approve', $request))
            ->assertNotFound();
    }

    public function test_forged_verifiable_input_is_ignored(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner);
        $product = $this->productFor($owner, 'pending', $umkm);
        $request = $this->productRequestFor($product, $owner);
        $otherProduct = $this->productFor($owner, 'pending', $umkm, 'Kopi Robusta', 'kopi-robusta');

        $this->actingAs($admin)
            ->post(route('admin.products.verification.approve', $request), [
                'verifiable_type' => Umkm::class,
                'verifiable_id' => $umkm->id,
            ]);

        $this->assertSame('approved', $request->fresh()->status);
        $this->assertSame('approved', $product->fresh()->status);
        $this->assertSame('pending', $otherProduct->fresh()->status);
        $this->assertSame('approved', $umkm->fresh()->status);
        $this->assertDatabaseCount('verification_requests', 1);
    }

    public function test_reviewer_id_cannot_be_forged(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $other = User::factory()->create();
        $product = $this->productFor($owner);
        $request = $this->productRequestFor($product, $owner);

        $this->actingAs($admin)
            ->post(route('admin.products.verification.approve', $request), ['reviewer_id' => $other->id]);

        $this->assertSame($admin->id, $request->fresh()->reviewer_id);
        $this->assertNotSame($other->id, $request->fresh()->reviewer_id);
    }

    public function test_forged_status_is_ignored(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $product = $this->productFor($owner);
        $request = $this->productRequestFor($product, $owner);

        $this->actingAs($admin)
            ->post(route('admin.products.verification.needs-revision', $request), [
                'notes' => 'Tambahkan deskripsi produk.',
                'status' => 'approved',
            ]);

        $this->assertSame('needs_revision', $request->fresh()->status);
        $this->assertSame('needs_revision', $product->fresh()->status);
    }

    public function test_owner_cannot_review_through_review_endpoint(): void
    {
        $owner = $this->owner();
        $product = $this->productFor($owner);
        $request = $this->productRequestFor($product, $owner);

        $this->actingAs($owner)
            ->post(route('admin.products.verification.approve', $request))
            ->assertForbidden();

        $this->assertSame('pending', $request->fresh()->status);
        $this->assertSame('pending', $product->fresh()->status);
    }

    public function test_review_forms_include_csrf_token(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $product = $this->productFor($owner);
        $request = $this->productRequestFor($product, $owner);

        $this->actingAs($admin)
            ->get(route('admin.products.verification.show', $request))
            ->assertSee('name="_token"', false);
    }

    public function test_approval_is_atomic_when_product_update_fails(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $product = $this->productFor($owner);
        $request = $this->productRequestFor($product, $owner);

        Product::updating(function () {
            throw new \RuntimeException('forced failure');
        });

        try {
            $this->actingAs($admin)
                ->post(route('admin.products.verification.approve', $request))
                ->assertStatus(500);
        } finally {
            Product::getEventDispatcher()->forget('eloquent.updating: '.Product::class);
        }

        $this->assertSame('pending', $product->fresh()->status);
        $this->assertSame('pending', $request->fresh()->status);
        $this->assertNull($request->fresh()->reviewer_id);
        $this->assertNull($request->fresh()->reviewed_at);
        $this->assertNull($request->fresh()->notes);
    }

    public function test_rejection_is_atomic_when_product_update_fails(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $product = $this->productFor($owner);
        $request = $this->productRequestFor($product, $owner);

        Product::updating(function () {
            throw new \RuntimeException('forced failure');
        });

        try {
            $this->actingAs($admin)
                ->post(route('admin.products.verification.reject', $request), ['notes' => 'Harga produk tidak sesuai.'])
                ->assertStatus(500);
        } finally {
            Product::getEventDispatcher()->forget('eloquent.updating: '.Product::class);
        }

        $this->assertSame('pending', $product->fresh()->status);
        $this->assertSame('pending', $request->fresh()->status);
        $this->assertNull($request->fresh()->reviewer_id);
        $this->assertNull($request->fresh()->reviewed_at);
        $this->assertNull($request->fresh()->notes);
    }

    public function test_needs_revision_is_atomic_when_product_update_fails(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $product = $this->productFor($owner);
        $request = $this->productRequestFor($product, $owner);

        Product::updating(function () {
            throw new \RuntimeException('forced failure');
        });

        try {
            $this->actingAs($admin)
                ->post(route('admin.products.verification.needs-revision', $request), ['notes' => 'Tambahkan deskripsi produk.'])
                ->assertStatus(500);
        } finally {
            Product::getEventDispatcher()->forget('eloquent.updating: '.Product::class);
        }

        $this->assertSame('pending', $product->fresh()->status);
        $this->assertSame('pending', $request->fresh()->status);
        $this->assertNull($request->fresh()->reviewer_id);
        $this->assertNull($request->fresh()->reviewed_at);
        $this->assertNull($request->fresh()->notes);
    }

    public function test_rejected_product_can_be_revised_and_resubmitted_through_documented_flow(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner);
        $product = $this->productFor($owner, 'pending', $umkm);
        $request = $this->productRequestFor($product, $owner);

        $this->actingAs($admin)
            ->post(route('admin.products.verification.reject', $request), ['notes' => 'Foto produk tidak jelas.']);

        $this->assertSame('rejected', $product->fresh()->status);

        $this->actingAs($owner)
            ->put(route('owner.products.update', $product), [
                'category_id' => $this->productCategory()->id,
                'name' => 'Kopi Arabika Premium',
                'description' => 'Kopi asli Gunung Papandayan.',
                'price' => '18000',
            ]);

        $this->assertSame('draft', $product->fresh()->status);

        $this->actingAs($owner)
            ->post(route('owner.products.submit', $product));

        $this->assertSame('pending', $product->fresh()->status);
        $this->assertDatabaseCount('verification_requests', 2);

        $latest = $product->verificationRequests()->latest('id')->firstOrFail();
        $this->assertSame('pending', $latest->status);
        $this->assertNull($latest->reviewer_id);

        $this->actingAs($admin)
            ->post(route('admin.products.verification.approve', $latest));

        $this->assertSame('approved', $product->fresh()->status);
        $this->assertSame('approved', $latest->fresh()->status);

        $this->assertDatabaseHas('verification_requests', [
            'id' => $request->id,
            'status' => 'rejected',
            'notes' => 'Foto produk tidak jelas.',
        ]);
    }

    public function test_pending_review_page_hides_public_preview_link(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $product = $this->productFor($owner);
        $request = $this->productRequestFor($product, $owner);

        $this->actingAs($admin)
            ->get(route('admin.products.verification.show', $request))
            ->assertOk()
            ->assertDontSee('Lihat Halaman Publik');
    }

    public function test_approved_review_page_shows_public_preview_link(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $product = $this->productFor($owner, 'approved');
        $request = $this->productRequestFor($product, $owner, 'approved');

        $this->actingAs($admin)
            ->get(route('admin.products.verification.show', $request))
            ->assertOk()
            ->assertSee('href="'.route('public.product.show', $product).'"', false)
            ->assertSee('Lihat Halaman Publik');
    }

    public function test_atomic_approve_rejects_non_pending_product_request_via_affected_rows(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $product = $this->productFor($owner, 'approved');
        $request = $this->productRequestFor($product, $owner, 'approved');

        $this->actingAs($admin)
            ->from(route('admin.products.verification.index'))
            ->post(route('admin.products.verification.approve', $request))
            ->assertRedirect(route('admin.products.verification.index'))
            ->assertSessionHas('error');

        $this->assertSame('approved', $request->fresh()->status);
        $this->assertSame('approved', $product->fresh()->status);
    }

    public function test_atomic_reject_rejects_non_pending_product_request_via_affected_rows(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $product = $this->productFor($owner, 'rejected');
        $request = $this->productRequestFor($product, $owner, 'rejected');

        $this->actingAs($admin)
            ->from(route('admin.products.verification.index'))
            ->post(route('admin.products.verification.reject', $request), ['notes' => 'Tolak'])
            ->assertRedirect(route('admin.products.verification.index'))
            ->assertSessionHas('error');

        $this->assertSame('rejected', $request->fresh()->status);
        $this->assertSame('rejected', $product->fresh()->status);
    }

    public function test_atomic_needs_revision_rejects_non_pending_product_request_via_affected_rows(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $product = $this->productFor($owner, 'needs_revision');
        $request = $this->productRequestFor($product, $owner, 'needs_revision');

        $this->actingAs($admin)
            ->from(route('admin.products.verification.index'))
            ->post(route('admin.products.verification.needs-revision', $request), ['notes' => 'Revisi'])
            ->assertRedirect(route('admin.products.verification.index'))
            ->assertSessionHas('error');

        $this->assertSame('needs_revision', $request->fresh()->status);
        $this->assertSame('needs_revision', $product->fresh()->status);
    }
}
