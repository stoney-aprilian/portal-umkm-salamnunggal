<?php

namespace Tests\Feature\Seeders;

use App\Models\Category;
use App\Models\Media;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Umkm;
use App\Models\User;
use App\Models\VerificationRequest;
use Database\Seeders\DemoDataSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use RuntimeException;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DemoDataSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_demo_data_seeder_runs_successfully(): void
    {
        $this->seed(DemoDataSeeder::class);

        $this->assertDatabaseCount('users', 5);
        $this->assertDatabaseCount('umkms', 4);
        $this->assertDatabaseCount('products', 4);
        $this->assertDatabaseCount('verification_requests', 8);
        $this->assertDatabaseCount('categories', 2);
    }

    public function test_demo_owner_exists(): void
    {
        $this->seed(DemoDataSeeder::class);

        $this->assertDatabaseHas('users', ['email' => 'owner.demo@example.test', 'status' => 'approved']);
    }

    public function test_demo_administrator_exists(): void
    {
        $this->seed(DemoDataSeeder::class);

        $this->assertDatabaseHas('users', ['email' => 'administrator.demo@example.test', 'status' => 'approved']);
    }

    public function test_demo_owner_has_owner_role(): void
    {
        $this->seed(DemoDataSeeder::class);

        $owner = User::where('email', 'owner.demo@example.test')->firstOrFail();

        $this->assertTrue($owner->hasRole('owner'));
    }

    public function test_demo_administrator_has_administrator_role(): void
    {
        $this->seed(DemoDataSeeder::class);

        $admin = User::where('email', 'administrator.demo@example.test')->firstOrFail();

        $this->assertTrue($admin->hasRole('administrator'));
    }

    public function test_demo_credentials_are_deterministic_and_demo_only(): void
    {
        $this->seed(DemoDataSeeder::class);

        foreach (User::all() as $user) {
            $this->assertTrue(Hash::check('password', $user->password));
            $this->assertStringEndsWith('@example.test', $user->email);
        }
    }

    public function test_approved_demo_umkm_exists(): void
    {
        $this->seed(DemoDataSeeder::class);

        $umkm = Umkm::where('slug', 'umkm-demo-kuliner-salamnunggal')->firstOrFail();

        $this->assertSame('approved', $umkm->status);
        $this->assertSame('umkm', $umkm->category->type);
        $this->assertNotNull($umkm->description);
        $this->assertNotNull($umkm->address);
    }

    public function test_approved_demo_product_exists(): void
    {
        $this->seed(DemoDataSeeder::class);

        $product = Product::where('slug', 'produk-demo-keripik-singkong')->firstOrFail();

        $this->assertSame('approved', $product->status);
        $this->assertSame('product', $product->category->type);
        $this->assertSame('approved', $product->umkm->status);
        $this->assertGreaterThan(0, $product->price);
    }

    public function test_pending_umkm_has_pending_verification_request(): void
    {
        $this->seed(DemoDataSeeder::class);

        $umkm = Umkm::where('slug', 'umkm-demo-katering-salamnunggal')->firstOrFail();
        $request = $umkm->verificationRequests()->firstOrFail();

        $this->assertSame('pending', $umkm->status);
        $this->assertSame('pending', $request->status);
        $this->assertNull($request->reviewer_id);
        $this->assertNull($request->reviewed_at);
    }

    public function test_needs_revision_umkm_has_correct_reviewer_note_and_status(): void
    {
        $this->seed(DemoDataSeeder::class);

        $umkm = Umkm::where('slug', 'umkm-demo-cemilan-salamnunggal')->firstOrFail();
        $request = $umkm->verificationRequests()->firstOrFail();

        $this->assertSame('needs_revision', $umkm->status);
        $this->assertSame('needs_revision', $request->status);
        $this->assertSame('administrator.demo@example.test', $request->reviewer->email);
        $this->assertStringContainsString('Demo', $request->notes);
        $this->assertNotNull($request->reviewed_at);
    }

    public function test_rejected_umkm_has_rejected_verification_request(): void
    {
        $this->seed(DemoDataSeeder::class);

        $umkm = Umkm::where('slug', 'umkm-demo-minuman-salamnunggal')->firstOrFail();
        $request = $umkm->verificationRequests()->firstOrFail();

        $this->assertSame('rejected', $umkm->status);
        $this->assertSame('rejected', $request->status);
        $this->assertSame('administrator.demo@example.test', $request->reviewer->email);
        $this->assertNotNull($request->reviewed_at);
    }

    public function test_pending_product_has_pending_verification_request(): void
    {
        $this->seed(DemoDataSeeder::class);

        $product = Product::where('slug', 'produk-demo-sambal-rumahan')->firstOrFail();
        $request = $product->verificationRequests()->firstOrFail();

        $this->assertSame('pending', $product->status);
        $this->assertSame('pending', $request->status);
        $this->assertNull($request->reviewer_id);
        $this->assertNull($request->reviewed_at);
    }

    public function test_needs_revision_product_has_correct_reviewer_note_and_status(): void
    {
        $this->seed(DemoDataSeeder::class);

        $product = Product::where('slug', 'produk-demo-abon-sapi')->firstOrFail();
        $request = $product->verificationRequests()->firstOrFail();

        $this->assertSame('needs_revision', $product->status);
        $this->assertSame('needs_revision', $request->status);
        $this->assertSame('administrator.demo@example.test', $request->reviewer->email);
        $this->assertStringContainsString('Demo', $request->notes);
        $this->assertNotNull($request->reviewed_at);
    }

    public function test_rejected_product_has_rejected_verification_request(): void
    {
        $this->seed(DemoDataSeeder::class);

        $product = Product::where('slug', 'produk-demo-kue-kering')->firstOrFail();
        $request = $product->verificationRequests()->firstOrFail();

        $this->assertSame('rejected', $product->status);
        $this->assertSame('rejected', $request->status);
        $this->assertSame('administrator.demo@example.test', $request->reviewer->email);
        $this->assertNotNull($request->reviewed_at);
    }

    public function test_seeder_is_idempotent(): void
    {
        $this->seed(DemoDataSeeder::class);
        $this->seed(DemoDataSeeder::class);

        $this->assertDatabaseCount('users', 5);
        $this->assertDatabaseCount('umkms', 4);
        $this->assertDatabaseCount('products', 4);
        $this->assertDatabaseCount('verification_requests', 8);
        $this->assertDatabaseCount('media', 0);
    }

    public function test_slugs_remain_unique_after_rerun(): void
    {
        $this->seed(DemoDataSeeder::class);
        $this->seed(DemoDataSeeder::class);

        $duplicateUmkmSlugs = Umkm::query()->selectRaw('slug, count(*) as total')->groupBy('slug')->havingRaw('count(*) > 1')->count();
        $duplicateProductSlugs = Product::query()->selectRaw('slug, count(*) as total')->groupBy('slug')->havingRaw('count(*) > 1')->count();

        $this->assertSame(0, $duplicateUmkmSlugs);
        $this->assertSame(0, $duplicateProductSlugs);
    }

    public function test_demo_users_each_own_at_most_one_umkm(): void
    {
        $this->seed(DemoDataSeeder::class);

        $duplicates = Umkm::query()->selectRaw('user_id, count(*) as total')->groupBy('user_id')->havingRaw('count(*) > 1')->count();

        $this->assertSame(0, $duplicates);
    }

    public function test_public_catalog_sees_approved_demo_umkm_only(): void
    {
        $this->seed(DemoDataSeeder::class);

        $this->get('/umkm')
            ->assertOk()
            ->assertSee('UMKM Demo Kuliner Salamnunggal')
            ->assertDontSee('UMKM Demo Katering Salamnunggal')
            ->assertDontSee('UMKM Demo Cemilan Salamnunggal')
            ->assertDontSee('UMKM Demo Minuman Salamnunggal');
    }

    public function test_public_catalog_sees_approved_demo_product_only(): void
    {
        $this->seed(DemoDataSeeder::class);

        $this->get('/produk')
            ->assertOk()
            ->assertSee('Produk Demo Keripik Singkong')
            ->assertDontSee('Produk Demo Sambal Rumahan')
            ->assertDontSee('Produk Demo Abon Sapi')
            ->assertDontSee('Produk Demo Kue Kering');
    }

    public function test_non_approved_demo_umkms_return_404_publicly(): void
    {
        $this->seed(DemoDataSeeder::class);

        $this->get('/umkm/umkm-demo-katering-salamnunggal')->assertNotFound();
        $this->get('/umkm/umkm-demo-cemilan-salamnunggal')->assertNotFound();
        $this->get('/umkm/umkm-demo-minuman-salamnunggal')->assertNotFound();
    }

    public function test_non_approved_demo_products_return_404_publicly(): void
    {
        $this->seed(DemoDataSeeder::class);

        $this->get('/produk/produk-demo-sambal-rumahan')->assertNotFound();
        $this->get('/produk/produk-demo-abon-sapi')->assertNotFound();
        $this->get('/produk/produk-demo-kue-kering')->assertNotFound();
    }

    public function test_public_pages_render_with_demo_data(): void
    {
        $this->seed(DemoDataSeeder::class);

        $this->get('/')->assertOk()->assertSee('UMKM Demo Kuliner Salamnunggal');
        $this->get('/umkm')->assertOk();
        $this->get('/produk')->assertOk();
        $this->get('/kategori/kuliner/umkm')->assertOk()->assertSee('UMKM Demo Kuliner Salamnunggal');
        $this->get('/kategori/makanan/produk')->assertOk()->assertSee('Produk Demo Keripik Singkong');
        $this->get('/umkm/umkm-demo-kuliner-salamnunggal')->assertOk()->assertSee('Terverifikasi');
        $this->get('/produk/produk-demo-keripik-singkong')->assertOk();
        $this->get('/tentang')->assertOk();
        $this->get('/kontak')->assertOk();
    }

    public function test_admin_dashboard_shows_pending_demo_requests(): void
    {
        $this->seed(DemoDataSeeder::class);

        $admin = User::where('email', 'administrator.demo@example.test')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('UMKM Demo Katering Salamnunggal')
            ->assertSee('Produk Demo Sambal Rumahan');
    }

    public function test_demo_owner_can_access_owner_dashboard(): void
    {
        $this->seed(DemoDataSeeder::class);

        $owner = User::where('email', 'owner.demo@example.test')->firstOrFail();

        $this->actingAs($owner)
            ->get('/dashboard')
            ->assertOk();
    }

    public function test_demo_data_creates_no_media(): void
    {
        $this->seed(DemoDataSeeder::class);

        $this->assertDatabaseCount('media', 0);
    }

    public function test_demo_data_does_not_populate_canonical_settings(): void
    {
        $this->seed(DemoDataSeeder::class);

        $this->assertDatabaseCount('settings', 0);
    }

    public function test_demo_data_creates_no_extra_roles(): void
    {
        $this->seed(DemoDataSeeder::class);

        $this->assertCount(2, Role::all());
        $this->assertDatabaseHas('roles', ['name' => 'owner']);
        $this->assertDatabaseHas('roles', ['name' => 'administrator']);
    }

    public function test_demo_data_creates_no_permissions(): void
    {
        $this->seed(DemoDataSeeder::class);

        $this->assertCount(0, Permission::all());
    }

    public function test_seeder_is_blocked_outside_local_and_testing_environments(): void
    {
        app()->detectEnvironment(fn () => 'staging');

        try {
            $this->expectException(RuntimeException::class);
            $this->seed(DemoDataSeeder::class);
        } finally {
            app()->detectEnvironment(fn () => 'testing');
        }
    }
}
