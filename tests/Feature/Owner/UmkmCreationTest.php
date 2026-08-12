<?php

namespace Tests\Feature\Owner;

use App\Models\Category;
use App\Models\Umkm;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class UmkmCreationTest extends TestCase
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

    public function test_guest_cannot_access_create_page(): void
    {
        $this->get(route('owner.umkm.create'))->assertRedirect(route('login'));
    }

    public function test_administrator_cannot_access_owner_umkm_creation(): void
    {
        $admin = $this->administrator();

        $this->actingAs($admin)
            ->get(route('owner.umkm.create'))
            ->assertForbidden();
    }

    public function test_owner_can_access_create_page(): void
    {
        $owner = $this->owner();
        $this->umkmCategory();

        $this->actingAs($owner)
            ->get(route('owner.umkm.create'))
            ->assertOk()
            ->assertSee('Ajukan UMKM')
            ->assertSee('Nama UMKM')
            ->assertSee('Kategori')
            ->assertSee('Simpan Draft');
    }

    public function test_owner_can_create_umkm(): void
    {
        $owner = $this->owner();
        $category = $this->umkmCategory();

        $this->actingAs($owner)->post(route('owner.umkm.store'), [
            'name' => 'Warung Maju',
            'category_id' => $category->id,
            'description' => 'Menjual nasi dan lauk.',
        ])->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('umkms', [
            'user_id' => $owner->id,
            'category_id' => $category->id,
            'name' => 'Warung Maju',
            'slug' => 'warung-maju',
            'description' => 'Menjual nasi dan lauk.',
            'status' => 'draft',
        ]);
    }

    public function test_created_umkm_belongs_to_authenticated_owner(): void
    {
        $owner = $this->owner();

        $this->actingAs($owner)->post(route('owner.umkm.store'), [
            'name' => 'Warung Maju',
            'category_id' => $this->umkmCategory()->id,
        ]);

        $umkm = Umkm::first();

        $this->assertSame($owner->id, $umkm->user_id);
        $this->assertSame($owner->id, $umkm->user->id);
    }

    public function test_user_id_cannot_be_overridden_by_request_input(): void
    {
        $owner = $this->owner();
        $other = User::factory()->create();

        $this->actingAs($owner)->post(route('owner.umkm.store'), [
            'name' => 'Warung Maju',
            'category_id' => $this->umkmCategory()->id,
            'user_id' => $other->id,
        ]);

        $umkm = Umkm::first();

        $this->assertSame($owner->id, $umkm->user_id);
        $this->assertNotSame($other->id, $umkm->user_id);
    }

    public function test_created_umkm_status_is_draft(): void
    {
        $owner = $this->owner();

        $this->actingAs($owner)->post(route('owner.umkm.store'), [
            'name' => 'Warung Maju',
            'category_id' => $this->umkmCategory()->id,
        ]);

        $this->assertSame('draft', Umkm::first()->status);
    }

    public function test_creating_draft_does_not_create_verification_request(): void
    {
        $owner = $this->owner();

        $this->actingAs($owner)->post(route('owner.umkm.store'), [
            'name' => 'Warung Maju',
            'category_id' => $this->umkmCategory()->id,
        ]);

        $this->assertDatabaseCount('verification_requests', 0);
    }

    public function test_creating_draft_does_not_create_media(): void
    {
        $owner = $this->owner();

        $this->actingAs($owner)->post(route('owner.umkm.store'), [
            'name' => 'Warung Maju',
            'category_id' => $this->umkmCategory()->id,
        ]);

        $this->assertDatabaseCount('media', 0);
    }

    public function test_creating_draft_does_not_change_user_status(): void
    {
        $owner = $this->owner();

        $this->assertSame('pending', $owner->status);

        $this->actingAs($owner)->post(route('owner.umkm.store'), [
            'name' => 'Warung Maju',
            'category_id' => $this->umkmCategory()->id,
        ]);

        $this->assertSame('pending', $owner->fresh()->status);
    }

    public function test_product_category_is_rejected(): void
    {
        $owner = $this->owner();
        $this->productCategory();

        $this->actingAs($owner)->post(route('owner.umkm.store'), [
            'name' => 'Warung Maju',
            'category_id' => $this->productCategory()->id,
        ])->assertSessionHasErrors('category_id');

        $this->assertDatabaseCount('umkms', 0);
    }

    public function test_umkm_category_is_accepted(): void
    {
        $owner = $this->owner();
        $category = $this->umkmCategory();

        $this->actingAs($owner)->post(route('owner.umkm.store'), [
            'name' => 'Warung Maju',
            'category_id' => $category->id,
        ])->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('umkms', ['category_id' => $category->id]);
    }

    public function test_required_fields_are_validated(): void
    {
        $owner = $this->owner();

        $this->actingAs($owner)
            ->post(route('owner.umkm.store'), [])
            ->assertSessionHasErrors(['name', 'category_id']);

        $this->assertDatabaseCount('umkms', 0);
    }

    public function test_optional_fields_may_be_omitted(): void
    {
        $owner = $this->owner();

        $this->actingAs($owner)->post(route('owner.umkm.store'), [
            'name' => 'Warung Maju',
            'category_id' => $this->umkmCategory()->id,
        ])->assertRedirect(route('dashboard'));

        $umkm = Umkm::first();

        $this->assertNull($umkm->description);
        $this->assertNull($umkm->address);
        $this->assertNull($umkm->google_maps);
        $this->assertNull($umkm->phone);
        $this->assertNull($umkm->email);
        $this->assertNull($umkm->website);
        $this->assertNull($umkm->instagram);
        $this->assertNull($umkm->facebook);
        $this->assertNull($umkm->tiktok);
        $this->assertNull($umkm->operational_hours);
    }

    public function test_optional_fields_can_be_submitted(): void
    {
        $owner = $this->owner();

        $this->actingAs($owner)->post(route('owner.umkm.store'), [
            'name' => 'Warung Maju',
            'category_id' => $this->umkmCategory()->id,
            'address' => 'Jl. Raya Salamnunggal No. 1',
            'google_maps' => 'https://maps.app.goo.gl/warungmaju',
            'phone' => '081234567890',
            'email' => 'warung@example.com',
            'website' => 'https://warungmaju.example',
            'instagram' => '@warungmaju',
            'facebook' => 'warungmaju',
            'tiktok' => '@warungmaju',
            'operational_hours' => '08.00 - 17.00',
        ])->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('umkms', [
            'address' => 'Jl. Raya Salamnunggal No. 1',
            'google_maps' => 'https://maps.app.goo.gl/warungmaju',
            'phone' => '081234567890',
            'email' => 'warung@example.com',
            'website' => 'https://warungmaju.example',
            'instagram' => '@warungmaju',
            'facebook' => 'warungmaju',
            'tiktok' => '@warungmaju',
            'operational_hours' => '08.00 - 17.00',
        ]);
    }

    public function test_invalid_email_is_rejected(): void
    {
        $owner = $this->owner();

        $this->actingAs($owner)->post(route('owner.umkm.store'), [
            'name' => 'Warung Maju',
            'category_id' => $this->umkmCategory()->id,
            'email' => 'bukan-email',
        ])->assertSessionHasErrors('email');

        $this->assertDatabaseCount('umkms', 0);
    }

    public function test_slug_is_generated_from_name(): void
    {
        $owner = $this->owner();

        $this->actingAs($owner)->post(route('owner.umkm.store'), [
            'name' => 'Warung Nasi Bu Siti',
            'category_id' => $this->umkmCategory()->id,
        ]);

        $this->assertDatabaseHas('umkms', ['slug' => 'warung-nasi-bu-siti']);
    }

    public function test_slug_ignores_submitted_slug_input(): void
    {
        $owner = $this->owner();

        $this->actingAs($owner)->post(route('owner.umkm.store'), [
            'name' => 'Warung Nasi Bu Siti',
            'category_id' => $this->umkmCategory()->id,
            'slug' => 'slug-curang',
        ]);

        $this->assertDatabaseHas('umkms', ['slug' => 'warung-nasi-bu-siti']);
    }

    public function test_duplicate_slug_gets_deterministic_unique_suffix(): void
    {
        $first = $this->owner();

        $this->actingAs($first)->post(route('owner.umkm.store'), [
            'name' => 'Warung Maju',
            'category_id' => $this->umkmCategory()->id,
        ]);

        $second = $this->owner();

        $this->actingAs($second)->post(route('owner.umkm.store'), [
            'name' => 'Warung Maju',
            'category_id' => $this->umkmCategory()->id,
        ]);

        $this->assertDatabaseHas('umkms', ['slug' => 'warung-maju']);
        $this->assertDatabaseHas('umkms', ['slug' => 'warung-maju-2']);
    }

    public function test_owner_cannot_create_a_second_umkm(): void
    {
        $owner = $this->owner();

        $this->actingAs($owner)->post(route('owner.umkm.store'), [
            'name' => 'Warung Maju',
            'category_id' => $this->umkmCategory()->id,
        ])->assertRedirect(route('dashboard'));

        $this->actingAs($owner)->get(route('owner.umkm.create'))->assertForbidden();
        $this->actingAs($owner)->post(route('owner.umkm.store'), [
            'name' => 'Warung Baru',
            'category_id' => $this->umkmCategory()->id,
        ])->assertForbidden();

        $this->assertDatabaseCount('umkms', 1);
    }

    public function test_owner_cannot_access_another_owners_umkm(): void
    {
        $first = $this->owner();

        $this->actingAs($first)->post(route('owner.umkm.store'), [
            'name' => 'Warung Maju',
            'category_id' => $this->umkmCategory()->id,
        ]);

        $umkm = Umkm::first();

        $second = $this->owner();

        $this->actingAs($second);
        $this->assertFalse(Gate::allows('view', $umkm));
        $this->actingAs($first);
        $this->assertTrue(Gate::allows('view', $umkm));
    }

    public function test_dashboard_exposes_ajukan_umkm_action_for_owner_without_umkm(): void
    {
        $owner = $this->owner();

        $this->actingAs($owner)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Ajukan UMKM');
    }
}
