<?php

namespace Tests\Feature\Owner;

use App\Models\Category;
use App\Models\Product;
use App\Models\Umkm;
use App\Models\User;
use App\Models\VerificationRequest;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OwnerDashboardTest extends TestCase
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

    private function productFor(User $owner, string $status, Umkm $umkm, string $name = 'Produk Kopi'): Product
    {
        return Product::create([
            'umkm_id' => $umkm->id,
            'category_id' => $this->productCategory()->id,
            'name' => $name,
            'slug' => Product::generateUniqueSlug($name),
            'price' => 10000,
            'status' => $status,
        ]);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
    }

    public function test_owner_can_access_dashboard(): void
    {
        $owner = $this->owner();
        $this->umkmFor($owner, 'draft');

        $this->actingAs($owner)->get(route('dashboard'))->assertOk();
    }

    public function test_administrator_is_redirected_to_admin_dashboard(): void
    {
        $admin = $this->administrator();

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertRedirect(route('admin.dashboard'));
    }

    public function test_dashboard_shows_greeting_with_owner_name(): void
    {
        $this->seed(DatabaseSeeder::class);

        $owner = User::factory()->create(['name' => 'Budi Santoso']);
        $owner->assignRole('owner');

        $this->actingAs($owner)
            ->get(route('dashboard'))
            ->assertSee('Selamat datang, Budi Santoso');
    }

    public function test_no_umkm_state_shows_onboarding(): void
    {
        $owner = $this->owner();

        $this->actingAs($owner)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Mulai promosikan usaha Anda')
            ->assertSee('Daftarkan UMKM')
            ->assertSee('Tunggu Pemeriksaan')
            ->assertSee('Kelola Produk')
            ->assertSee('Ajukan UMKM')
            ->assertDontSee('Status Produk');
    }

    public function test_draft_umkm_shows_status_and_submit_action(): void
    {
        $owner = $this->owner();
        $this->umkmFor($owner, 'draft');

        $this->actingAs($owner)
            ->get(route('dashboard'))
            ->assertSee('Draft')
            ->assertSee('UMKM Anda masih berupa draft.')
            ->assertSee('Kirim Pengajuan');
    }

    public function test_pending_umkm_shows_waiting_message(): void
    {
        $owner = $this->owner();
        $this->umkmFor($owner, 'pending');

        $this->actingAs($owner)
            ->get(route('dashboard'))
            ->assertSee('Menunggu Pemeriksaan')
            ->assertSee('UMKM Anda sedang menunggu pemeriksaan Administrator.')
            ->assertDontSee('Kirim Pengajuan');
    }

    public function test_approved_umkm_shows_status_public_link_and_product_actions(): void
    {
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner, 'approved');

        $this->actingAs($owner)
            ->get(route('dashboard'))
            ->assertSee('Disetujui')
            ->assertSee('Lihat UMKM')
            ->assertSee(route('public.umkm.show', $umkm))
            ->assertSee('Kelola Produk')
            ->assertDontSee('Produk dapat ditambahkan setelah UMKM disetujui');
    }

    public function test_unapproved_umkm_shows_product_information_text(): void
    {
        $owner = $this->owner();
        $this->umkmFor($owner, 'needs_revision');

        $this->actingAs($owner)
            ->get(route('dashboard'))
            ->assertSee('Status Produk')
            ->assertSee('Produk dapat ditambahkan setelah UMKM disetujui.')
            ->assertDontSee('Total Produk');
    }

    public function test_approved_umkm_shows_product_summary_counts(): void
    {
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner, 'approved');

        $this->productFor($owner, 'draft', $umkm);
        $this->productFor($owner, 'pending', $umkm, 'Produk Kopi A');
        $this->productFor($owner, 'pending', $umkm, 'Produk Kopi B');
        $this->productFor($owner, 'approved', $umkm, 'Produk Kopi C');
        $this->productFor($owner, 'needs_revision', $umkm, 'Produk Kopi D');
        $this->productFor($owner, 'rejected', $umkm, 'Produk Kopi E');

        $content = $this->actingAs($owner)->get(route('dashboard'))->assertOk()->getContent();

        $this->assertMatchesRegularExpression('/Total Produk<\/dt>\s*<dd[^>]*>\s*6\s*<\/dd>/', $content);
        $this->assertMatchesRegularExpression('/Draft<\/dt>\s*<dd[^>]*>\s*1\s*<\/dd>/', $content);
        $this->assertMatchesRegularExpression('/Menunggu Pemeriksaan<\/dt>\s*<dd[^>]*>\s*2\s*<\/dd>/', $content);
        $this->assertMatchesRegularExpression('/Disetujui<\/dt>\s*<dd[^>]*>\s*1\s*<\/dd>/', $content);
        $this->assertMatchesRegularExpression('/Perlu Revisi<\/dt>\s*<dd[^>]*>\s*1\s*<\/dd>/', $content);
        $this->assertMatchesRegularExpression('/Ditolak<\/dt>\s*<dd[^>]*>\s*1\s*<\/dd>/', $content);
    }

    public function test_product_counts_exclude_other_owners(): void
    {
        $ownerA = $this->owner();
        $umkmA = $this->umkmFor($ownerA, 'approved');
        $this->productFor($ownerA, 'draft', $umkmA, 'Produk Rahasia A');

        $ownerB = $this->owner();
        $umkmB = $this->umkmFor($ownerB, 'approved');
        $this->productFor($ownerB, 'draft', $umkmB, 'Produk Rahasia B');
        $this->productFor($ownerB, 'draft', $umkmB, 'Produk Rahasia C');
        $this->productFor($ownerB, 'draft', $umkmB, 'Produk Rahasia D');

        $content = $this->actingAs($ownerA)->get(route('dashboard'))->assertOk()->getContent();

        $this->assertMatchesRegularExpression('/Total Produk<\/dt>\s*<dd[^>]*>\s*1\s*<\/dd>/', $content);
        $this->assertStringNotContainsString('Produk Rahasia B', $content);
        $this->assertStringNotContainsString('Produk Rahasia C', $content);
        $this->assertStringNotContainsString('Produk Rahasia D', $content);
    }

    public function test_action_required_for_needs_revision_umkm(): void
    {
        $owner = $this->owner();
        $this->umkmFor($owner, 'needs_revision');

        $this->actingAs($owner)
            ->get(route('dashboard'))
            ->assertSee('UMKM perlu diperbaiki')
            ->assertSee('Perbaiki UMKM');
    }

    public function test_action_required_for_rejected_umkm(): void
    {
        $owner = $this->owner();
        $this->umkmFor($owner, 'rejected');

        $this->actingAs($owner)
            ->get(route('dashboard'))
            ->assertSee('UMKM perlu diperbaiki sebelum diajukan kembali')
            ->assertSee('Perbaiki UMKM');
    }

    public function test_action_required_for_draft_products(): void
    {
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner, 'approved');
        $this->productFor($owner, 'draft', $umkm);

        $this->actingAs($owner)
            ->get(route('dashboard'))
            ->assertSee('Anda memiliki produk yang belum diajukan')
            ->assertSee('Kelola Produk');
    }

    public function test_action_required_for_needs_revision_products(): void
    {
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner, 'approved');
        $this->productFor($owner, 'needs_revision', $umkm);

        $this->actingAs($owner)
            ->get(route('dashboard'))
            ->assertSee('Produk perlu diperbaiki')
            ->assertSee('Perbaiki Produk');
    }

    public function test_action_required_for_rejected_products(): void
    {
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner, 'approved');
        $this->productFor($owner, 'rejected', $umkm);

        $this->actingAs($owner)
            ->get(route('dashboard'))
            ->assertSee('Produk ditolak')
            ->assertSee('Perbaiki Produk');
    }

    public function test_no_action_required_state(): void
    {
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner, 'approved');
        $this->productFor($owner, 'approved', $umkm);

        $this->actingAs($owner)
            ->get(route('dashboard'))
            ->assertSee('Semua pengajuan Anda sudah diproses. Tidak ada tindakan yang diperlukan.')
            ->assertDontSee('Perbaiki UMKM')
            ->assertDontSee('Perbaiki Produk');
    }

    public function test_activity_shows_lifecycle_events_newest_first(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner, 'draft');

        $this->actingAs($owner)->post(route('owner.umkm.submit', $umkm));

        $request = $umkm->verificationRequests()->firstOrFail();

        $this->actingAs($admin)
            ->post(route('admin.umkm.verification.approve', $request));

        $this->actingAs($owner)
            ->get(route('dashboard'))
            ->assertSee('Pengajuan UMKM Anda dikirim untuk diperiksa')
            ->assertSee('UMKM Anda disetujui')
            ->assertSeeInOrder(['UMKM Anda disetujui', 'Pengajuan UMKM Anda dikirim untuk diperiksa']);
    }

    public function test_activity_is_scoped_to_owner(): void
    {
        $admin = $this->administrator();
        $ownerA = $this->owner();
        $umkmA = $this->umkmFor($ownerA, 'approved');
        $productA = $this->productFor($ownerA, 'pending', $umkmA, 'Produk Unik A');
        $productRequest = $productA->verificationRequests()->create([
            'user_id' => $ownerA->id,
            'reviewer_id' => null,
            'status' => 'pending',
            'notes' => null,
            'reviewed_at' => null,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.products.verification.reject', $productRequest), ['notes' => 'Harga tidak sesuai.']);

        $ownerB = $this->owner();
        $this->umkmFor($ownerB, 'approved');

        $this->actingAs($ownerB)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee('Produk Unik A');
    }

    public function test_activity_does_not_leak_internal_fields(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner, 'draft');

        $this->actingAs($owner)->post(route('owner.umkm.submit', $umkm));

        $request = $umkm->verificationRequests()->firstOrFail();

        $this->actingAs($admin)
            ->post(route('admin.umkm.verification.approve', $request));

        $this->actingAs($owner)
            ->get(route('dashboard'))
            ->assertDontSee('submitted')
            ->assertDontSee('approved')
            ->assertDontSee('App\\Models')
            ->assertDontSee('activity_log')
            ->assertDontSee('causer');
    }

    public function test_activity_empty_state(): void
    {
        $owner = $this->owner();
        $this->umkmFor($owner, 'approved');

        $this->actingAs($owner)
            ->get(route('dashboard'))
            ->assertSee('Aktivitas Terbaru')
            ->assertSee('Belum ada aktivitas.');
    }

    public function test_no_raw_enum_values_are_displayed(): void
    {
        $owner = $this->owner();
        $umkm = $this->umkmFor($owner, 'rejected');
        $this->productFor($owner, 'rejected', $umkm, 'Produk Ditolak');

        $this->actingAs($owner)
            ->get(route('dashboard'))
            ->assertDontSee('needs_revision')
            ->assertDontSee('rejected')
            ->assertDontSee('draft')
            ->assertDontSee('pending');
    }
}
