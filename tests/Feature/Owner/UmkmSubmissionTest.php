<?php

namespace Tests\Feature\Owner;

use App\Models\Category;
use App\Models\Media;
use App\Models\Umkm;
use App\Models\User;
use App\Models\VerificationRequest;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UmkmSubmissionTest extends TestCase
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

    private function umkmFor(User $owner, string $status = 'draft'): Umkm
    {
        return Umkm::create([
            'user_id' => $owner->id,
            'category_id' => $this->umkmCategory()->id,
            'name' => 'Warung Maju',
            'slug' => Umkm::generateUniqueSlug('Warung Maju'),
            'status' => $status,
        ]);
    }

    public function test_guest_cannot_submit(): void
    {
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner);

        $this->post(route('owner.umkm.submit', $umkm))
            ->assertRedirect(route('login'));

        $this->assertDatabaseCount('verification_requests', 0);
        $this->assertSame('draft', $umkm->fresh()->status);
    }

    public function test_administrator_cannot_submit_through_owner_flow(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner);

        $this->actingAs($admin)
            ->post(route('owner.umkm.submit', $umkm))
            ->assertForbidden();

        $this->assertDatabaseCount('verification_requests', 0);
        $this->assertSame('draft', $umkm->fresh()->status);
    }

    public function test_owner_can_submit_their_own_draft_umkm(): void
    {
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner);

        $this->actingAs($owner)
            ->post(route('owner.umkm.submit', $umkm))
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseCount('verification_requests', 1);
    }

    public function test_umkm_status_changes_from_draft_to_pending(): void
    {
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner);

        $this->actingAs($owner)->post(route('owner.umkm.submit', $umkm));

        $this->assertSame('pending', $umkm->fresh()->status);
    }

    public function test_verification_request_is_created(): void
    {
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner);

        $this->actingAs($owner)->post(route('owner.umkm.submit', $umkm));

        $this->assertDatabaseCount('verification_requests', 1);
        $this->assertInstanceOf(VerificationRequest::class, VerificationRequest::first());
    }

    public function test_verification_request_user_id_is_authenticated_owner(): void
    {
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner);

        $this->actingAs($owner)->post(route('owner.umkm.submit', $umkm));

        $this->assertDatabaseHas('verification_requests', [
            'user_id' => $owner->id,
        ]);
    }

    public function test_verification_request_reviewer_id_is_null(): void
    {
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner);

        $this->actingAs($owner)->post(route('owner.umkm.submit', $umkm));

        $this->assertDatabaseHas('verification_requests', [
            'reviewer_id' => null,
        ]);
    }

    public function test_verification_request_status_is_pending(): void
    {
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner);

        $this->actingAs($owner)->post(route('owner.umkm.submit', $umkm));

        $this->assertDatabaseHas('verification_requests', [
            'status' => 'pending',
        ]);
    }

    public function test_verification_request_verifiable_resolves_to_correct_umkm(): void
    {
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner);

        $this->actingAs($owner)->post(route('owner.umkm.submit', $umkm));

        $request = VerificationRequest::first();

        $this->assertSame('App\\Models\\Umkm', $request->verifiable_type);
        $this->assertSame($umkm->id, $request->verifiable_id);
        $this->assertTrue($request->verifiable->is($umkm));
    }

    public function test_verification_request_notes_is_null(): void
    {
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner);

        $this->actingAs($owner)->post(route('owner.umkm.submit', $umkm));

        $this->assertDatabaseHas('verification_requests', [
            'notes' => null,
        ]);
    }

    public function test_verification_request_reviewed_at_is_null(): void
    {
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner);

        $this->actingAs($owner)->post(route('owner.umkm.submit', $umkm));

        $this->assertDatabaseHas('verification_requests', [
            'reviewed_at' => null,
        ]);
    }

    public function test_submission_does_not_create_media(): void
    {
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner);

        $this->actingAs($owner)->post(route('owner.umkm.submit', $umkm));

        $this->assertDatabaseCount('media', 0);
    }

    public function test_submission_does_not_change_user_status(): void
    {
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner);

        $this->assertSame('approved', $owner->status);

        $this->actingAs($owner)->post(route('owner.umkm.submit', $umkm));

        $this->assertSame('approved', $owner->fresh()->status);
    }

    public function test_owner_cannot_submit_another_owners_umkm(): void
    {
        $owner = $this->owner();
        $other = $this->owner();
        $umkm = $this->umkmFor($other);

        $this->actingAs($owner)
            ->post(route('owner.umkm.submit', $umkm))
            ->assertForbidden();

        $this->assertDatabaseCount('verification_requests', 0);
        $this->assertSame('draft', $umkm->fresh()->status);
    }

    public function test_pending_umkm_cannot_be_submitted_again(): void
    {
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner, 'pending');

        $this->actingAs($owner)
            ->from(route('dashboard'))
            ->post(route('owner.umkm.submit', $umkm))
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('error');

        $this->assertDatabaseCount('verification_requests', 0);
        $this->assertSame('pending', $umkm->fresh()->status);
    }

    public function test_approved_umkm_cannot_be_submitted_through_draft_endpoint(): void
    {
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner, 'approved');

        $this->actingAs($owner)
            ->from(route('dashboard'))
            ->post(route('owner.umkm.submit', $umkm))
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('error');

        $this->assertDatabaseCount('verification_requests', 0);
        $this->assertSame('approved', $umkm->fresh()->status);
    }

    public function test_rejected_umkm_cannot_be_submitted_through_draft_endpoint(): void
    {
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner, 'rejected');

        $this->actingAs($owner)
            ->from(route('dashboard'))
            ->post(route('owner.umkm.submit', $umkm))
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('error');

        $this->assertDatabaseCount('verification_requests', 0);
        $this->assertSame('rejected', $umkm->fresh()->status);
    }

    public function test_needs_revision_umkm_cannot_be_submitted_through_draft_endpoint(): void
    {
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner, 'needs_revision');

        $this->actingAs($owner)
            ->from(route('dashboard'))
            ->post(route('owner.umkm.submit', $umkm))
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('error');

        $this->assertDatabaseCount('verification_requests', 0);
        $this->assertSame('needs_revision', $umkm->fresh()->status);
    }

    public function test_submission_is_atomic_when_verification_request_creation_fails(): void
    {
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner);

        VerificationRequest::creating(function () {
            throw new \RuntimeException('forced failure');
        });

        try {
            $this->actingAs($owner)
                ->post(route('owner.umkm.submit', $umkm))
                ->assertStatus(500);
        } finally {
            VerificationRequest::getEventDispatcher()
                ->forget('eloquent.creating: '.VerificationRequest::class);
        }

        $this->assertSame('draft', $umkm->fresh()->status);
        $this->assertDatabaseCount('verification_requests', 0);
    }

    public function test_successful_submission_redirects_with_bahasa_indonesia_feedback(): void
    {
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner);

        $this->actingAs($owner)
            ->post(route('owner.umkm.submit', $umkm))
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('status', 'Pengajuan UMKM berhasil dikirim dan sedang menunggu pemeriksaan.');
    }

    public function test_submit_uses_atomic_status_check_no_duplicate_verification_request(): void
    {
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner);

        $this->actingAs($owner)->post(route('owner.umkm.submit', $umkm));

        $this->assertSame('pending', $umkm->fresh()->status);
        $this->assertDatabaseCount('verification_requests', 1);
        $this->assertDatabaseHas('verification_requests', [
            'verifiable_type' => Umkm::class,
            'verifiable_id' => $umkm->id,
            'status' => 'pending',
        ]);
    }

    public function test_non_draft_umkm_submit_returns_error_without_creating_verification_request(): void
    {
        $statuses = ['pending', 'approved', 'needs_revision', 'rejected'];

        foreach ($statuses as $index => $status) {
            $owner = $this->owner();
            $umkm = $this->umkmFor($owner, $status, 'Warung '.$status, 'warung-'.$status.'-'.$index);

            $this->actingAs($owner)
                ->from(route('dashboard'))
                ->post(route('owner.umkm.submit', $umkm))
                ->assertRedirect(route('dashboard'))
                ->assertSessionHas('error');

            $this->assertSame($status, $umkm->fresh()->status);
            $this->assertDatabaseCount('verification_requests', 0);
        }
    }
}
