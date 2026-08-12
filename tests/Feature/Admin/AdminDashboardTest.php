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

class AdminDashboardTest extends TestCase
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

    private function umkmRequestFor(Umkm $umkm, User $owner, string $status = 'pending'): VerificationRequest
    {
        return $umkm->verificationRequests()->create([
            'user_id' => $owner->id,
            'reviewer_id' => $status === 'pending' ? null : $this->administrator()->id,
            'status' => $status,
            'notes' => $status === 'pending' ? null : 'Catatan pemeriksaan.',
            'reviewed_at' => $status === 'pending' ? null : now(),
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

    private function productRequestFor(Product $product, User $owner, string $status = 'pending'): VerificationRequest
    {
        return $product->verificationRequests()->create([
            'user_id' => $owner->id,
            'reviewer_id' => $status === 'pending' ? null : $this->administrator()->id,
            'status' => $status,
            'notes' => $status === 'pending' ? null : 'Catatan pemeriksaan.',
            'reviewed_at' => $status === 'pending' ? null : now(),
        ]);
    }

    public function test_guest_cannot_access_admin_dashboard(): void
    {
        $this->get(route('admin.dashboard'))
            ->assertRedirect(route('login'));
    }

    public function test_owner_cannot_access_admin_dashboard(): void
    {
        $owner = $this->owner();

        $this->actingAs($owner)
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }

    public function test_administrator_can_access_admin_dashboard(): void
    {
        $admin = $this->administrator();

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Dashboard Administrator')
            ->assertSee('Perlu Perhatian');
    }

    public function test_administrator_is_redirected_from_root_dashboard(): void
    {
        $admin = $this->administrator();

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertRedirect(route('admin.dashboard'));
    }

    public function test_zero_pending_umkm_count_displays_correctly(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner);
        $product = $this->productFor($owner, 'pending', $umkm);
        $this->productRequestFor($product, $owner);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertSee('>0</dd>', false);
    }

    public function test_zero_pending_product_count_displays_correctly(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner);
        $this->umkmRequestFor($umkm, $owner);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertSee('>0</dd>', false);
    }

    public function test_pending_umkm_count_is_correct(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner);
        $this->umkmRequestFor($umkm, $owner);

        $otherOwner = $this->owner();
        $otherUmkm = $this->umkmFor($otherOwner);
        $this->umkmRequestFor($otherUmkm, $otherOwner);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertSee('>2</dd>', false);
    }

    public function test_pending_product_count_is_correct(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner);

        foreach (['Kopi Arabika', 'Kopi Robusta', 'Keripik Pisang'] as $i => $name) {
            $product = $this->productFor($owner, 'pending', $umkm, $name, 'produk-'.$i);
            $this->productRequestFor($product, $owner);
        }

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertSee('>3</dd>', false);
    }

    public function test_total_pending_count_is_correct(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();

        $umkm = $this->umkmFor($owner);
        $this->umkmRequestFor($umkm, $owner);
        $this->umkmRequestFor($umkm, $owner);

        $product = $this->productFor($owner, 'pending', $umkm);
        $this->productRequestFor($product, $owner);
        $this->productRequestFor($product, $owner);
        $this->productRequestFor($product, $owner);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertSee('>2</dd>', false)
            ->assertSee('>3</dd>', false)
            ->assertSee('>5</dd>', false);
    }

    public function test_approved_requests_are_not_counted(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner, 'approved');
        $this->umkmRequestFor($umkm, $owner, 'approved');

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertSee('>0</dd>', false)
            ->assertDontSee('Warung Maju');
    }

    public function test_rejected_requests_are_not_counted(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner, 'rejected');
        $this->umkmRequestFor($umkm, $owner, 'rejected');

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertSee('>0</dd>', false)
            ->assertDontSee('Warung Maju');
    }

    public function test_needs_revision_requests_are_not_counted(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner, 'needs_revision');
        $this->umkmRequestFor($umkm, $owner, 'needs_revision');

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertSee('>0</dd>', false)
            ->assertDontSee('Warung Maju');
    }

    public function test_recent_pending_umkm_appears(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner);
        $this->umkmRequestFor($umkm, $owner);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertSee('Warung Maju');
    }

    public function test_recent_pending_product_appears(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $product = $this->productFor($owner);
        $this->productRequestFor($product, $owner);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertSee('Kopi Arabika');
    }

    public function test_umkm_owner_name_appears(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner);
        $this->umkmRequestFor($umkm, $owner);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertSee('Pemilik: '.$owner->name);
    }

    public function test_product_umkm_name_appears(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner);
        $product = $this->productFor($owner, 'pending', $umkm);
        $this->productRequestFor($product, $owner);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertSee('UMKM: Warung Maju');
    }

    public function test_product_owner_name_appears(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $product = $this->productFor($owner);
        $this->productRequestFor($product, $owner);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertSee('Pemilik: '.$owner->name);
    }

    public function test_recent_items_ordered_newest_first(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner);
        $product = $this->productFor($owner, 'pending', $umkm);

        $this->umkmRequestFor($umkm, $owner);
        VerificationRequest::where('verifiable_type', Umkm::class)
            ->update(['created_at' => now()->subHour()]);

        $this->productRequestFor($product, $owner);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertSeeInOrder(['Kopi Arabika', 'Warung Maju']);
    }

    public function test_umkm_review_link_points_to_existing_review_route(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner);
        $request = $this->umkmRequestFor($umkm, $owner);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertSee(route('admin.umkm.verification.show', $request));
    }

    public function test_product_review_link_points_to_existing_review_route(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $product = $this->productFor($owner);
        $request = $this->productRequestFor($product, $owner);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertSee(route('admin.products.verification.show', $request));
    }

    public function test_umkm_requests_do_not_inflate_product_count(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner);
        $this->umkmRequestFor($umkm, $owner);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertSee('>1</dd>', false)
            ->assertSee('>0</dd>', false);
    }

    public function test_product_requests_do_not_inflate_umkm_count(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $product = $this->productFor($owner);
        $this->productRequestFor($product, $owner);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertSee('>0</dd>', false)
            ->assertSee('>1</dd>', false);
    }

    public function test_unsupported_polymorphic_targets_are_not_counted_or_displayed(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();

        DB::table('verification_requests')->insert([
            'user_id' => $owner->id,
            'verifiable_type' => Category::class,
            'verifiable_id' => $this->productCategory()->id,
            'status' => 'pending',
            'notes' => null,
            'reviewed_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertSee('>0</dd>', false)
            ->assertDontSee('Makanan');
    }

    public function test_admin_dashboard_link_visible_to_administrator(): void
    {
        $admin = $this->administrator();

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertSee('/admin/dashboard');
    }

    public function test_admin_verification_links_visible_to_administrator(): void
    {
        $admin = $this->administrator();

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertSee('/admin/umkm/verification')
            ->assertSee('/admin/products/verification');
    }

    public function test_owner_does_not_receive_administrator_links(): void
    {
        $owner = $this->owner();

        $this->actingAs($owner)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee('/admin/dashboard')
            ->assertDontSee('/admin/umkm/verification')
            ->assertDontSee('/admin/products/verification');
    }

    public function test_dashboard_does_not_mutate_verification_state(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner, 'pending');
        $umkmRequest = $this->umkmRequestFor($umkm, $owner);
        $product = $this->productFor($owner, 'pending', $umkm);
        $productRequest = $this->productRequestFor($product, $owner);

        $this->actingAs($admin)->get(route('admin.dashboard'));

        $this->assertSame('pending', $umkm->fresh()->status);
        $this->assertSame('pending', $umkmRequest->fresh()->status);
        $this->assertSame('pending', $product->fresh()->status);
        $this->assertSame('pending', $productRequest->fresh()->status);
        $this->assertNull($umkmRequest->fresh()->reviewer_id);
        $this->assertNull($productRequest->fresh()->reviewer_id);
        $this->assertNull($umkmRequest->fresh()->reviewed_at);
        $this->assertNull($productRequest->fresh()->reviewed_at);
    }

    public function test_dashboard_does_not_create_records(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner);
        $this->umkmRequestFor($umkm, $owner);

        $this->actingAs($admin)->get(route('admin.dashboard'));

        $this->assertDatabaseCount('verification_requests', 1);
        $this->assertDatabaseCount('umkms', 1);
        $this->assertDatabaseCount('products', 0);
        $this->assertDatabaseCount('media', 0);
    }

    public function test_empty_state_shown_when_no_pending_requests(): void
    {
        $admin = $this->administrator();

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Semua pengajuan sudah ditangani.')
            ->assertSee('Verifikasi UMKM')
            ->assertSee('Verifikasi Produk');
    }
}
