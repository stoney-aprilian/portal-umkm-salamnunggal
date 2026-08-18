<?php

namespace Tests\Feature\Owner;

use App\Models\Category;
use App\Models\Media;
use App\Models\Umkm;
use App\Models\UmkmRevision;
use App\Models\User;
use App\Models\VerificationRequest;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\Models\Activity;
use Tests\TestCase;

/**
 * Phase 8: change moderation for approved UMKM. An owner proposes
 * changes through a working copy revision; the approved UMKM keeps
 * showing its current data on the public pages until an administrator
 * approves the revision.
 */
class UmkmChangeModerationTest extends TestCase
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

    private function approvedUmkm(User $owner, array $overrides = []): Umkm
    {
        return Umkm::create(array_merge([
            'user_id' => $owner->id,
            'category_id' => $this->umkmCategory()->id,
            'name' => 'Warung Maju',
            'slug' => Umkm::generateUniqueSlug('Warung Maju'),
            'description' => 'Deskripsi lama.',
            'address' => 'Alamat lama.',
            'phone' => '081111111111',
            'operational_hours' => '08.00 - 17.00',
            'status' => 'approved',
        ], $overrides));
    }

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Warung Maju Baru',
            'category_id' => $this->umkmCategory()->id,
            'description' => 'Deskripsi baru.',
            'address' => 'Alamat baru.',
            'google_maps' => 'https://maps.example.com',
            'phone' => '081234567890',
            'email' => 'warung@example.com',
            'website' => 'https://warung.example.com',
            'instagram' => 'warungmaju',
            'facebook' => 'warungmaju',
            'tiktok' => 'warungmaju',
            'operational_hours' => '10.00 - 20.00',
        ], $overrides);
    }

    private function revisionFor(Umkm $umkm, User $owner, array $payload = []): UmkmRevision
    {
        $payload = $payload ?: $this->validPayload();

        $this->actingAs($owner)
            ->post(route('owner.umkm.revisions.store', $umkm), $payload)
            ->assertRedirect();

        return $umkm->revisions()->latest('id')->first();
    }

    private function submitRevision(UmkmRevision $revision, User $owner): VerificationRequest
    {
        $this->actingAs($owner)
            ->post(route('owner.umkm.revisions.submit', $revision))
            ->assertRedirect()
            ->assertSessionHas('status');

        return $revision->verificationRequests()->latest('id')->first();
    }

    // ---------- Akses & otorisasi ----------

    public function test_guest_cannot_open_change_form(): void
    {
        $umkm = $this->approvedUmkm($this->owner());

        $this->get(route('owner.umkm.revisions.create', $umkm))
            ->assertRedirect(route('login'));
    }

    public function test_guest_cannot_store_change(): void
    {
        $umkm = $this->approvedUmkm($this->owner());

        $this->post(route('owner.umkm.revisions.store', $umkm), $this->validPayload())
            ->assertRedirect(route('login'));

        $this->assertDatabaseCount('umkm_revisions', 0);
    }

    public function test_owner_can_open_change_form_prefilled_with_approved_data(): void
    {
        $owner = $this->owner();
        $umkm = $this->approvedUmkm($owner);

        $this->actingAs($owner)
            ->get(route('owner.umkm.revisions.create', $umkm))
            ->assertOk()
            ->assertSee('Ajukan Perubahan UMKM')
            ->assertSee('Warung Maju')
            ->assertSee('Deskripsi lama.')
            ->assertSee('08.00 - 17.00');
    }

    public function test_change_form_is_blocked_for_non_approved_umkm(): void
    {
        $owner = $this->owner();
        $umkm = Umkm::create([
            'user_id' => $owner->id,
            'category_id' => $this->umkmCategory()->id,
            'name' => 'Warung Maju',
            'slug' => Umkm::generateUniqueSlug('Warung Maju'),
            'status' => 'pending',
        ]);

        $this->actingAs($owner)
            ->from(route('dashboard'))
            ->get(route('owner.umkm.revisions.create', $umkm))
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('error');
    }

    public function test_other_owner_cannot_open_change_form(): void
    {
        $umkm = $this->approvedUmkm($this->owner());
        $other = $this->owner();

        $this->actingAs($other)
            ->get(route('owner.umkm.revisions.create', $umkm))
            ->assertForbidden();
    }

    public function test_other_owner_cannot_store_change(): void
    {
        $umkm = $this->approvedUmkm($this->owner());
        $other = $this->owner();

        $this->actingAs($other)
            ->post(route('owner.umkm.revisions.store', $umkm), $this->validPayload())
            ->assertForbidden();

        $this->assertDatabaseCount('umkm_revisions', 0);
        $this->assertSame('Warung Maju', $umkm->fresh()->name);
    }

    public function test_administrator_cannot_use_owner_change_flow(): void
    {
        $umkm = $this->approvedUmkm($this->owner());
        $admin = $this->administrator();

        $this->actingAs($admin)
            ->get(route('owner.umkm.revisions.create', $umkm))
            ->assertForbidden();

        $this->actingAs($admin)
            ->post(route('owner.umkm.revisions.store', $umkm), $this->validPayload())
            ->assertForbidden();
    }

    // ---------- Alur pengajuan perubahan ----------

    public function test_owner_can_store_change_revision_and_approved_umkm_stays_unchanged(): void
    {
        $owner = $this->owner();
        $umkm = $this->approvedUmkm($owner);

        $this->actingAs($owner)
            ->post(route('owner.umkm.revisions.store', $umkm), $this->validPayload())
            ->assertRedirect()
            ->assertSessionHas('status');

        $this->assertDatabaseCount('umkm_revisions', 1);

        $revision = $umkm->revisions()->first();
        $this->assertSame('draft', $revision->status);
        $this->assertSame('Warung Maju Baru', $revision->name);
        $this->assertSame('Deskripsi baru.', $revision->description);
        $this->assertSame('10.00 - 20.00', $revision->operational_hours);

        $fresh = $umkm->fresh();
        $this->assertSame('approved', $fresh->status);
        $this->assertSame('Warung Maju', $fresh->name);
        $this->assertSame('warung-maju', $fresh->slug);
        $this->assertDatabaseMissing('umkm_revisions', ['status' => 'pending']);
    }

    public function test_only_one_active_revision_is_allowed(): void
    {
        $owner = $this->owner();
        $umkm = $this->approvedUmkm($owner);
        $revision = $this->revisionFor($umkm, $owner);

        $this->actingAs($owner)
            ->post(route('owner.umkm.revisions.store', $umkm), $this->validPayload())
            ->assertRedirect(route('owner.umkm.revisions.edit', $revision));

        $this->assertDatabaseCount('umkm_revisions', 1);
    }

    public function test_forged_fields_are_ignored_when_storing_change(): void
    {
        $owner = $this->owner();
        $other = $this->owner();
        $umkm = $this->approvedUmkm($owner);

        $this->actingAs($owner)
            ->post(route('owner.umkm.revisions.store', $umkm), $this->validPayload([
                'status' => 'approved',
                'umkm_id' => 999,
                'user_id' => $other->id,
            ]));

        $revision = $umkm->revisions()->first();
        $this->assertSame('draft', $revision->status);
        $this->assertSame($umkm->id, $revision->umkm_id);
    }

    public function test_owner_can_edit_change_revision(): void
    {
        $owner = $this->owner();
        $umkm = $this->approvedUmkm($owner);
        $revision = $this->revisionFor($umkm, $owner);

        $this->actingAs($owner)
            ->put(route('owner.umkm.revisions.update', $revision), $this->validPayload([
                'name' => 'Warung Maju Edisi Baru',
                'phone' => '089999999999',
            ]))
            ->assertRedirect()
            ->assertSessionHas('status');

        $fresh = $revision->fresh();
        $this->assertSame('draft', $fresh->status);
        $this->assertSame('Warung Maju Edisi Baru', $fresh->name);
        $this->assertSame('089999999999', $fresh->phone);
        $this->assertSame('Warung Maju', $umkm->fresh()->name);
    }

    public function test_pending_revision_cannot_be_edited(): void
    {
        $owner = $this->owner();
        $umkm = $this->approvedUmkm($owner);
        $revision = $this->revisionFor($umkm, $owner);
        $this->submitRevision($revision, $owner);

        $this->actingAs($owner)
            ->from(route('dashboard'))
            ->get(route('owner.umkm.revisions.edit', $revision))
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('error');

        $this->actingAs($owner)
            ->from(route('dashboard'))
            ->put(route('owner.umkm.revisions.update', $revision), $this->validPayload())
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('error');

        $this->assertSame('pending', $revision->fresh()->status);
    }

    public function test_other_owner_cannot_edit_or_submit_change(): void
    {
        $owner = $this->owner();
        $umkm = $this->approvedUmkm($owner);
        $revision = $this->revisionFor($umkm, $owner);
        $other = $this->owner();

        $this->actingAs($other)
            ->get(route('owner.umkm.revisions.edit', $revision))
            ->assertForbidden();

        $this->actingAs($other)
            ->put(route('owner.umkm.revisions.update', $revision), $this->validPayload())
            ->assertForbidden();

        $this->actingAs($other)
            ->post(route('owner.umkm.revisions.submit', $revision))
            ->assertForbidden();

        $this->assertSame('draft', $revision->fresh()->status);
        $this->assertSame('Warung Maju Baru', $revision->fresh()->name);
        $this->assertDatabaseCount('verification_requests', 0);
    }

    // ---------- Publikasi: data lama tetap tampil ----------

    public function test_public_keeps_old_data_after_owner_edit(): void
    {
        $owner = $this->owner();
        $umkm = $this->approvedUmkm($owner);
        $revision = $this->revisionFor($umkm, $owner);

        $this->actingAs($owner)
            ->put(route('owner.umkm.revisions.update', $revision), $this->validPayload([
                'name' => 'Warung Maju Edisi Baru',
                'phone' => '089999999999',
            ]));

        $this->get(route('public.umkm.show', $umkm))
            ->assertOk()
            ->assertSee('Warung Maju')
            ->assertSee('Deskripsi lama.')
            ->assertSee('081111111111')
            ->assertDontSee('Warung Maju Edisi Baru')
            ->assertDontSee('089999999999');
    }

    public function test_public_keeps_old_data_after_submit(): void
    {
        $owner = $this->owner();
        $umkm = $this->approvedUmkm($owner);
        $revision = $this->revisionFor($umkm, $owner);
        $this->submitRevision($revision, $owner);

        $this->assertSame('pending', $revision->fresh()->status);

        $this->get(route('public.umkm.show', $umkm))
            ->assertOk()
            ->assertSee('Warung Maju')
            ->assertDontSee('Warung Maju Baru');
    }

    public function test_public_keeps_old_data_after_needs_revision(): void
    {
        $owner = $this->owner();
        $admin = $this->administrator();
        $umkm = $this->approvedUmkm($owner);
        $revision = $this->revisionFor($umkm, $owner);
        $request = $this->submitRevision($revision, $owner);

        $this->actingAs($admin)
            ->post(route('admin.umkm.verification.needs-revision', $request), ['notes' => 'Mohon perbaiki jam operasional.']);

        $this->assertSame('needs_revision', $revision->fresh()->status);
        $this->assertSame('approved', $umkm->fresh()->status);

        $this->get(route('public.umkm.show', $umkm))
            ->assertOk()
            ->assertSee('Warung Maju')
            ->assertDontSee('Warung Maju Baru');
    }

    public function test_public_keeps_old_data_after_reject(): void
    {
        $owner = $this->owner();
        $admin = $this->administrator();
        $umkm = $this->approvedUmkm($owner);
        $revision = $this->revisionFor($umkm, $owner);
        $request = $this->submitRevision($revision, $owner);

        $this->actingAs($admin)
            ->post(route('admin.umkm.verification.reject', $request), ['notes' => 'Data tidak sesuai.']);

        $this->assertSame('rejected', $revision->fresh()->status);
        $this->assertSame('approved', $umkm->fresh()->status);

        $this->get(route('public.umkm.show', $umkm))
            ->assertOk()
            ->assertSee('Warung Maju')
            ->assertDontSee('Warung Maju Baru');
    }

    public function test_public_uses_new_data_after_approve(): void
    {
        $owner = $this->owner();
        $admin = $this->administrator();
        $umkm = $this->approvedUmkm($owner);
        $revision = $this->revisionFor($umkm, $owner);
        $request = $this->submitRevision($revision, $owner);

        $this->actingAs($admin)
            ->post(route('admin.umkm.verification.approve', $request))
            ->assertRedirect()
            ->assertSessionHas('status');

        $fresh = $umkm->fresh();
        $this->assertSame('approved', $fresh->status);
        $this->assertSame('Warung Maju Baru', $fresh->name);
        $this->assertSame('Deskripsi baru.', $fresh->description);
        $this->assertSame('Alamat baru.', $fresh->address);
        $this->assertSame('081234567890', $fresh->phone);
        $this->assertSame('10.00 - 20.00', $fresh->operational_hours);
        $this->assertSame('warung-maju-baru', $fresh->slug);

        $this->assertSame('approved', $revision->fresh()->status);

        $this->get(route('public.umkm.show', $fresh))
            ->assertOk()
            ->assertSee('Warung Maju Baru')
            ->assertSee('Deskripsi baru.')
            ->assertSee('081234567890')
            ->assertDontSee('Deskripsi lama.');
    }

    public function test_approve_keeps_ownership(): void
    {
        $owner = $this->owner();
        $admin = $this->administrator();
        $umkm = $this->approvedUmkm($owner);
        $revision = $this->revisionFor($umkm, $owner);
        $request = $this->submitRevision($revision, $owner);

        $this->actingAs($admin)->post(route('admin.umkm.verification.approve', $request));

        $this->assertSame($owner->id, $umkm->fresh()->user_id);
        $this->assertSame($umkm->id, $revision->fresh()->umkm_id);
    }

    public function test_old_public_url_becomes_unavailable_after_name_change(): void
    {
        $owner = $this->owner();
        $admin = $this->administrator();
        $umkm = $this->approvedUmkm($owner);
        $revision = $this->revisionFor($umkm, $owner);
        $request = $this->submitRevision($revision, $owner);

        $this->actingAs($admin)->post(route('admin.umkm.verification.approve', $request));

        $this->get(route('public.umkm.show', $umkm))->assertNotFound();
        $this->get(route('public.umkm.show', $umkm->fresh()))->assertOk();
    }

    // ---------- Resubmit ----------

    public function test_resubmit_after_needs_revision_keeps_history(): void
    {
        $owner = $this->owner();
        $admin = $this->administrator();
        $umkm = $this->approvedUmkm($owner);
        $revision = $this->revisionFor($umkm, $owner);
        $first = $this->submitRevision($revision, $owner);

        $this->actingAs($admin)
            ->post(route('admin.umkm.verification.needs-revision', $first), ['notes' => 'Mohon perbaiki jam operasional.']);

        $this->actingAs($owner)
            ->put(route('owner.umkm.revisions.update', $revision), $this->validPayload(['operational_hours' => '09.00 - 21.00']));

        $this->assertSame('draft', $revision->fresh()->status);

        $this->actingAs($owner)
            ->post(route('owner.umkm.revisions.submit', $revision))
            ->assertRedirect(route('dashboard'));

        $this->assertSame('pending', $revision->fresh()->status);
        $this->assertDatabaseCount('verification_requests', 2);

        $previous = VerificationRequest::oldest('id')->first();
        $this->assertSame('needs_revision', $previous->status);
        $this->assertSame($admin->id, $previous->reviewer_id);
        $this->assertSame('Mohon perbaiki jam operasional.', $previous->notes);

        $latest = VerificationRequest::latest('id')->first();
        $this->assertSame('pending', $latest->status);
        $this->assertNull($latest->reviewer_id);

        $this->assertSame('approved', $umkm->fresh()->status);
        $this->assertSame('Warung Maju', $umkm->fresh()->name);
    }

    public function test_resubmit_after_reject_works(): void
    {
        $owner = $this->owner();
        $admin = $this->administrator();
        $umkm = $this->approvedUmkm($owner);
        $revision = $this->revisionFor($umkm, $owner);
        $first = $this->submitRevision($revision, $owner);

        $this->actingAs($admin)
            ->post(route('admin.umkm.verification.reject', $first), ['notes' => 'Data tidak sesuai.']);

        $this->assertSame('rejected', $revision->fresh()->status);

        $this->actingAs($owner)
            ->put(route('owner.umkm.revisions.update', $revision), $this->validPayload(['name' => 'Warung Maju Final']));

        $this->actingAs($owner)
            ->post(route('owner.umkm.revisions.submit', $revision))
            ->assertRedirect(route('dashboard'));

        $this->assertSame('pending', $revision->fresh()->status);
        $this->assertDatabaseCount('verification_requests', 2);
        $this->assertSame('rejected', VerificationRequest::oldest('id')->first()->status);

        $this->actingAs($admin)
            ->post(route('admin.umkm.verification.approve', VerificationRequest::latest('id')->first()));

        $this->assertSame('Warung Maju Final', $umkm->fresh()->name);
    }

    // ---------- Activity log ----------

    public function test_change_submission_logs_activity_with_owner_as_causer(): void
    {
        $owner = $this->owner();
        $umkm = $this->approvedUmkm($owner);
        $revision = $this->revisionFor($umkm, $owner);

        $this->submitRevision($revision, $owner);

        $activity = Activity::query()
            ->where('subject_type', UmkmRevision::class)
            ->where('subject_id', $revision->id)
            ->where('event', 'submitted')
            ->first();

        $this->assertNotNull($activity);
        $this->assertSame($owner->id, $activity->causer_id);
        $this->assertSame('Pengajuan perubahan UMKM Anda dikirim untuk diperiksa', $activity->description);
    }

    public function test_review_outcomes_log_activity_with_admin_as_causer(): void
    {
        $owner = $this->owner();
        $admin = $this->administrator();
        $umkm = $this->approvedUmkm($owner);
        $revision = $this->revisionFor($umkm, $owner);
        $request = $this->submitRevision($revision, $owner);

        $this->actingAs($admin)
            ->post(route('admin.umkm.verification.needs-revision', $request), ['notes' => 'Perbaiki.']);

        $this->actingAs($owner)
            ->put(route('owner.umkm.revisions.update', $revision), $this->validPayload());

        $this->actingAs($owner)
            ->post(route('owner.umkm.revisions.submit', $revision));

        $latest = VerificationRequest::latest('id')->first();

        $this->actingAs($admin)
            ->post(route('admin.umkm.verification.approve', $latest));

        $this->assertDatabaseHas('activity_log', [
            'subject_type' => UmkmRevision::class,
            'subject_id' => $revision->id,
            'event' => 'needs_revision',
            'causer_id' => $admin->id,
            'description' => 'Perubahan UMKM Anda perlu diperbaiki',
        ]);

        $this->assertDatabaseHas('activity_log', [
            'subject_type' => UmkmRevision::class,
            'subject_id' => $revision->id,
            'event' => 'approved',
            'causer_id' => $admin->id,
            'description' => 'Perubahan UMKM Anda disetujui',
        ]);
    }

    // ---------- Media perubahan ----------

    public function test_revision_media_is_not_public_before_approve(): void
    {
        $owner = $this->owner();
        $admin = $this->administrator();
        $umkm = $this->approvedUmkm($owner);

        $oldLogo = $umkm->media()->create([
            'disk' => 'public',
            'path' => 'umkms/'.$umkm->id.'/logo-lama.png',
            'collection' => 'logo',
            'sort_order' => 0,
        ]);
        Storage::disk('public')->put($oldLogo->path, 'lama');

        $revision = $this->revisionFor($umkm, $owner);

        $this->actingAs($owner)
            ->post(route('owner.umkm.revisions.media.store', [$revision, 'logo']), [
                'file_logo' => UploadedFile::fake()->image('logo-baru.png'),
            ])->assertRedirect()->assertSessionHas('status');

        $newLogo = $revision->media()->where('collection', 'logo')->first();
        $this->assertNotNull($newLogo);
        $this->assertSame(UmkmRevision::class, $newLogo->mediable_type);
        Storage::disk('public')->assertExists($newLogo->path);

        $this->get(route('public.umkm.show', $umkm))
            ->assertOk()
            ->assertSee('/storage/'.$oldLogo->path)
            ->assertDontSee('/storage/'.$newLogo->path);
    }

    public function test_approve_moves_revision_logo_and_removes_old_file(): void
    {
        $owner = $this->owner();
        $admin = $this->administrator();
        $umkm = $this->approvedUmkm($owner);

        $oldLogo = $umkm->media()->create([
            'disk' => 'public',
            'path' => 'umkms/'.$umkm->id.'/logo-lama.png',
            'collection' => 'logo',
            'sort_order' => 0,
        ]);
        Storage::disk('public')->put($oldLogo->path, 'lama');

        $revision = $this->revisionFor($umkm, $owner);

        $this->actingAs($owner)
            ->post(route('owner.umkm.revisions.media.store', [$revision, 'logo']), [
                'file_logo' => UploadedFile::fake()->image('logo-baru.png'),
            ]);

        $newLogo = $revision->media()->where('collection', 'logo')->first();
        $request = $this->submitRevision($revision, $owner);

        $this->actingAs($admin)
            ->post(route('admin.umkm.verification.approve', $request));

        $this->assertSame(Umkm::class, $newLogo->fresh()->mediable_type);
        $this->assertSame($umkm->id, $newLogo->fresh()->mediable_id);

        $this->assertDatabaseCount('media', 1);
        Storage::disk('public')->assertExists($newLogo->path);
        Storage::disk('public')->assertMissing($oldLogo->path);

        $this->get(route('public.umkm.show', $umkm->fresh()))
            ->assertOk()
            ->assertSee('/storage/'.$newLogo->path)
            ->assertDontSee('/storage/'.$oldLogo->path);
    }

    public function test_approve_appends_revision_gallery_to_public_gallery(): void
    {
        $owner = $this->owner();
        $admin = $this->administrator();
        $umkm = $this->approvedUmkm($owner);

        $umkm->media()->create([
            'disk' => 'public',
            'path' => 'umkms/'.$umkm->id.'/gallery/lama-1.png',
            'collection' => 'gallery',
            'sort_order' => 1,
        ]);

        $revision = $this->revisionFor($umkm, $owner);

        $this->actingAs($owner)
            ->post(route('owner.umkm.revisions.media.store', [$revision, 'gallery']), [
                'gallery' => [
                    UploadedFile::fake()->image('galeri-baru-1.png'),
                    UploadedFile::fake()->image('galeri-baru-2.png'),
                ],
            ]);

        $request = $this->submitRevision($revision, $owner);

        $this->actingAs($admin)
            ->post(route('admin.umkm.verification.approve', $request));

        $gallery = $umkm->media()->where('collection', 'gallery')->orderBy('sort_order')->get();
        $this->assertCount(3, $gallery);
        $this->assertTrue($gallery[0]->sort_order < $gallery[1]->sort_order);
        $this->assertTrue($gallery[1]->sort_order < $gallery[2]->sort_order);
        $this->assertSame(Umkm::class, $gallery[1]->mediable_type);
        $this->assertSame($umkm->id, $gallery[1]->mediable_id);
    }

    public function test_owner_can_delete_revision_media(): void
    {
        $owner = $this->owner();
        $umkm = $this->approvedUmkm($owner);
        $revision = $this->revisionFor($umkm, $owner);

        $media = $revision->media()->create([
            'disk' => 'public',
            'path' => 'umkms/'.$umkm->id.'/revisions/'.$revision->id.'/logo.png',
            'collection' => 'logo',
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

    public function test_other_owner_cannot_upload_or_delete_revision_media(): void
    {
        $owner = $this->owner();
        $umkm = $this->approvedUmkm($owner);
        $revision = $this->revisionFor($umkm, $owner);
        $other = $this->owner();

        $this->actingAs($other)
            ->post(route('owner.umkm.revisions.media.store', [$revision, 'logo']), [
                'file_logo' => UploadedFile::fake()->image('logo.png'),
            ])->assertForbidden();

        $media = $revision->media()->create([
            'disk' => 'public',
            'path' => 'umkms/'.$umkm->id.'/revisions/'.$revision->id.'/logo.png',
            'collection' => 'logo',
            'sort_order' => 0,
        ]);
        Storage::disk('public')->put($media->path, 'konten');

        $this->actingAs($other)
            ->delete(route('owner.media.destroy', $media))
            ->assertForbidden();

        $this->assertDatabaseHas('media', ['id' => $media->id]);
    }

    // ---------- Initial submission tetap bekerja ----------

    public function test_initial_submission_flow_still_works(): void
    {
        $owner = $this->owner();
        $admin = $this->administrator();
        $umkm = $this->approvedUmkm($owner, [
            'name' => 'Warung Awal',
            'slug' => Umkm::generateUniqueSlug('Warung Awal'),
            'status' => 'draft',
        ]);

        $this->actingAs($owner)
            ->post(route('owner.umkm.submit', $umkm))
            ->assertRedirect(route('dashboard'));

        $request = $umkm->verificationRequests()->latest('id')->first();
        $this->assertSame('pending', $umkm->fresh()->status);
        $this->assertSame(Umkm::class, $request->verifiable_type);

        $this->actingAs($admin)
            ->post(route('admin.umkm.verification.approve', $request));

        $this->assertSame('approved', $umkm->fresh()->status);
        $this->assertDatabaseCount('umkm_revisions', 0);
    }

    // ---------- Admin antrean ----------

    public function test_admin_verification_index_distinguishes_change_and_initial(): void
    {
        $owner = $this->owner();
        $admin = $this->administrator();
        $umkm = $this->approvedUmkm($owner);
        $revision = $this->revisionFor($umkm, $owner);
        $this->submitRevision($revision, $owner);

        $newOwner = $this->owner();
        $newUmkm = Umkm::create([
            'user_id' => $newOwner->id,
            'category_id' => $this->umkmCategory()->id,
            'name' => 'Warung Kedua',
            'slug' => Umkm::generateUniqueSlug('Warung Kedua'),
            'status' => 'pending',
        ]);
        $newUmkm->verificationRequests()->create(['user_id' => $newOwner->id]);

        $this->actingAs($admin)
            ->get(route('admin.umkm.verification.index'))
            ->assertOk()
            ->assertSee('Perubahan')
            ->assertSee('Pengajuan Baru')
            ->assertSee('Warung Maju Baru')
            ->assertSee('Warung Kedua');
    }

    public function test_admin_verification_show_displays_current_public_data_for_change(): void
    {
        $owner = $this->owner();
        $admin = $this->administrator();
        $umkm = $this->approvedUmkm($owner);
        $revision = $this->revisionFor($umkm, $owner);
        $request = $this->submitRevision($revision, $owner);

        $this->actingAs($admin)
            ->get(route('admin.umkm.verification.show', $request))
            ->assertOk()
            ->assertSee('Pengajuan Perubahan')
            ->assertSee('Data Publik Saat Ini')
            ->assertSee('Warung Maju')
            ->assertSee('Warung Maju Baru');
    }
}