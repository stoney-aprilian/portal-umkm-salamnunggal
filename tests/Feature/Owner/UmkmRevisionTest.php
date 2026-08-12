<?php

namespace Tests\Feature\Owner;

use App\Models\Category;
use App\Models\Umkm;
use App\Models\User;
use App\Models\VerificationRequest;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UmkmRevisionTest extends TestCase
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

    private function revisedUmkm(User $owner, User $admin, string $notes = 'Mohon lengkapi jam operasional.'): Umkm
    {
        $umkm = $this->umkmFor($owner, 'needs_revision');

        $umkm->verificationRequests()->create([
            'user_id' => $owner->id,
            'reviewer_id' => $admin->id,
            'status' => 'needs_revision',
            'notes' => $notes,
            'reviewed_at' => now()->subDay(),
        ]);

        return $umkm;
    }

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Warung Maju Baru',
            'category_id' => $this->umkmCategory()->id,
            'description' => 'Deskripsi baru.',
            'address' => 'Jl. Raya No. 1',
            'google_maps' => 'https://maps.example.com',
            'phone' => '081234567890',
            'email' => 'warung@example.com',
            'website' => 'https://warung.example.com',
            'instagram' => 'warungmaju',
            'facebook' => 'warungmaju',
            'tiktok' => 'warungmaju',
            'operational_hours' => '08.00 - 17.00',
        ], $overrides);
    }

    public function test_guest_cannot_access_revision_edit(): void
    {
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner, 'needs_revision');

        $this->get(route('owner.umkm.edit', $umkm))->assertRedirect(route('login'));
    }

    public function test_administrator_cannot_access_revision_edit(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner, 'needs_revision');

        $this->actingAs($admin)
            ->get(route('owner.umkm.edit', $umkm))
            ->assertForbidden();
    }

    public function test_owner_can_access_own_needs_revision_umkm(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $umkm = $this->revisedUmkm($owner, $admin);

        $this->actingAs($owner)
            ->get(route('owner.umkm.edit', $umkm))
            ->assertOk()
            ->assertSee('Perbaiki UMKM')
            ->assertSee('Simpan Perubahan')
            ->assertSee('Warung Maju');
    }

    public function test_owner_cannot_access_another_owners_umkm(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $other = $this->owner();
        $umkm = $this->revisedUmkm($other, $admin);

        $this->actingAs($owner)
            ->get(route('owner.umkm.edit', $umkm))
            ->assertForbidden();
    }

    public function test_draft_umkm_can_enter_edit(): void
    {
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner, 'draft');

        $this->actingAs($owner)
            ->get(route('owner.umkm.edit', $umkm))
            ->assertOk()
            ->assertSee('Ubah UMKM')
            ->assertSee('Simpan Perubahan')
            ->assertSee('Warung Maju');
    }

    public function test_pending_umkm_cannot_enter_revision_edit(): void
    {
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner, 'pending');

        $this->actingAs($owner)
            ->from(route('dashboard'))
            ->get(route('owner.umkm.edit', $umkm))
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('error');
    }

    public function test_approved_umkm_cannot_enter_revision_edit(): void
    {
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner, 'approved');

        $this->actingAs($owner)
            ->from(route('dashboard'))
            ->get(route('owner.umkm.edit', $umkm))
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('error');
    }

    public function test_rejected_umkm_can_enter_revision_edit(): void
    {
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner, 'rejected');

        $this->actingAs($owner)
            ->get(route('owner.umkm.edit', $umkm))
            ->assertOk();
    }

    public function test_needs_revision_umkm_can_enter_revision_edit(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $umkm = $this->revisedUmkm($owner, $admin);

        $this->actingAs($owner)
            ->get(route('owner.umkm.edit', $umkm))
            ->assertOk();
    }

    public function test_required_fields_are_validated(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $umkm = $this->revisedUmkm($owner, $admin);

        $this->actingAs($owner)
            ->from(route('owner.umkm.edit', $umkm))
            ->put(route('owner.umkm.update', $umkm), $this->validPayload(['name' => '']))
            ->assertSessionHasErrors('name');

        $this->assertSame('needs_revision', $umkm->fresh()->status);
    }

    public function test_category_must_be_umkm_type(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $umkm = $this->revisedUmkm($owner, $admin);
        $this->productCategory();

        $this->actingAs($owner)
            ->from(route('owner.umkm.edit', $umkm))
            ->put(route('owner.umkm.update', $umkm), $this->validPayload(['category_id' => $this->productCategory()->id]))
            ->assertSessionHasErrors('category_id');

        $this->assertSame('needs_revision', $umkm->fresh()->status);
    }

    public function test_forged_status_is_ignored(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $umkm = $this->revisedUmkm($owner, $admin);

        $this->actingAs($owner)
            ->put(route('owner.umkm.update', $umkm), $this->validPayload(['status' => 'approved']));

        $this->assertSame('draft', $umkm->fresh()->status);
    }

    public function test_forged_user_id_is_ignored(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $other = User::factory()->create();
        $umkm = $this->revisedUmkm($owner, $admin);

        $this->actingAs($owner)
            ->put(route('owner.umkm.update', $umkm), $this->validPayload(['user_id' => $other->id]));

        $this->assertSame($owner->id, $umkm->fresh()->user_id);
        $this->assertNotSame($other->id, $umkm->fresh()->user_id);
    }

    public function test_forged_slug_is_ignored(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $umkm = $this->revisedUmkm($owner, $admin);

        $this->actingAs($owner)
            ->put(route('owner.umkm.update', $umkm), $this->validPayload(['slug' => 'slug-palsu']));

        $this->assertSame('warung-maju-baru', $umkm->fresh()->slug);
        $this->assertNotSame('slug-palsu', $umkm->fresh()->slug);
    }

    public function test_owner_can_update_needs_revision_umkm(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $umkm = $this->revisedUmkm($owner, $admin);

        $this->actingAs($owner)
            ->put(route('owner.umkm.update', $umkm), $this->validPayload())
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('status');

        $this->assertSame('Warung Maju Baru', $umkm->fresh()->name);
        $this->assertSame('Deskripsi baru.', $umkm->fresh()->description);
        $this->assertSame('08.00 - 17.00', $umkm->fresh()->operational_hours);
    }

    public function test_umkm_returns_to_draft_after_revision_save(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $umkm = $this->revisedUmkm($owner, $admin);

        $this->actingAs($owner)->put(route('owner.umkm.update', $umkm), $this->validPayload());

        $this->assertSame('draft', $umkm->fresh()->status);
    }

    public function test_previous_request_remains_needs_revision_after_save(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $umkm = $this->revisedUmkm($owner, $admin);

        $this->actingAs($owner)->put(route('owner.umkm.update', $umkm), $this->validPayload());

        $this->assertSame('needs_revision', $umkm->verificationRequests()->first()->fresh()->status);
    }

    public function test_reviewer_id_is_preserved_after_save(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $umkm = $this->revisedUmkm($owner, $admin);

        $this->actingAs($owner)->put(route('owner.umkm.update', $umkm), $this->validPayload());

        $this->assertSame($admin->id, $umkm->verificationRequests()->first()->fresh()->reviewer_id);
    }

    public function test_reviewed_at_is_preserved_after_save(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $umkm = $this->revisedUmkm($owner, $admin);

        $this->actingAs($owner)->put(route('owner.umkm.update', $umkm), $this->validPayload());

        $this->assertNotNull($umkm->verificationRequests()->first()->fresh()->reviewed_at);
    }

    public function test_notes_are_preserved_after_save(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $umkm = $this->revisedUmkm($owner, $admin);

        $this->actingAs($owner)->put(route('owner.umkm.update', $umkm), $this->validPayload());

        $this->assertSame('Mohon lengkapi jam operasional.', $umkm->verificationRequests()->first()->fresh()->notes);
    }

    public function test_no_new_request_created_during_revision_save(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $umkm = $this->revisedUmkm($owner, $admin);

        $this->actingAs($owner)->put(route('owner.umkm.update', $umkm), $this->validPayload());

        $this->assertDatabaseCount('verification_requests', 1);
    }

    public function test_dashboard_shows_administrator_revision_note(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $this->revisedUmkm($owner, $admin);

        $this->actingAs($owner)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Perlu Revisi')
            ->assertSee('Mohon lengkapi jam operasional.')
            ->assertSee('Perbaiki UMKM');
    }

    public function test_revision_action_appears_only_for_needs_revision(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $this->revisedUmkm($owner, $admin);

        $this->actingAs($owner)
            ->get(route('dashboard'))
            ->assertSee('Perbaiki UMKM');
    }

    public function test_other_statuses_do_not_show_revision_action(): void
    {
        foreach (['draft', 'pending', 'approved'] as $status) {
            $owner = $this->owner();
            $this->umkmFor($owner, $status);

            $this->actingAs($owner)
                ->get(route('dashboard'))
                ->assertDontSee('Perbaiki UMKM');
        }
    }

    public function test_revised_umkm_can_be_submitted_using_existing_endpoint(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $umkm = $this->revisedUmkm($owner, $admin);

        $this->actingAs($owner)->put(route('owner.umkm.update', $umkm), $this->validPayload());

        $this->actingAs($owner)
            ->post(route('owner.umkm.submit', $umkm->fresh()))
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('status', 'Pengajuan UMKM berhasil dikirim dan sedang menunggu pemeriksaan.');
    }

    public function test_umkm_becomes_pending_after_resubmission(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $umkm = $this->revisedUmkm($owner, $admin);

        $this->actingAs($owner)->put(route('owner.umkm.update', $umkm), $this->validPayload());
        $this->actingAs($owner)->post(route('owner.umkm.submit', $umkm->fresh()));

        $this->assertSame('pending', $umkm->fresh()->status);
    }

    public function test_new_verification_request_is_created_after_resubmission(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $umkm = $this->revisedUmkm($owner, $admin);

        $this->actingAs($owner)->put(route('owner.umkm.update', $umkm), $this->validPayload());
        $this->actingAs($owner)->post(route('owner.umkm.submit', $umkm->fresh()));

        $this->assertDatabaseCount('verification_requests', 2);
    }

    public function test_new_request_status_is_pending(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $umkm = $this->revisedUmkm($owner, $admin);

        $this->actingAs($owner)->put(route('owner.umkm.update', $umkm), $this->validPayload());
        $this->actingAs($owner)->post(route('owner.umkm.submit', $umkm->fresh()));

        $this->assertSame('pending', VerificationRequest::latest('id')->first()->status);
    }

    public function test_new_request_reviewer_id_is_null(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $umkm = $this->revisedUmkm($owner, $admin);

        $this->actingAs($owner)->put(route('owner.umkm.update', $umkm), $this->validPayload());
        $this->actingAs($owner)->post(route('owner.umkm.submit', $umkm->fresh()));

        $this->assertNull(VerificationRequest::latest('id')->first()->reviewer_id);
    }

    public function test_new_request_reviewed_at_is_null(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $umkm = $this->revisedUmkm($owner, $admin);

        $this->actingAs($owner)->put(route('owner.umkm.update', $umkm), $this->validPayload());
        $this->actingAs($owner)->post(route('owner.umkm.submit', $umkm->fresh()));

        $this->assertNull(VerificationRequest::latest('id')->first()->reviewed_at);
    }

    public function test_new_request_notes_are_null(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $umkm = $this->revisedUmkm($owner, $admin);

        $this->actingAs($owner)->put(route('owner.umkm.update', $umkm), $this->validPayload());
        $this->actingAs($owner)->post(route('owner.umkm.submit', $umkm->fresh()));

        $this->assertNull(VerificationRequest::latest('id')->first()->notes);
    }

    public function test_previous_request_remains_needs_revision_after_resubmission(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $umkm = $this->revisedUmkm($owner, $admin);

        $this->actingAs($owner)->put(route('owner.umkm.update', $umkm), $this->validPayload());
        $this->actingAs($owner)->post(route('owner.umkm.submit', $umkm->fresh()));

        $previous = VerificationRequest::oldest('id')->first();

        $this->assertSame('needs_revision', $previous->fresh()->status);
        $this->assertSame($admin->id, $previous->fresh()->reviewer_id);
        $this->assertSame('Mohon lengkapi jam operasional.', $previous->fresh()->notes);
    }

    public function test_total_verification_requests_is_two_after_resubmission(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $umkm = $this->revisedUmkm($owner, $admin);

        $this->actingAs($owner)->put(route('owner.umkm.update', $umkm), $this->validPayload());
        $this->actingAs($owner)->post(route('owner.umkm.submit', $umkm->fresh()));

        $this->assertDatabaseCount('verification_requests', 2);
    }

    public function test_cross_owner_update_is_blocked(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $other = $this->owner();
        $umkm = $this->revisedUmkm($other, $admin);

        $this->actingAs($owner)
            ->put(route('owner.umkm.update', $umkm), $this->validPayload())
            ->assertForbidden();

        $this->assertSame('needs_revision', $umkm->fresh()->status);
        $this->assertSame('Warung Maju', $umkm->fresh()->name);
    }

    public function test_forged_verification_fields_do_not_alter_request_history(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $other = User::factory()->create();
        $umkm = $this->revisedUmkm($owner, $admin);

        $this->actingAs($owner)
            ->put(route('owner.umkm.update', $umkm), $this->validPayload([
                'reviewer_id' => $other->id,
                'reviewed_at' => now()->addDay(),
                'notes' => 'Catatan palsu.',
                'verifiable_type' => 'App\\Models\\Product',
            ]));

        $request = $umkm->verificationRequests()->first()->fresh();

        $this->assertSame('needs_revision', $request->status);
        $this->assertSame($admin->id, $request->reviewer_id);
        $this->assertSame('Mohon lengkapi jam operasional.', $request->notes);
        $this->assertSame('App\\Models\\Umkm', $request->verifiable_type);
    }
}
