<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Umkm;
use App\Models\User;
use App\Models\VerificationRequest;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Activitylog\Models\Activity;
use Tests\TestCase;

/**
 * Administrator review flow for owner account verification
 * (Self-Service registrations). Reviews only change the account status:
 * roles, UMKM ownership, and passwords are never touched, and every
 * decision is logged with the Administrator as causer.
 */
class OwnerVerificationTest extends TestCase
{
    use RefreshDatabase;

    private function administrator(): User
    {
        $this->seed(DatabaseSeeder::class);

        $user = User::factory()->create(['status' => 'approved']);
        $user->assignRole('administrator');

        return $user;
    }

    private function pendingOwner(string $email = 'owner@example.com'): User
    {
        $this->seed(DatabaseSeeder::class);

        $user = User::factory()->create([
            'email' => $email,
            'phone' => '081234567890',
            'password' => 'rahasia123',
            'status' => 'pending',
        ]);
        $user->assignRole('owner');

        $user->verificationRequests()->create([
            'user_id' => $user->id,
            'status' => 'pending',
        ]);

        return $user;
    }

    private function pendingRequest(User $owner): VerificationRequest
    {
        return $owner->verificationRequests()->firstOrFail();
    }

    public function test_admin_can_list_pending_owner_verifications(): void
    {
        $admin = $this->administrator();

        $ownerA = $this->pendingOwner('owner-a@example.com');
        $ownerB = $this->pendingOwner('owner-b@example.com');

        $approvedOwner = User::factory()->create(['status' => 'approved', 'email' => 'owner-c@example.com']);
        $approvedOwner->assignRole('owner');

        $this->actingAs($admin)
            ->get(route('admin.owner-verification.index'))
            ->assertOk()
            ->assertSee('owner-a@example.com')
            ->assertSee('owner-b@example.com')
            ->assertDontSee('owner-c@example.com');

        $this->assertSame(2, VerificationRequest::where('verifiable_type', User::class)->where('status', 'pending')->count());
    }

    public function test_admin_can_open_owner_verification_detail(): void
    {
        $admin = $this->administrator();
        $owner = $this->pendingOwner();
        $request = $this->pendingRequest($owner);

        $this->actingAs($admin)
            ->get(route('admin.owner-verification.show', $request))
            ->assertOk()
            ->assertSee($owner->name)
            ->assertSee('owner@example.com')
            ->assertSee('081234567890')
            ->assertSee('Data Akun')
            ->assertSee('Tindakan Pemeriksaan');
    }

    public function test_admin_detail_shows_umkm_business_data_as_context(): void
    {
        $admin = $this->administrator();
        $owner = $this->pendingOwner();
        $request = $this->pendingRequest($owner);

        Umkm::create([
            'user_id' => $owner->id,
            'category_id' => Category::firstOrCreate(['type' => 'umkm', 'name' => 'Kuliner', 'slug' => 'kuliner'])->id,
            'name' => 'Warung Konteks',
            'slug' => 'warung-konteks',
            'status' => 'draft',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.owner-verification.show', $request))
            ->assertOk()
            ->assertSee('Data UMKM (Konteks)')
            ->assertSee('Warung Konteks');
    }

    public function test_admin_approves_owner_account(): void
    {
        $admin = $this->administrator();
        $owner = $this->pendingOwner();
        $request = $this->pendingRequest($owner);

        $this->actingAs($admin)
            ->post(route('admin.owner-verification.approve', $request))
            ->assertRedirect(route('admin.owner-verification.index'));

        $this->assertSame('approved', $owner->fresh()->status);
        $this->assertDatabaseHas('verification_requests', [
            'id' => $request->id,
            'status' => 'approved',
            'reviewer_id' => $admin->id,
        ]);
        $this->assertNotNull($request->fresh()->reviewed_at);
    }

    public function test_approved_owner_can_login_and_use_owner_area(): void
    {
        $admin = $this->administrator();
        $owner = $this->pendingOwner();
        $request = $this->pendingRequest($owner);

        $this->actingAs($admin)->post(route('admin.owner-verification.approve', $request));

        $this->post('/login', [
            'email' => 'owner@example.com',
            'password' => 'rahasia123',
        ])->assertRedirect(route('dashboard'));

        $this->actingAs($owner->fresh())
            ->get(route('dashboard'))
            ->assertOk();
    }

    public function test_admin_marks_owner_account_needs_revision(): void
    {
        $admin = $this->administrator();
        $owner = $this->pendingOwner();
        $request = $this->pendingRequest($owner);

        $this->actingAs($admin)
            ->post(route('admin.owner-verification.needs-revision', $request), [
                'notes' => 'Mohon lengkapi nomor telepon.',
            ])->assertRedirect(route('admin.owner-verification.index'));

        $this->assertSame('needs_revision', $owner->fresh()->status);
        $this->assertDatabaseHas('verification_requests', [
            'id' => $request->id,
            'status' => 'needs_revision',
            'notes' => 'Mohon lengkapi nomor telepon.',
            'reviewer_id' => $admin->id,
        ]);

        $this->actingAs($owner->fresh())
            ->get(route('account.verification.notice'))
            ->assertOk()
            ->assertSee('Mohon lengkapi nomor telepon.');
    }

    public function test_admin_rejects_owner_account_and_owner_sees_reason(): void
    {
        $admin = $this->administrator();
        $owner = $this->pendingOwner();
        $request = $this->pendingRequest($owner);

        $this->actingAs($admin)
            ->post(route('admin.owner-verification.reject', $request), [
                'notes' => 'Profil tidak memenuhi ketentuan.',
            ])->assertRedirect(route('admin.owner-verification.index'));

        $this->assertSame('rejected', $owner->fresh()->status);
        $this->assertDatabaseHas('verification_requests', [
            'id' => $request->id,
            'status' => 'rejected',
            'notes' => 'Profil tidak memenuhi ketentuan.',
            'reviewer_id' => $admin->id,
        ]);

        $this->actingAs($owner->fresh())
            ->get(route('account.verification.notice'))
            ->assertOk()
            ->assertSee('Profil tidak memenuhi ketentuan.');
    }

    public function test_review_requires_notes_for_rejection_and_revision(): void
    {
        $admin = $this->administrator();
        $owner = $this->pendingOwner();
        $request = $this->pendingRequest($owner);

        $this->actingAs($admin)
            ->post(route('admin.owner-verification.reject', $request), [])
            ->assertSessionHasErrors('notes', null, 'reject');

        $this->actingAs($admin)
            ->post(route('admin.owner-verification.needs-revision', $request), [])
            ->assertSessionHasErrors('notes', null, 'revision');

        $this->assertSame('pending', $owner->fresh()->status);
    }

    public function test_reviewed_request_cannot_be_reviewed_again(): void
    {
        $admin = $this->administrator();
        $owner = $this->pendingOwner();
        $request = $this->pendingRequest($owner);

        $this->actingAs($admin)->post(route('admin.owner-verification.approve', $request));

        $this->actingAs($admin)
            ->post(route('admin.owner-verification.reject', $request), ['notes' => 'Terlambat.'])
            ->assertRedirect()->assertSessionHas('error');

        $this->assertSame('approved', $owner->fresh()->status);
    }

    public function test_admin_review_logs_activity_with_admin_as_causer_and_user_as_subject(): void
    {
        $admin = $this->administrator();

        $owner = $this->pendingOwner();
        $request = $this->pendingRequest($owner);

        $this->actingAs($admin)->post(route('admin.owner-verification.approve', $request));

        $this->assertDatabaseHas('activity_log', [
            'event' => 'approved',
            'subject_type' => User::class,
            'subject_id' => $owner->id,
            'causer_id' => $admin->id,
            'description' => 'Akun Anda disetujui',
        ]);

        $revisionOwner = $this->pendingOwner('revisi@example.com');
        $revisionRequest = $this->pendingRequest($revisionOwner);
        $this->actingAs($admin)->post(route('admin.owner-verification.needs-revision', $revisionRequest), ['notes' => 'Perbaiki.']);

        $this->assertDatabaseHas('activity_log', [
            'event' => 'needs_revision',
            'subject_type' => User::class,
            'subject_id' => $revisionOwner->id,
            'causer_id' => $admin->id,
            'description' => 'Akun Anda perlu diperbaiki',
        ]);

        $rejectedOwner = $this->pendingOwner('tolak@example.com');
        $rejectedRequest = $this->pendingRequest($rejectedOwner);
        $this->actingAs($admin)->post(route('admin.owner-verification.reject', $rejectedRequest), ['notes' => 'Ditolak.']);

        $this->assertDatabaseHas('activity_log', [
            'event' => 'rejected',
            'subject_type' => User::class,
            'subject_id' => $rejectedOwner->id,
            'causer_id' => $admin->id,
            'description' => 'Akun Anda ditolak',
        ]);

        $this->assertSame(3, Activity::where('subject_type', User::class)->whereIn('event', ['approved', 'needs_revision', 'rejected'])->count());
    }

    public function test_review_never_touches_role_umkm_ownership_or_password(): void
    {
        $admin = $this->administrator();
        $owner = $this->pendingOwner();
        $request = $this->pendingRequest($owner);

        $umkm = Umkm::create([
            'user_id' => $owner->id,
            'category_id' => Category::firstOrCreate(['type' => 'umkm', 'name' => 'Kuliner', 'slug' => 'kuliner'])->id,
            'name' => 'Warung Pemilik',
            'slug' => 'warung-pemilik',
            'status' => 'draft',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.owner-verification.approve', $request));

        $this->assertTrue($owner->fresh()->hasExactRoles('owner'));
        $this->assertSame($owner->id, $umkm->fresh()->user_id);
        $this->assertNotSame($admin->id, $umkm->fresh()->user_id);
    }

    public function test_owner_verification_flow_rejects_non_owner_accounts(): void
    {
        $reviewerAdmin = $this->administrator();

        $target = User::factory()->create(['status' => 'pending', 'email' => 'administrator-x@example.com']);
        $target->assignRole('administrator');

        $request = $target->verificationRequests()->create([
            'user_id' => $target->id,
            'status' => 'pending',
        ]);

        $this->actingAs($reviewerAdmin)
            ->get(route('admin.owner-verification.show', $request))
            ->assertNotFound();
    }

    public function test_owner_verification_flow_rejects_umkm_requests(): void
    {
        $admin = $this->administrator();
        $owner = $this->pendingOwner();

        $umkm = Umkm::create([
            'user_id' => $owner->id,
            'category_id' => Category::firstOrCreate(['type' => 'umkm', 'name' => 'Kuliner', 'slug' => 'kuliner'])->id,
            'name' => 'Warung UMKM',
            'slug' => 'warung-umkm',
            'status' => 'pending',
        ]);

        $request = $umkm->verificationRequests()->create([
            'user_id' => $owner->id,
            'status' => 'pending',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.owner-verification.show', $request))
            ->assertNotFound();

        $this->actingAs($admin)
            ->post(route('admin.owner-verification.approve', $request))
            ->assertNotFound();
    }

    public function test_admin_dashboard_counts_pending_owner_verifications(): void
    {
        $admin = $this->administrator();

        $this->pendingOwner('owner-a@example.com');
        $this->pendingOwner('owner-b@example.com');

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Owner menunggu verifikasi')
            ->assertSee(route('admin.owner-verification.index'));
    }

    public function test_admin_review_flow_never_exposes_password_or_internal_fields(): void
    {
        $admin = $this->administrator();
        $owner = $this->pendingOwner('aman-admin@example.com');
        $request = $this->pendingRequest($owner);

        $response = $this->actingAs($admin)
            ->get(route('admin.owner-verification.show', $request))
            ->assertOk();

        $this->assertStringNotContainsString('rahasia123', $response->getContent());
        $this->assertStringNotContainsString('password', $response->getContent());
        $this->assertStringNotContainsString('App\\Models', $response->getContent());

        $this->assertSame(0, Activity::where('properties', 'like', '%rahasia123%')->count());
    }
}