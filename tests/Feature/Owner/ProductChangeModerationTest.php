<?php

namespace Tests\Feature\Owner;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductRevision;
use App\Models\Umkm;
use App\Models\User;
use App\Models\VerificationRequest;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\Models\Activity;
use Tests\TestCase;

/**
 * Phase 8: change moderation for approved products. An owner proposes
 * changes through a working copy revision; the approved product keeps
 * showing its current data on the public pages until an administrator
 * approves the revision.
 */
class ProductChangeModerationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
    }

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

    private function approvedUmkm(User $owner): Umkm
    {
        return Umkm::create([
            'user_id' => $owner->id,
            'category_id' => $this->umkmCategory()->id,
            'name' => 'Warung Maju',
            'slug' => Umkm::generateUniqueSlug('Warung Maju'),
            'status' => 'approved',
        ]);
    }

    private function approvedProduct(Umkm $umkm, array $overrides = []): Product
    {
        return Product::create(array_merge([
            'umkm_id' => $umkm->id,
            'category_id' => $this->productCategory()->id,
            'name' => 'Kopi Arabika',
            'slug' => Product::generateUniqueSlug('Kopi Arabika'),
            'description' => 'Kopi asli Gunung Papandayan.',
            'price' => 15000,
            'status' => 'approved',
        ], $overrides));
    }

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Kopi Arabika Premium',
            'category_id' => $this->productCategory()->id,
            'description' => 'Kopi premium dari Gunung Papandayan.',
            'price' => 20000,
        ], $overrides);
    }

    private function revisionFor(Product $product, User $owner, array $payload = []): ProductRevision
    {
        $payload = $payload ?: $this->validPayload();

        $this->actingAs($owner)
            ->post(route('owner.products.revisions.store', $product), $payload)
            ->assertRedirect();

        return $product->revisions()->latest('id')->first();
    }

    private function submitRevision(ProductRevision $revision, User $owner): VerificationRequest
    {
        $this->actingAs($owner)
            ->post(route('owner.products.revisions.submit', $revision))
            ->assertRedirect()
            ->assertSessionHas('status');

        return $revision->verificationRequests()->latest('id')->first();
    }

    // ---------- Akses & otorisasi ----------

    public function test_guest_cannot_open_change_form(): void
    {
        $umkm = $this->approvedUmkm($this->owner());
        $product = $this->approvedProduct($umkm);

        $this->get(route('owner.products.revisions.create', $product))
            ->assertRedirect(route('login'));
    }

    public function test_guest_cannot_store_change(): void
    {
        $umkm = $this->approvedUmkm($this->owner());
        $product = $this->approvedProduct($umkm);

        $this->post(route('owner.products.revisions.store', $product), $this->validPayload())
            ->assertRedirect(route('login'));

        $this->assertDatabaseCount('product_revisions', 0);
    }

    public function test_owner_can_open_change_form_prefilled_with_approved_data(): void
    {
        $owner = $this->owner();
        $umkm = $this->approvedUmkm($owner);
        $product = $this->approvedProduct($umkm);

        $this->actingAs($owner)
            ->get(route('owner.products.revisions.create', $product))
            ->assertOk()
            ->assertSee('Ajukan Perubahan Produk')
            ->assertSee('Kopi Arabika')
            ->assertSee('Kopi asli Gunung Papandayan.');
    }

    public function test_change_form_is_blocked_for_non_approved_product(): void
    {
        $owner = $this->owner();
        $umkm = $this->approvedUmkm($owner);
        $product = $this->approvedProduct($umkm, ['status' => 'draft']);

        $this->actingAs($owner)
            ->from(route('owner.products.index', $umkm))
            ->get(route('owner.products.revisions.create', $product))
            ->assertRedirect(route('owner.products.index', $umkm))
            ->assertSessionHas('error');
    }

    public function test_other_owner_cannot_open_or_store_change(): void
    {
        $umkm = $this->approvedUmkm($this->owner());
        $product = $this->approvedProduct($umkm);
        $other = $this->owner();

        $this->actingAs($other)
            ->get(route('owner.products.revisions.create', $product))
            ->assertForbidden();

        $this->actingAs($other)
            ->post(route('owner.products.revisions.store', $product), $this->validPayload())
            ->assertForbidden();

        $this->assertDatabaseCount('product_revisions', 0);
        $this->assertSame('Kopi Arabika', $product->fresh()->name);
    }

    public function test_administrator_cannot_use_owner_change_flow(): void
    {
        $umkm = $this->approvedUmkm($this->owner());
        $product = $this->approvedProduct($umkm);
        $admin = $this->administrator();

        $this->actingAs($admin)
            ->get(route('owner.products.revisions.create', $product))
            ->assertForbidden();

        $this->actingAs($admin)
            ->post(route('owner.products.revisions.store', $product), $this->validPayload())
            ->assertForbidden();
    }

    // ---------- Alur pengajuan perubahan ----------

    public function test_owner_can_store_change_revision_and_approved_product_stays_unchanged(): void
    {
        $owner = $this->owner();
        $umkm = $this->approvedUmkm($owner);
        $product = $this->approvedProduct($umkm);

        $this->actingAs($owner)
            ->post(route('owner.products.revisions.store', $product), $this->validPayload())
            ->assertRedirect()
            ->assertSessionHas('status');

        $this->assertDatabaseCount('product_revisions', 1);

        $revision = $product->revisions()->first();
        $this->assertSame('draft', $revision->status);
        $this->assertSame('Kopi Arabika Premium', $revision->name);
        $this->assertSame(20000.0, (float) $revision->price);

        $fresh = $product->fresh();
        $this->assertSame('approved', $fresh->status);
        $this->assertSame('Kopi Arabika', $fresh->name);
        $this->assertSame(15000.0, (float) $fresh->price);
    }

    public function test_only_one_active_revision_is_allowed(): void
    {
        $owner = $this->owner();
        $umkm = $this->approvedUmkm($owner);
        $product = $this->approvedProduct($umkm);
        $revision = $this->revisionFor($product, $owner);

        $this->actingAs($owner)
            ->post(route('owner.products.revisions.store', $product), $this->validPayload())
            ->assertRedirect(route('owner.products.revisions.edit', $revision));

        $this->assertDatabaseCount('product_revisions', 1);
    }

    public function test_owner_can_edit_change_revision(): void
    {
        $owner = $this->owner();
        $umkm = $this->approvedUmkm($owner);
        $product = $this->approvedProduct($umkm);
        $revision = $this->revisionFor($product, $owner);

        $this->actingAs($owner)
            ->put(route('owner.products.revisions.update', $revision), $this->validPayload([
                'name' => 'Kopi Arabika Special',
                'price' => 25000,
            ]))
            ->assertRedirect()
            ->assertSessionHas('status');

        $fresh = $revision->fresh();
        $this->assertSame('draft', $fresh->status);
        $this->assertSame('Kopi Arabika Special', $fresh->name);
        $this->assertSame(25000.0, (float) $fresh->price);
        $this->assertSame('Kopi Arabika', $product->fresh()->name);
    }

    public function test_pending_revision_cannot_be_edited(): void
    {
        $owner = $this->owner();
        $umkm = $this->approvedUmkm($owner);
        $product = $this->approvedProduct($umkm);
        $revision = $this->revisionFor($product, $owner);
        $this->submitRevision($revision, $owner);

        $this->actingAs($owner)
            ->from(route('owner.products.index', $umkm))
            ->get(route('owner.products.revisions.edit', $revision))
            ->assertRedirect(route('owner.products.index', $umkm))
            ->assertSessionHas('error');

        $this->actingAs($owner)
            ->from(route('owner.products.index', $umkm))
            ->put(route('owner.products.revisions.update', $revision), $this->validPayload())
            ->assertRedirect(route('owner.products.index', $umkm))
            ->assertSessionHas('error');

        $this->assertSame('pending', $revision->fresh()->status);
    }

    public function test_other_owner_cannot_edit_or_submit_change(): void
    {
        $owner = $this->owner();
        $umkm = $this->approvedUmkm($owner);
        $product = $this->approvedProduct($umkm);
        $revision = $this->revisionFor($product, $owner);
        $other = $this->owner();

        $this->actingAs($other)
            ->get(route('owner.products.revisions.edit', $revision))
            ->assertForbidden();

        $this->actingAs($other)
            ->put(route('owner.products.revisions.update', $revision), $this->validPayload())
            ->assertForbidden();

        $this->actingAs($other)
            ->post(route('owner.products.revisions.submit', $revision))
            ->assertForbidden();

        $this->assertSame('draft', $revision->fresh()->status);
        $this->assertDatabaseCount('verification_requests', 0);
    }

    // ---------- Publikasi: data lama tetap tampil ----------

    public function test_public_keeps_old_data_after_owner_edit(): void
    {
        $owner = $this->owner();
        $umkm = $this->approvedUmkm($owner);
        $product = $this->approvedProduct($umkm);
        $revision = $this->revisionFor($product, $owner);

        $this->actingAs($owner)
            ->put(route('owner.products.revisions.update', $revision), $this->validPayload([
                'name' => 'Kopi Arabika Special',
                'price' => 25000,
            ]));

        $this->get(route('public.product.show', $product))
            ->assertOk()
            ->assertSee('Kopi Arabika')
            ->assertSee('Rp 15.000')
            ->assertSee('Kopi asli Gunung Papandayan.')
            ->assertDontSee('Kopi Arabika Special')
            ->assertDontSee('Rp 25.000');
    }

    public function test_public_keeps_old_data_after_submit(): void
    {
        $owner = $this->owner();
        $umkm = $this->approvedUmkm($owner);
        $product = $this->approvedProduct($umkm);
        $revision = $this->revisionFor($product, $owner);
        $this->submitRevision($revision, $owner);

        $this->assertSame('pending', $revision->fresh()->status);

        $this->get(route('public.product.show', $product))
            ->assertOk()
            ->assertSee('Kopi Arabika')
            ->assertDontSee('Kopi Arabika Premium');
    }

    public function test_public_keeps_old_data_after_needs_revision(): void
    {
        $owner = $this->owner();
        $admin = $this->administrator();
        $umkm = $this->approvedUmkm($owner);
        $product = $this->approvedProduct($umkm);
        $revision = $this->revisionFor($product, $owner);
        $request = $this->submitRevision($revision, $owner);

        $this->actingAs($admin)
            ->post(route('admin.products.verification.needs-revision', $request), ['notes' => 'Mohon perbaiki harga.']);

        $this->assertSame('needs_revision', $revision->fresh()->status);
        $this->assertSame('approved', $product->fresh()->status);

        $this->get(route('public.product.show', $product))
            ->assertOk()
            ->assertSee('Kopi Arabika')
            ->assertDontSee('Kopi Arabika Premium');
    }

    public function test_public_keeps_old_data_after_reject(): void
    {
        $owner = $this->owner();
        $admin = $this->administrator();
        $umkm = $this->approvedUmkm($owner);
        $product = $this->approvedProduct($umkm);
        $revision = $this->revisionFor($product, $owner);
        $request = $this->submitRevision($revision, $owner);

        $this->actingAs($admin)
            ->post(route('admin.products.verification.reject', $request), ['notes' => 'Data tidak sesuai.']);

        $this->assertSame('rejected', $revision->fresh()->status);
        $this->assertSame('approved', $product->fresh()->status);

        $this->get(route('public.product.show', $product))
            ->assertOk()
            ->assertSee('Kopi Arabika')
            ->assertDontSee('Kopi Arabika Premium');
    }

    public function test_public_uses_new_data_after_approve(): void
    {
        $owner = $this->owner();
        $admin = $this->administrator();
        $umkm = $this->approvedUmkm($owner);
        $product = $this->approvedProduct($umkm);
        $revision = $this->revisionFor($product, $owner);
        $request = $this->submitRevision($revision, $owner);

        $this->actingAs($admin)
            ->post(route('admin.products.verification.approve', $request))
            ->assertRedirect()
            ->assertSessionHas('status');

        $fresh = $product->fresh();
        $this->assertSame('approved', $fresh->status);
        $this->assertSame('Kopi Arabika Premium', $fresh->name);
        $this->assertSame('Kopi premium dari Gunung Papandayan.', $fresh->description);
        $this->assertSame(20000.0, (float) $fresh->price);
        $this->assertSame('kopi-arabika-premium', $fresh->slug);
        $this->assertSame('approved', $revision->fresh()->status);

        $this->get(route('public.product.show', $fresh))
            ->assertOk()
            ->assertSee('Kopi Arabika Premium')
            ->assertSee('Rp 20.000')
            ->assertDontSee('Kopi asli Gunung Papandayan.')
            ->assertDontSee('Rp 15.000');
    }

    public function test_approve_keeps_ownership(): void
    {
        $owner = $this->owner();
        $admin = $this->administrator();
        $umkm = $this->approvedUmkm($owner);
        $product = $this->approvedProduct($umkm);
        $revision = $this->revisionFor($product, $owner);
        $request = $this->submitRevision($revision, $owner);

        $this->actingAs($admin)->post(route('admin.products.verification.approve', $request));

        $this->assertSame($umkm->id, $product->fresh()->umkm_id);
        $this->assertSame($product->id, $revision->fresh()->product_id);
        $this->assertSame($owner->id, $umkm->fresh()->user_id);
    }

    public function test_old_public_url_becomes_unavailable_after_name_change(): void
    {
        $owner = $this->owner();
        $admin = $this->administrator();
        $umkm = $this->approvedUmkm($owner);
        $product = $this->approvedProduct($umkm);
        $revision = $this->revisionFor($product, $owner);
        $request = $this->submitRevision($revision, $owner);

        $this->actingAs($admin)->post(route('admin.products.verification.approve', $request));

        $this->get(route('public.product.show', $product))->assertNotFound();
        $this->get(route('public.product.show', $product->fresh()))->assertOk();
    }

    // ---------- Resubmit ----------

    public function test_resubmit_after_needs_revision_keeps_history(): void
    {
        $owner = $this->owner();
        $admin = $this->administrator();
        $umkm = $this->approvedUmkm($owner);
        $product = $this->approvedProduct($umkm);
        $revision = $this->revisionFor($product, $owner);
        $first = $this->submitRevision($revision, $owner);

        $this->actingAs($admin)
            ->post(route('admin.products.verification.needs-revision', $first), ['notes' => 'Mohon perbaiki harga.']);

        $this->actingAs($owner)
            ->put(route('owner.products.revisions.update', $revision), $this->validPayload(['price' => 22000]));

        $this->assertSame('draft', $revision->fresh()->status);

        $this->actingAs($owner)
            ->post(route('owner.products.revisions.submit', $revision))
            ->assertRedirect();

        $this->assertSame('pending', $revision->fresh()->status);
        $this->assertDatabaseCount('verification_requests', 2);

        $previous = VerificationRequest::oldest('id')->first();
        $this->assertSame('needs_revision', $previous->status);
        $this->assertSame($admin->id, $previous->reviewer_id);
        $this->assertSame('Mohon perbaiki harga.', $previous->notes);

        $this->assertSame('approved', $product->fresh()->status);
        $this->assertSame('Kopi Arabika', $product->fresh()->name);
    }

    public function test_resubmit_after_reject_works(): void
    {
        $owner = $this->owner();
        $admin = $this->administrator();
        $umkm = $this->approvedUmkm($owner);
        $product = $this->approvedProduct($umkm);
        $revision = $this->revisionFor($product, $owner);
        $first = $this->submitRevision($revision, $owner);

        $this->actingAs($admin)
            ->post(route('admin.products.verification.reject', $first), ['notes' => 'Data tidak sesuai.']);

        $this->assertSame('rejected', $revision->fresh()->status);

        $this->actingAs($owner)
            ->put(route('owner.products.revisions.update', $revision), $this->validPayload(['name' => 'Kopi Arabika Final']));

        $this->actingAs($owner)
            ->post(route('owner.products.revisions.submit', $revision))
            ->assertRedirect();

        $this->assertDatabaseCount('verification_requests', 2);
        $this->assertSame('rejected', VerificationRequest::oldest('id')->first()->status);

        $this->actingAs($admin)
            ->post(route('admin.products.verification.approve', VerificationRequest::latest('id')->first()));

        $this->assertSame('Kopi Arabika Final', $product->fresh()->name);
    }

    // ---------- Activity log ----------

    public function test_change_submission_logs_activity_with_owner_as_causer(): void
    {
        $owner = $this->owner();
        $umkm = $this->approvedUmkm($owner);
        $product = $this->approvedProduct($umkm);
        $revision = $this->revisionFor($product, $owner);

        $this->submitRevision($revision, $owner);

        $activity = Activity::query()
            ->where('subject_type', ProductRevision::class)
            ->where('subject_id', $revision->id)
            ->where('event', 'submitted')
            ->first();

        $this->assertNotNull($activity);
        $this->assertSame($owner->id, $activity->causer_id);
        $this->assertSame('Pengajuan perubahan produk Kopi Arabika Premium dikirim untuk diperiksa', $activity->description);
    }

    public function test_review_outcomes_log_activity_with_admin_as_causer(): void
    {
        $owner = $this->owner();
        $admin = $this->administrator();
        $umkm = $this->approvedUmkm($owner);
        $product = $this->approvedProduct($umkm);
        $revision = $this->revisionFor($product, $owner);
        $request = $this->submitRevision($revision, $owner);

        $this->actingAs($admin)
            ->post(route('admin.products.verification.needs-revision', $request), ['notes' => 'Perbaiki.']);

        $this->actingAs($owner)
            ->put(route('owner.products.revisions.update', $revision), $this->validPayload());

        $this->actingAs($owner)
            ->post(route('owner.products.revisions.submit', $revision));

        $latest = VerificationRequest::latest('id')->first();

        $this->actingAs($admin)
            ->post(route('admin.products.verification.approve', $latest));

        $this->assertDatabaseHas('activity_log', [
            'subject_type' => ProductRevision::class,
            'subject_id' => $revision->id,
            'event' => 'needs_revision',
            'causer_id' => $admin->id,
            'description' => 'Perubahan produk Kopi Arabika Premium perlu diperbaiki',
        ]);

        $this->assertDatabaseHas('activity_log', [
            'subject_type' => ProductRevision::class,
            'subject_id' => $revision->id,
            'event' => 'approved',
            'causer_id' => $admin->id,
            'description' => 'Perubahan produk Kopi Arabika Premium disetujui',
        ]);
    }

    // ---------- Media perubahan ----------

    public function test_revision_photo_is_not_public_before_approve(): void
    {
        $owner = $this->owner();
        $umkm = $this->approvedUmkm($owner);
        $product = $this->approvedProduct($umkm);

        $oldPhoto = $product->media()->create([
            'disk' => 'public',
            'path' => 'products/'.$product->id.'/foto-lama.png',
            'collection' => 'product',
            'sort_order' => 0,
        ]);
        Storage::disk('public')->put($oldPhoto->path, 'lama');

        $revision = $this->revisionFor($product, $owner);

        $this->actingAs($owner)
            ->post(route('owner.products.revisions.media.store', [$revision, 'product']), [
                'file_product' => UploadedFile::fake()->image('foto-baru.png'),
            ])->assertRedirect()->assertSessionHas('status');

        $newPhoto = $revision->media()->first();
        $this->assertNotNull($newPhoto);
        $this->assertSame(ProductRevision::class, $newPhoto->mediable_type);

        $this->get(route('public.product.show', $product))
            ->assertOk()
            ->assertSee('/storage/'.$oldPhoto->path)
            ->assertDontSee('/storage/'.$newPhoto->path);
    }

    public function test_approve_replaces_public_photo_and_removes_old_file(): void
    {
        $owner = $this->owner();
        $admin = $this->administrator();
        $umkm = $this->approvedUmkm($owner);
        $product = $this->approvedProduct($umkm);

        $oldPhoto = $product->media()->create([
            'disk' => 'public',
            'path' => 'products/'.$product->id.'/foto-lama.png',
            'collection' => 'product',
            'sort_order' => 0,
        ]);
        Storage::disk('public')->put($oldPhoto->path, 'lama');

        $revision = $this->revisionFor($product, $owner);

        $this->actingAs($owner)
            ->post(route('owner.products.revisions.media.store', [$revision, 'product']), [
                'file_product' => UploadedFile::fake()->image('foto-baru.png'),
            ]);

        $newPhoto = $revision->media()->first();
        $request = $this->submitRevision($revision, $owner);

        $this->actingAs($admin)
            ->post(route('admin.products.verification.approve', $request));

        $this->assertSame(Product::class, $newPhoto->fresh()->mediable_type);
        $this->assertSame($product->id, $newPhoto->fresh()->mediable_id);

        $this->assertDatabaseCount('media', 1);
        Storage::disk('public')->assertExists($newPhoto->path);
        Storage::disk('public')->assertMissing($oldPhoto->path);

        $this->get(route('public.product.show', $product->fresh()))
            ->assertOk()
            ->assertSee('/storage/'.$newPhoto->path)
            ->assertDontSee('/storage/'.$oldPhoto->path);
    }

    public function test_owner_can_delete_revision_photo(): void
    {
        $owner = $this->owner();
        $umkm = $this->approvedUmkm($owner);
        $product = $this->approvedProduct($umkm);
        $revision = $this->revisionFor($product, $owner);

        $media = $revision->media()->create([
            'disk' => 'public',
            'path' => 'products/'.$product->id.'/revisions/'.$revision->id.'/foto.png',
            'collection' => 'product',
            'sort_order' => 0,
        ]);
        Storage::disk('public')->put($media->path, 'konten');

        $this->actingAs($owner)
            ->delete(route('owner.media.destroy', $media))
            ->assertRedirect()
            ->assertSessionHas('status');

        $this->assertDatabaseMissing('media', ['id' => $media->id]);
        Storage::disk('public')->assertMissing($media->path);
    }

    public function test_other_owner_cannot_upload_or_delete_revision_photo(): void
    {
        $owner = $this->owner();
        $umkm = $this->approvedUmkm($owner);
        $product = $this->approvedProduct($umkm);
        $revision = $this->revisionFor($product, $owner);
        $other = $this->owner();

        $this->actingAs($other)
            ->post(route('owner.products.revisions.media.store', [$revision, 'product']), [
                'file_product' => UploadedFile::fake()->image('foto.png'),
            ])->assertForbidden();

        $media = $revision->media()->create([
            'disk' => 'public',
            'path' => 'products/'.$product->id.'/revisions/'.$revision->id.'/foto.png',
            'collection' => 'product',
            'sort_order' => 0,
        ]);
        Storage::disk('public')->put($media->path, 'konten');

        $this->actingAs($other)
            ->delete(route('owner.media.destroy', $media))
            ->assertForbidden();

        $this->assertDatabaseHas('media', ['id' => $media->id]);
    }

    public function test_approve_keeps_product_without_revision_photo(): void
    {
        $owner = $this->owner();
        $admin = $this->administrator();
        $umkm = $this->approvedUmkm($owner);
        $product = $this->approvedProduct($umkm);

        $oldPhoto = $product->media()->create([
            'disk' => 'public',
            'path' => 'products/'.$product->id.'/foto-lama.png',
            'collection' => 'product',
            'sort_order' => 0,
        ]);
        Storage::disk('public')->put($oldPhoto->path, 'lama');

        $revision = $this->revisionFor($product, $owner);
        $request = $this->submitRevision($revision, $owner);

        $this->actingAs($admin)->post(route('admin.products.verification.approve', $request));

        $this->assertDatabaseCount('media', 1);
        $this->assertSame($oldPhoto->id, $product->media()->first()->id);
        Storage::disk('public')->assertExists($oldPhoto->path);

        $this->get(route('public.product.show', $product->fresh()))
            ->assertOk()
            ->assertSee('/storage/'.$oldPhoto->path);
    }

    // ---------- Initial submission tetap bekerja ----------

    public function test_initial_product_submission_flow_still_works(): void
    {
        $owner = $this->owner();
        $admin = $this->administrator();
        $umkm = $this->approvedUmkm($owner);
        $product = $this->approvedProduct($umkm, ['status' => 'draft']);

        $this->actingAs($owner)
            ->post(route('owner.products.submit', $product))
            ->assertRedirect();

        $request = $product->verificationRequests()->latest('id')->first();
        $this->assertSame('pending', $product->fresh()->status);
        $this->assertSame(Product::class, $request->verifiable_type);

        $this->actingAs($admin)
            ->post(route('admin.products.verification.approve', $request));

        $this->assertSame('approved', $product->fresh()->status);
        $this->assertDatabaseCount('product_revisions', 0);
    }

    // ---------- Admin antrean ----------

    public function test_admin_verification_index_distinguishes_change_and_initial(): void
    {
        $owner = $this->owner();
        $admin = $this->administrator();
        $umkm = $this->approvedUmkm($owner);
        $product = $this->approvedProduct($umkm);
        $revision = $this->revisionFor($product, $owner);
        $this->submitRevision($revision, $owner);

        $newProduct = $this->approvedProduct($umkm, [
            'name' => 'Es Teh Manis',
            'slug' => Product::generateUniqueSlug('Es Teh Manis'),
            'status' => 'pending',
        ]);
        $newProduct->verificationRequests()->create(['user_id' => $owner->id]);

        $this->actingAs($admin)
            ->get(route('admin.products.verification.index'))
            ->assertOk()
            ->assertSee('Perubahan')
            ->assertSee('Pengajuan Baru')
            ->assertSee('Kopi Arabika Premium')
            ->assertSee('Es Teh Manis');
    }

    public function test_admin_verification_show_displays_current_public_data_for_change(): void
    {
        $owner = $this->owner();
        $admin = $this->administrator();
        $umkm = $this->approvedUmkm($owner);
        $product = $this->approvedProduct($umkm);
        $revision = $this->revisionFor($product, $owner);
        $request = $this->submitRevision($revision, $owner);

        $this->actingAs($admin)
            ->get(route('admin.products.verification.show', $request))
            ->assertOk()
            ->assertSee('Pengajuan Perubahan')
            ->assertSee('Data Publik Saat Ini')
            ->assertSee('Kopi Arabika')
            ->assertSee('Kopi Arabika Premium');
    }
}