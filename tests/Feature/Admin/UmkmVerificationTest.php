<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Product;
use App\Models\Umkm;
use App\Models\User;
use App\Models\VerificationRequest;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UmkmVerificationTest extends TestCase
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

    private function umkmFor(User $owner, string $status = 'pending'): Umkm
    {
        return Umkm::create([
            'user_id' => $owner->id,
            'category_id' => $this->umkmCategory()->id,
            'name' => 'Warung Maju',
            'slug' => Umkm::generateUniqueSlug('Warung Maju'),
            'status' => $status,
        ]);
    }

    private function umkmRequestFor(Umkm $umkm, User $owner, string $status = 'pending'): VerificationRequest
    {
        return $umkm->verificationRequests()->create([
            'user_id' => $owner->id,
            'reviewer_id' => null,
            'status' => $status,
            'notes' => null,
            'reviewed_at' => null,
        ]);
    }

    private function productRequestFor(User $owner): VerificationRequest
    {
        $umkm = $this->umkmFor($owner);

        $product = Product::create([
            'umkm_id' => $umkm->id,
            'category_id' => $this->productCategory()->id,
            'name' => 'Kopi Arabika',
            'slug' => 'kopi-arabika',
            'price' => 15000,
            'status' => 'pending',
        ]);

        return $product->verificationRequests()->create([
            'user_id' => $owner->id,
            'status' => 'pending',
        ]);
    }

    public function test_guest_cannot_access_queue(): void
    {
        $this->get(route('admin.umkm.verification.index'))->assertRedirect(route('login'));
    }

    public function test_owner_cannot_access_queue(): void
    {
        $owner = $this->owner();

        $this->actingAs($owner)
            ->get(route('admin.umkm.verification.index'))
            ->assertForbidden();
    }

    public function test_administrator_can_access_queue(): void
    {
        $admin = $this->administrator();

        $this->actingAs($admin)
            ->get(route('admin.umkm.verification.index'))
            ->assertOk()
            ->assertSee('Verifikasi UMKM');
    }

    public function test_owner_cannot_open_review_detail(): void
    {
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner);
        $request = $this->umkmRequestFor($umkm, $owner);

        $this->actingAs($owner)
            ->get(route('admin.umkm.verification.show', $request))
            ->assertForbidden();
    }

    public function test_administrator_can_open_pending_umkm_review(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner);
        $request = $this->umkmRequestFor($umkm, $owner);

        $this->actingAs($admin)
            ->get(route('admin.umkm.verification.show', $request))
            ->assertOk()
            ->assertSee('Warung Maju')
            ->assertSee($owner->name)
            ->assertSee('Setujui')
            ->assertSee('Tolak')
            ->assertSee('Perlu Revisi');
    }

    public function test_pending_umkm_requests_appear_in_queue(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner);
        $this->umkmRequestFor($umkm, $owner);

        $this->actingAs($admin)
            ->get(route('admin.umkm.verification.index'))
            ->assertOk()
            ->assertSee('Warung Maju')
            ->assertSee($owner->name)
            ->assertSee('Menunggu Pemeriksaan')
            ->assertSee('Periksa');
    }

    public function test_approved_requests_do_not_appear_in_queue(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner);
        $this->umkmRequestFor($umkm, $owner, 'approved');

        $this->actingAs($admin)
            ->get(route('admin.umkm.verification.index'))
            ->assertOk()
            ->assertDontSee('Warung Maju');
    }

    public function test_rejected_requests_do_not_appear_in_queue(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner);
        $this->umkmRequestFor($umkm, $owner, 'rejected');

        $this->actingAs($admin)
            ->get(route('admin.umkm.verification.index'))
            ->assertOk()
            ->assertDontSee('Warung Maju');
    }

    public function test_needs_revision_requests_do_not_appear_in_queue(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner);
        $this->umkmRequestFor($umkm, $owner, 'needs_revision');

        $this->actingAs($admin)
            ->get(route('admin.umkm.verification.index'))
            ->assertOk()
            ->assertDontSee('Warung Maju');
    }

    public function test_product_verification_requests_do_not_appear_in_umkm_queue(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $this->productRequestFor($owner);

        $this->actingAs($admin)
            ->get(route('admin.umkm.verification.index'))
            ->assertOk()
            ->assertDontSee('Kopi Arabika');
    }

    public function test_administrator_can_approve_pending_umkm(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner);
        $request = $this->umkmRequestFor($umkm, $owner);

        $this->actingAs($admin)
            ->post(route('admin.umkm.verification.approve', $request))
            ->assertRedirect(route('admin.umkm.verification.index'))
            ->assertSessionHas('status');
    }

    public function test_umkm_becomes_approved_after_approval(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner);
        $request = $this->umkmRequestFor($umkm, $owner);

        $this->actingAs($admin)->post(route('admin.umkm.verification.approve', $request));

        $this->assertSame('approved', $umkm->fresh()->status);
    }

    public function test_verification_request_becomes_approved_after_approval(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner);
        $request = $this->umkmRequestFor($umkm, $owner);

        $this->actingAs($admin)->post(route('admin.umkm.verification.approve', $request));

        $this->assertSame('approved', $request->fresh()->status);
    }

    public function test_approval_sets_reviewer_id_to_authenticated_administrator(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner);
        $request = $this->umkmRequestFor($umkm, $owner);

        $this->actingAs($admin)->post(route('admin.umkm.verification.approve', $request));

        $this->assertSame($admin->id, $request->fresh()->reviewer_id);
    }

    public function test_approval_populates_reviewed_at(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner);
        $request = $this->umkmRequestFor($umkm, $owner);

        $this->actingAs($admin)->post(route('admin.umkm.verification.approve', $request));

        $this->assertNotNull($request->fresh()->reviewed_at);
    }

    public function test_approval_does_not_change_owner_or_user(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner);
        $request = $this->umkmRequestFor($umkm, $owner);

        $this->actingAs($admin)->post(route('admin.umkm.verification.approve', $request));

        $this->assertSame($owner->id, $umkm->fresh()->user_id);
        $this->assertSame($owner->id, $request->fresh()->user_id);
        $this->assertSame('pending', $owner->fresh()->status);
    }

    public function test_approval_does_not_create_media(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner);
        $request = $this->umkmRequestFor($umkm, $owner);

        $this->actingAs($admin)->post(route('admin.umkm.verification.approve', $request));

        $this->assertDatabaseCount('media', 0);
    }

    public function test_rejection_requires_notes(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner);
        $request = $this->umkmRequestFor($umkm, $owner);

        $this->actingAs($admin)
            ->from(route('admin.umkm.verification.show', $request))
            ->post(route('admin.umkm.verification.reject', $request), ['notes' => ''])
            ->assertSessionHasErrors('notes', null, 'reject');

        $this->assertSame('pending', $request->fresh()->status);
        $this->assertSame('pending', $umkm->fresh()->status);
    }

    public function test_notes_validation_error_is_displayed_on_review_page(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner);
        $request = $this->umkmRequestFor($umkm, $owner);

        $this->actingAs($admin)
            ->from(route('admin.umkm.verification.show', $request))
            ->post(route('admin.umkm.verification.reject', $request), ['notes' => ''])
            ->assertSessionHasErrors('notes', null, 'reject');

        $this->withCookie(session()->getName(), session()->getId())
            ->get(route('admin.umkm.verification.show', $request))
            ->assertOk()
            ->assertSee('Catatan wajib diisi.');
    }

    public function test_administrator_can_reject_with_notes(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner);
        $request = $this->umkmRequestFor($umkm, $owner);

        $this->actingAs($admin)
            ->post(route('admin.umkm.verification.reject', $request), ['notes' => 'Logo kurang jelas.'])
            ->assertRedirect(route('admin.umkm.verification.index'))
            ->assertSessionHas('status');
    }

    public function test_umkm_becomes_rejected_after_rejection(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner);
        $request = $this->umkmRequestFor($umkm, $owner);

        $this->actingAs($admin)->post(route('admin.umkm.verification.reject', $request), ['notes' => 'Logo kurang jelas.']);

        $this->assertSame('rejected', $umkm->fresh()->status);
    }

    public function test_verification_request_becomes_rejected_after_rejection(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner);
        $request = $this->umkmRequestFor($umkm, $owner);

        $this->actingAs($admin)->post(route('admin.umkm.verification.reject', $request), ['notes' => 'Logo kurang jelas.']);

        $this->assertSame('rejected', $request->fresh()->status);
    }

    public function test_rejection_sets_reviewer_id(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner);
        $request = $this->umkmRequestFor($umkm, $owner);

        $this->actingAs($admin)->post(route('admin.umkm.verification.reject', $request), ['notes' => 'Logo kurang jelas.']);

        $this->assertSame($admin->id, $request->fresh()->reviewer_id);
    }

    public function test_rejection_populates_reviewed_at(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner);
        $request = $this->umkmRequestFor($umkm, $owner);

        $this->actingAs($admin)->post(route('admin.umkm.verification.reject', $request), ['notes' => 'Logo kurang jelas.']);

        $this->assertNotNull($request->fresh()->reviewed_at);
    }

    public function test_rejection_persists_notes(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner);
        $request = $this->umkmRequestFor($umkm, $owner);

        $this->actingAs($admin)->post(route('admin.umkm.verification.reject', $request), ['notes' => 'Logo kurang jelas.']);

        $this->assertSame('Logo kurang jelas.', $request->fresh()->notes);
    }

    public function test_needs_revision_requires_notes(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner);
        $request = $this->umkmRequestFor($umkm, $owner);

        $this->actingAs($admin)
            ->from(route('admin.umkm.verification.show', $request))
            ->post(route('admin.umkm.verification.needs-revision', $request), ['notes' => ''])
            ->assertSessionHasErrors('notes', null, 'revision');

        $this->assertSame('pending', $request->fresh()->status);
        $this->assertSame('pending', $umkm->fresh()->status);
    }

    public function test_administrator_can_mark_needs_revision(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner);
        $request = $this->umkmRequestFor($umkm, $owner);

        $this->actingAs($admin)
            ->post(route('admin.umkm.verification.needs-revision', $request), ['notes' => 'Mohon lengkapi alamat usaha.'])
            ->assertRedirect(route('admin.umkm.verification.index'))
            ->assertSessionHas('status');
    }

    public function test_umkm_becomes_needs_revision(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner);
        $request = $this->umkmRequestFor($umkm, $owner);

        $this->actingAs($admin)->post(route('admin.umkm.verification.needs-revision', $request), ['notes' => 'Mohon lengkapi alamat usaha.']);

        $this->assertSame('needs_revision', $umkm->fresh()->status);
    }

    public function test_verification_request_becomes_needs_revision(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner);
        $request = $this->umkmRequestFor($umkm, $owner);

        $this->actingAs($admin)->post(route('admin.umkm.verification.needs-revision', $request), ['notes' => 'Mohon lengkapi alamat usaha.']);

        $this->assertSame('needs_revision', $request->fresh()->status);
    }

    public function test_needs_revision_sets_reviewer_id(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner);
        $request = $this->umkmRequestFor($umkm, $owner);

        $this->actingAs($admin)->post(route('admin.umkm.verification.needs-revision', $request), ['notes' => 'Mohon lengkapi alamat usaha.']);

        $this->assertSame($admin->id, $request->fresh()->reviewer_id);
    }

    public function test_needs_revision_populates_reviewed_at(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner);
        $request = $this->umkmRequestFor($umkm, $owner);

        $this->actingAs($admin)->post(route('admin.umkm.verification.needs-revision', $request), ['notes' => 'Mohon lengkapi alamat usaha.']);

        $this->assertNotNull($request->fresh()->reviewed_at);
    }

    public function test_needs_revision_persists_notes(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner);
        $request = $this->umkmRequestFor($umkm, $owner);

        $this->actingAs($admin)->post(route('admin.umkm.verification.needs-revision', $request), ['notes' => 'Mohon lengkapi alamat usaha.']);

        $this->assertSame('Mohon lengkapi alamat usaha.', $request->fresh()->notes);
    }

    public function test_approved_request_cannot_be_reviewed_again(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner, 'approved');
        $request = $this->umkmRequestFor($umkm, $owner, 'approved');

        $this->actingAs($admin)
            ->from(route('admin.umkm.verification.index'))
            ->post(route('admin.umkm.verification.approve', $request))
            ->assertRedirect(route('admin.umkm.verification.index'))
            ->assertSessionHas('error');

        $this->assertSame('approved', $request->fresh()->status);
        $this->assertSame('approved', $umkm->fresh()->status);
        $this->assertDatabaseCount('verification_requests', 1);
    }

    public function test_rejected_request_cannot_be_reviewed_again(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner, 'rejected');
        $request = $this->umkmRequestFor($umkm, $owner, 'rejected');

        $this->actingAs($admin)
            ->from(route('admin.umkm.verification.index'))
            ->post(route('admin.umkm.verification.approve', $request))
            ->assertRedirect(route('admin.umkm.verification.index'))
            ->assertSessionHas('error');

        $this->assertSame('rejected', $request->fresh()->status);
        $this->assertSame('rejected', $umkm->fresh()->status);
    }

    public function test_needs_revision_request_cannot_be_reviewed_again(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner, 'needs_revision');
        $request = $this->umkmRequestFor($umkm, $owner, 'needs_revision');

        $this->actingAs($admin)
            ->from(route('admin.umkm.verification.index'))
            ->post(route('admin.umkm.verification.approve', $request))
            ->assertRedirect(route('admin.umkm.verification.index'))
            ->assertSessionHas('error');

        $this->assertSame('needs_revision', $request->fresh()->status);
        $this->assertSame('needs_revision', $umkm->fresh()->status);
    }

    public function test_reviewer_id_cannot_be_forged(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $other = User::factory()->create();
        $umkm = $this->umkmFor($owner);
        $request = $this->umkmRequestFor($umkm, $owner);

        $this->actingAs($admin)
            ->post(route('admin.umkm.verification.approve', $request), ['reviewer_id' => $other->id]);

        $this->assertSame($admin->id, $request->fresh()->reviewer_id);
        $this->assertNotSame($other->id, $request->fresh()->reviewer_id);
    }

    public function test_product_verification_cannot_be_reviewed_through_umkm_endpoint(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $request = $this->productRequestFor($owner);

        $this->actingAs($admin)
            ->get(route('admin.umkm.verification.show', $request))
            ->assertNotFound();

        $this->actingAs($admin)
            ->post(route('admin.umkm.verification.approve', $request))
            ->assertNotFound();
    }

    public function test_owner_cannot_review_umkm_through_review_endpoint(): void
    {
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner);
        $request = $this->umkmRequestFor($umkm, $owner);

        $this->actingAs($owner)
            ->post(route('admin.umkm.verification.approve', $request))
            ->assertForbidden();

        $this->assertSame('pending', $request->fresh()->status);
        $this->assertSame('pending', $umkm->fresh()->status);
    }

    public function test_review_is_atomic_when_umkm_update_fails(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner);
        $request = $this->umkmRequestFor($umkm, $owner);

        Umkm::updating(function () {
            throw new \RuntimeException('forced failure');
        });

        try {
            $this->actingAs($admin)
                ->post(route('admin.umkm.verification.approve', $request))
                ->assertStatus(500);
        } finally {
            Umkm::getEventDispatcher()->forget('eloquent.updating: '.Umkm::class);
        }

        $this->assertSame('pending', $umkm->fresh()->status);
        $this->assertSame('pending', $request->fresh()->status);
        $this->assertNull($request->fresh()->reviewer_id);
        $this->assertNull($request->fresh()->reviewed_at);
    }

    public function test_pending_review_page_hides_public_preview_link(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner);
        $request = $this->umkmRequestFor($umkm, $owner);

        $this->actingAs($admin)
            ->get(route('admin.umkm.verification.show', $request))
            ->assertOk()
            ->assertDontSee('Lihat Halaman Publik');
    }

    public function test_approved_review_page_shows_public_preview_link(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner, 'approved');
        $request = $this->umkmRequestFor($umkm, $owner, 'approved');

        $this->actingAs($admin)
            ->get(route('admin.umkm.verification.show', $request))
            ->assertOk()
            ->assertSee('href="'.route('public.umkm.show', $umkm).'"', false)
            ->assertSee('Lihat Halaman Publik');
    }

    public function test_atomic_approve_rejects_non_pending_request_via_affected_rows(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner, 'approved');
        $request = $this->umkmRequestFor($umkm, $owner, 'approved');

        $this->actingAs($admin)
            ->from(route('admin.umkm.verification.index'))
            ->post(route('admin.umkm.verification.approve', $request))
            ->assertRedirect(route('admin.umkm.verification.index'))
            ->assertSessionHas('error');

        $this->assertSame('approved', $request->fresh()->status);
        $this->assertSame('approved', $umkm->fresh()->status);
        $this->assertNull($request->fresh()->reviewer_id);
    }

    public function test_atomic_reject_rejects_non_pending_request_via_affected_rows(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner, 'rejected');
        $request = $this->umkmRequestFor($umkm, $owner, 'rejected');

        $this->actingAs($admin)
            ->from(route('admin.umkm.verification.index'))
            ->post(route('admin.umkm.verification.reject', $request), ['notes' => 'Tolak'])
            ->assertRedirect(route('admin.umkm.verification.index'))
            ->assertSessionHas('error');

        $this->assertSame('rejected', $request->fresh()->status);
        $this->assertSame('rejected', $umkm->fresh()->status);
    }

    public function test_atomic_needs_revision_rejects_non_pending_request_via_affected_rows(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner, 'needs_revision');
        $request = $this->umkmRequestFor($umkm, $owner, 'needs_revision');

        $this->actingAs($admin)
            ->from(route('admin.umkm.verification.index'))
            ->post(route('admin.umkm.verification.needs-revision', $request), ['notes' => 'Revisi'])
            ->assertRedirect(route('admin.umkm.verification.index'))
            ->assertSessionHas('error');

        $this->assertSame('needs_revision', $request->fresh()->status);
        $this->assertSame('needs_revision', $umkm->fresh()->status);
    }
}
