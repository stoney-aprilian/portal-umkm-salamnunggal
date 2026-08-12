<?php

namespace Tests\Feature\Owner;

use App\Models\Category;
use App\Models\Umkm;
use App\Models\User;
use App\Models\VerificationRequest;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UmkmRejectedFlowTest extends TestCase
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

    private function umkmFor(User $owner, string $status = 'draft', string $name = 'Warung Maju'): Umkm
    {
        return Umkm::create([
            'user_id' => $owner->id,
            'category_id' => $this->umkmCategory()->id,
            'name' => $name,
            'slug' => Umkm::generateUniqueSlug($name),
            'status' => $status,
        ]);
    }

    private function rejectedUmkm(User $owner, User $admin, string $notes = 'Logo kurang jelas. Mohon lengkapi alamat usaha.'): Umkm
    {
        $umkm = $this->umkmFor($owner, 'rejected');

        $umkm->verificationRequests()->create([
            'user_id' => $owner->id,
            'reviewer_id' => $admin->id,
            'status' => 'rejected',
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

    public function test_dashboard_shows_rejected_status_and_rejection_reason(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $this->rejectedUmkm($owner, $admin);

        $this->actingAs($owner)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Ditolak')
            ->assertSee('Alasan Penolakan')
            ->assertSee('Logo kurang jelas. Mohon lengkapi alamat usaha.')
            ->assertSee('Perbaiki UMKM')
            ->assertDontSee('Kirim Pengajuan');
    }

    public function test_dashboard_rejection_note_comes_from_latest_rejected_request(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $umkm = $this->rejectedUmkm($owner, $admin, 'Alasan pertama.');

        $umkm->verificationRequests()->create([
            'user_id' => $owner->id,
            'reviewer_id' => $admin->id,
            'status' => 'rejected',
            'notes' => 'Alasan terbaru.',
            'reviewed_at' => now(),
        ]);

        $this->actingAs($owner)
            ->get(route('dashboard'))
            ->assertSee('Alasan terbaru.')
            ->assertDontSee('Alasan pertama.');
    }

    public function test_dashboard_handles_missing_verification_request_without_error(): void
    {
        $owner = $this->owner();
        $this->umkmFor($owner, 'rejected');

        $this->actingAs($owner)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Perbaiki UMKM');
    }

    public function test_rejected_edit_page_is_accessible_and_shows_rejection_reason(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $umkm = $this->rejectedUmkm($owner, $admin);

        $this->actingAs($owner)
            ->get(route('owner.umkm.edit', $umkm))
            ->assertOk()
            ->assertSee('Perbaiki UMKM')
            ->assertSee('Simpan Perubahan')
            ->assertSee('Pengajuan UMKM Anda ditolak.')
            ->assertSee('Logo kurang jelas. Mohon lengkapi alamat usaha.')
            ->assertSee('Warung Maju');
    }

    public function test_owner_can_update_rejected_umkm_and_it_returns_to_draft(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $umkm = $this->rejectedUmkm($owner, $admin);

        $this->actingAs($owner)
            ->put(route('owner.umkm.update', $umkm), $this->validPayload())
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('status');

        $this->assertSame('draft', $umkm->fresh()->status);
        $this->assertSame('Warung Maju Baru', $umkm->fresh()->name);
        $this->assertSame('Deskripsi baru.', $umkm->fresh()->description);
        $this->assertSame('08.00 - 17.00', $umkm->fresh()->operational_hours);
    }

    public function test_ownership_is_preserved_after_rejected_update(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $umkm = $this->rejectedUmkm($owner, $admin);

        $this->actingAs($owner)->put(route('owner.umkm.update', $umkm), $this->validPayload());

        $this->assertSame($owner->id, $umkm->fresh()->user_id);
    }

    public function test_slug_is_regenerated_from_new_name(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $umkm = $this->rejectedUmkm($owner, $admin);

        $this->actingAs($owner)
            ->put(route('owner.umkm.update', $umkm), $this->validPayload(['name' => 'Warung Nusantara']));

        $this->assertSame('warung-nusantara', $umkm->fresh()->slug);
    }

    public function test_current_slug_is_not_treated_as_collision(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $umkm = $this->rejectedUmkm($owner, $admin);

        $this->actingAs($owner)
            ->put(route('owner.umkm.update', $umkm), $this->validPayload(['name' => 'Warung Maju']));

        $this->assertSame('warung-maju', $umkm->fresh()->slug);
    }

    public function test_slug_collision_with_other_umkm_is_suffixed(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $this->umkmFor($this->owner(), 'approved', 'Warung Maju Baru');
        $umkm = $this->rejectedUmkm($owner, $admin);

        $this->actingAs($owner)
            ->put(route('owner.umkm.update', $umkm), $this->validPayload());

        $this->assertSame('warung-maju-baru-2', $umkm->fresh()->slug);
    }

    public function test_rejected_verification_request_history_is_preserved_after_save(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $umkm = $this->rejectedUmkm($owner, $admin);
        $before = $umkm->verificationRequests()->first();

        $this->actingAs($owner)->put(route('owner.umkm.update', $umkm), $this->validPayload());

        $after = $umkm->verificationRequests()->first()->fresh();

        $this->assertSame($before->id, $after->id);
        $this->assertSame('rejected', $after->status);
        $this->assertSame($before->reviewer_id, $after->reviewer_id);
        $this->assertSame($before->reviewed_at, $after->reviewed_at);
        $this->assertSame($before->notes, $after->notes);
        $this->assertDatabaseCount('verification_requests', 1);
    }

    public function test_rejected_umkm_can_be_resubmitted_creating_new_pending_request(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $umkm = $this->rejectedUmkm($owner, $admin);

        $this->actingAs($owner)->put(route('owner.umkm.update', $umkm), $this->validPayload());

        $this->actingAs($owner)
            ->post(route('owner.umkm.submit', $umkm->fresh()))
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('status');

        $this->assertSame('pending', $umkm->fresh()->status);
        $this->assertDatabaseCount('verification_requests', 2);

        $latest = VerificationRequest::latest('id')->first();

        $this->assertSame('pending', $latest->status);
        $this->assertSame($owner->id, $latest->user_id);
        $this->assertNull($latest->reviewer_id);
        $this->assertNull($latest->reviewed_at);
        $this->assertNull($latest->notes);
    }

    public function test_first_request_remains_rejected_after_resubmission(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $umkm = $this->rejectedUmkm($owner, $admin);

        $this->actingAs($owner)->put(route('owner.umkm.update', $umkm), $this->validPayload());
        $this->actingAs($owner)->post(route('owner.umkm.submit', $umkm->fresh()));

        $previous = VerificationRequest::oldest('id')->first();

        $this->assertSame('rejected', $previous->fresh()->status);
        $this->assertSame($admin->id, $previous->fresh()->reviewer_id);
        $this->assertNotNull($previous->fresh()->reviewed_at);
        $this->assertSame('Logo kurang jelas. Mohon lengkapi alamat usaha.', $previous->fresh()->notes);
    }

    public function test_guest_cannot_access_rejected_edit(): void
    {
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner, 'rejected');

        $this->get(route('owner.umkm.edit', $umkm))->assertRedirect(route('login'));
    }

    public function test_administrator_cannot_access_rejected_edit(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner, 'rejected');

        $this->actingAs($admin)
            ->get(route('owner.umkm.edit', $umkm))
            ->assertForbidden();
    }

    public function test_other_owner_cannot_edit_rejected_umkm(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $other = $this->owner();
        $umkm = $this->rejectedUmkm($owner, $admin);

        $this->actingAs($other)
            ->put(route('owner.umkm.update', $umkm), $this->validPayload())
            ->assertForbidden();

        $this->assertSame('rejected', $umkm->fresh()->status);
        $this->assertSame('Warung Maju', $umkm->fresh()->name);
    }

    public function test_forged_user_id_is_ignored(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $other = User::factory()->create();
        $umkm = $this->rejectedUmkm($owner, $admin);

        $this->actingAs($owner)
            ->put(route('owner.umkm.update', $umkm), $this->validPayload(['user_id' => $other->id]));

        $this->assertSame($owner->id, $umkm->fresh()->user_id);
        $this->assertNotSame($other->id, $umkm->fresh()->user_id);
    }

    public function test_forged_status_is_ignored_and_status_becomes_draft(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $umkm = $this->rejectedUmkm($owner, $admin);

        $this->actingAs($owner)
            ->put(route('owner.umkm.update', $umkm), $this->validPayload(['status' => 'approved']));

        $this->assertSame('draft', $umkm->fresh()->status);
    }

    public function test_direct_post_to_invalid_status_does_not_change_status(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $umkm = $this->rejectedUmkm($owner, $admin);

        $this->actingAs($owner)
            ->put(route('owner.umkm.update', $umkm), $this->validPayload(['status' => 'pending']));

        $this->assertSame('draft', $umkm->fresh()->status);
    }

    public function test_forged_slug_is_ignored(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $umkm = $this->rejectedUmkm($owner, $admin);

        $this->actingAs($owner)
            ->put(route('owner.umkm.update', $umkm), $this->validPayload(['slug' => 'slug-palsu']));

        $this->assertSame('warung-maju-baru', $umkm->fresh()->slug);
        $this->assertNotSame('slug-palsu', $umkm->fresh()->slug);
    }

    public function test_forged_review_fields_do_not_alter_request_history(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $other = User::factory()->create();
        $umkm = $this->rejectedUmkm($owner, $admin);

        $this->actingAs($owner)
            ->put(route('owner.umkm.update', $umkm), $this->validPayload([
                'reviewer_id' => $other->id,
                'reviewed_at' => now()->addDay(),
                'notes' => 'Catatan palsu.',
                'verifiable_type' => 'App\\Models\\Product',
                'verifiable_id' => 999,
            ]));

        $request = $umkm->verificationRequests()->first()->fresh();

        $this->assertSame('rejected', $request->status);
        $this->assertSame($admin->id, $request->reviewer_id);
        $this->assertSame('Logo kurang jelas. Mohon lengkapi alamat usaha.', $request->notes);
        $this->assertSame('App\\Models\\Umkm', $request->verifiable_type);
        $this->assertSame($umkm->id, $request->verifiable_id);
        $this->assertDatabaseCount('verification_requests', 1);
    }

    public function test_product_category_is_rejected_for_revision(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $umkm = $this->rejectedUmkm($owner, $admin);
        $this->productCategory();

        $this->actingAs($owner)
            ->from(route('owner.umkm.edit', $umkm))
            ->put(route('owner.umkm.update', $umkm), $this->validPayload(['category_id' => $this->productCategory()->id]))
            ->assertSessionHasErrors('category_id');

        $this->assertSame('rejected', $umkm->fresh()->status);
    }
}
