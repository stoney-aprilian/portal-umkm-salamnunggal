<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Models\VerificationRequest;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Activitylog\Models\Activity;
use Tests\TestCase;

/**
 * Owner-side behaviour of the account verification lifecycle:
 * registration lands in `pending`, unapproved accounts are steered to
 * the verification notice page, needs_revision owners can fix their
 * account data and resubmit, rejected owners only see the reason, and
 * owners can never review their own or another account.
 */
class OwnerAccountVerificationTest extends TestCase
{
    use RefreshDatabase;

    private function owner(string $status = 'pending', string $email = 'owner@example.com'): User
    {
        $this->seed(DatabaseSeeder::class);

        $user = User::factory()->create([
            'email' => $email,
            'password' => 'rahasia123',
            'status' => $status,
        ]);
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

    public function test_self_registration_creates_pending_owner_with_verification_request(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->post('/register', [
            'name' => 'Owner Baru',
            'email' => 'baru@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertRedirect(route('account.verification.notice'));

        $user = User::where('email', 'baru@example.com')->firstOrFail();

        $this->assertSame('pending', $user->status);
        $this->assertTrue($user->hasRole('owner'));
        $this->assertDatabaseHas('verification_requests', [
            'user_id' => $user->id,
            'verifiable_type' => User::class,
            'verifiable_id' => $user->id,
            'status' => 'pending',
        ]);
    }

    public function test_registration_logs_submitted_activity_with_owner_as_causer(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->post('/register', [
            'name' => 'Owner Logged',
            'email' => 'logged@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $user = User::where('email', 'logged@example.com')->firstOrFail();

        $this->assertDatabaseHas('activity_log', [
            'event' => 'submitted',
            'subject_type' => User::class,
            'subject_id' => $user->id,
            'causer_id' => $user->id,
            'description' => 'Pengajuan verifikasi akun Anda dikirim untuk diperiksa',
        ]);
    }

    public function test_pending_owner_cannot_access_owner_area_or_profile(): void
    {
        $owner = $this->owner('pending');

        $this->actingAs($owner)
            ->get(route('owner.umkm.create'))
            ->assertRedirect(route('account.verification.notice'));

        $this->actingAs($owner)
            ->get(route('profile.edit'))
            ->assertRedirect(route('account.verification.notice'));
    }

    public function test_pending_owner_receives_waiting_feedback_on_notice_page(): void
    {
        $owner = $this->owner('pending');

        $this->actingAs($owner)
            ->get(route('account.verification.notice'))
            ->assertOk()
            ->assertSee('Akun Anda sedang menunggu verifikasi Administrator')
            ->assertDontSee('Perbaiki Data Akun');
    }

    public function test_login_redirects_pending_owner_to_verification_notice(): void
    {
        $this->owner('pending');

        $this->post('/login', [
            'email' => 'owner@example.com',
            'password' => 'rahasia123',
        ])->assertRedirect(route('account.verification.notice'));

        $this->assertAuthenticated();
    }

    public function test_approved_owner_login_still_reaches_dashboard(): void
    {
        $this->owner('approved');

        $this->post('/login', [
            'email' => 'owner@example.com',
            'password' => 'rahasia123',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticated();
    }

    public function test_approved_owner_visiting_notice_is_sent_to_dashboard(): void
    {
        $owner = $this->owner('approved');

        $this->actingAs($owner)
            ->get(route('account.verification.notice'))
            ->assertRedirect(route('dashboard'));
    }

    public function test_needs_revision_owner_receives_admin_notes_and_actions(): void
    {
        $owner = $this->owner('needs_revision');

        $owner->verificationRequests()->create([
            'user_id' => $owner->id,
            'status' => 'needs_revision',
            'reviewer_id' => $this->administrator()->id,
            'notes' => 'Mohon lengkapi nomor telepon.',
            'reviewed_at' => now(),
        ]);

        $this->actingAs($owner)
            ->get(route('account.verification.notice'))
            ->assertOk()
            ->assertSee('Akun Anda perlu diperbaiki')
            ->assertSee('Mohon lengkapi nomor telepon.')
            ->assertSee('Perbaiki Data Akun')
            ->assertSee('Ajukan Kembali untuk Verifikasi');
    }

    public function test_needs_revision_owner_can_fix_account_data(): void
    {
        $owner = $this->owner('needs_revision');

        $this->actingAs($owner)
            ->put(route('account.verification.update'), [
                'name' => 'Nama Baru Owner',
                'email' => 'email-baru@example.com',
                'phone' => '081234567890',
            ])->assertRedirect(route('account.verification.notice'));

        $fresh = $owner->fresh();

        $this->assertSame('Nama Baru Owner', $fresh->name);
        $this->assertSame('email-baru@example.com', $fresh->email);
        $this->assertSame('081234567890', $fresh->phone);
        $this->assertSame('needs_revision', $fresh->status);
    }

    public function test_needs_revision_owner_update_rejects_duplicate_email(): void
    {
        $this->owner('approved', 'pemilik-lain@example.com');
        $owner = $this->owner('needs_revision');

        $this->actingAs($owner)
            ->put(route('account.verification.update'), [
                'name' => 'Nama Baru Owner',
                'email' => 'pemilik-lain@example.com',
            ])->assertSessionHasErrors('email');
    }

    public function test_needs_revision_owner_cannot_fix_data_or_resubmit_when_account_is_rejected(): void
    {
        $owner = $this->owner('rejected');

        $this->actingAs($owner)
            ->get(route('account.verification.edit'))
            ->assertRedirect(route('account.verification.notice'));

        $this->actingAs($owner)
            ->post(route('account.verification.submit'))
            ->assertRedirect(route('account.verification.notice'));

        $this->assertSame('rejected', $owner->fresh()->status);
    }

    public function test_needs_revision_owner_resubmission_moves_account_back_to_pending(): void
    {
        $owner = $this->owner('needs_revision');

        $owner->verificationRequests()->create([
            'user_id' => $owner->id,
            'status' => 'needs_revision',
            'reviewer_id' => $this->administrator()->id,
            'notes' => 'Mohon lengkapi nomor telepon.',
            'reviewed_at' => now(),
        ]);

        $this->actingAs($owner)
            ->post(route('account.verification.submit'))
            ->assertRedirect(route('account.verification.notice'));

        $this->assertSame('pending', $owner->fresh()->status);
        $this->assertDatabaseHas('verification_requests', [
            'user_id' => $owner->id,
            'verifiable_type' => User::class,
            'verifiable_id' => $owner->id,
            'status' => 'pending',
        ]);
        $this->assertSame(2, VerificationRequest::count());
    }

    public function test_needs_revision_owner_resubmission_is_logged(): void
    {
        $owner = $this->owner('needs_revision');

        $this->actingAs($owner)
            ->post(route('account.verification.submit'));

        $this->assertDatabaseHas('activity_log', [
            'event' => 'submitted',
            'subject_type' => User::class,
            'subject_id' => $owner->id,
            'causer_id' => $owner->id,
            'description' => 'Pengajuan verifikasi akun Anda dikirim untuk diperiksa',
        ]);
    }

    public function test_pending_owner_cannot_resubmit(): void
    {
        $owner = $this->owner('pending');

        $owner->verificationRequests()->create([
            'user_id' => $owner->id,
            'status' => 'pending',
        ]);

        $this->actingAs($owner)
            ->post(route('account.verification.submit'))
            ->assertRedirect(route('account.verification.notice'));

        $this->assertSame('pending', $owner->fresh()->status);
        $this->assertSame(1, VerificationRequest::count());
    }

    public function test_rejected_owner_sees_rejection_reason_on_notice_page(): void
    {
        $owner = $this->owner('rejected');

        $owner->verificationRequests()->create([
            'user_id' => $owner->id,
            'status' => 'rejected',
            'reviewer_id' => $this->administrator()->id,
            'notes' => 'Profil tidak memenuhi ketentuan.',
            'reviewed_at' => now(),
        ]);

        $this->actingAs($owner)
            ->get(route('account.verification.notice'))
            ->assertOk()
            ->assertSee('Akun Anda tidak disetujui')
            ->assertSee('Profil tidak memenuhi ketentuan.')
            ->assertDontSee('Perbaiki Data Akun')
            ->assertDontSee('Ajukan Kembali untuk Verifikasi');
    }

    public function test_login_redirects_rejected_owner_to_verification_notice(): void
    {
        $this->owner('rejected');

        $this->post('/login', [
            'email' => 'owner@example.com',
            'password' => 'rahasia123',
        ])->assertRedirect(route('account.verification.notice'));
    }

    public function test_owner_cannot_reach_admin_review_routes(): void
    {
        $owner = $this->owner('pending');

        $request = $owner->verificationRequests()->create([
            'user_id' => $owner->id,
            'status' => 'pending',
        ]);

        $this->actingAs($owner)
            ->get(route('admin.owner-verification.index'))
            ->assertForbidden();

        $this->actingAs($owner)
            ->get(route('admin.owner-verification.show', $request))
            ->assertForbidden();

        $this->actingAs($owner)
            ->post(route('admin.owner-verification.approve', $request))
            ->assertForbidden();

        $this->assertSame('pending', $owner->fresh()->status);
    }

    public function test_guest_is_redirected_to_login_from_notice_page(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->get(route('account.verification.notice'))
            ->assertRedirect(route('login'));
    }

    public function test_pending_owner_can_still_browse_public_portal(): void
    {
        $owner = $this->owner('pending');

        $this->actingAs($owner)
            ->get(route('public.umkm.index'))
            ->assertOk();

        $this->actingAs($owner)
            ->get(route('home'))
            ->assertOk();
    }

    public function test_notice_page_never_exposes_password_or_internal_fields(): void
    {
        $owner = $this->owner('needs_revision', 'aman@example.com');

        $response = $this->actingAs($owner)
            ->get(route('account.verification.notice'))
            ->assertOk();

        $this->assertStringNotContainsString('rahasia123', $response->getContent());
        $this->assertStringNotContainsString('password', $response->getContent());
        $this->assertStringNotContainsString('App\\Models', $response->getContent());
        $this->assertStringNotContainsString('activity_log', $response->getContent());
        $this->assertStringNotContainsString('causer', $response->getContent());

        $this->assertSame(0, Activity::where('properties', 'like', '%rahasia123%')->count());
    }

    public function test_registration_does_not_break_existing_role_field_behaviour(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->post('/register', [
            'name' => 'Role User',
            'email' => 'role@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'administrator',
        ]);

        $user = User::where('email', 'role@example.com')->firstOrFail();

        $this->assertTrue($user->hasExactRoles('owner'));
        $this->assertSame('pending', $user->status);
    }
}