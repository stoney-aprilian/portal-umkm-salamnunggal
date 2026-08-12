<?php

namespace Tests\Feature\Public;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AboutContactTest extends TestCase
{
    use RefreshDatabase;

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

    public function test_guest_can_access_about_page(): void
    {
        $this->get(route('public.about'))
            ->assertOk()
            ->assertSee('Tentang Portal');
    }

    public function test_about_route_is_public_tentang(): void
    {
        $this->assertSame('/tentang', parse_url(route('public.about'), PHP_URL_PATH));
    }

    public function test_about_page_contains_portal_identity(): void
    {
        $this->get(route('public.about'))
            ->assertOk()
            ->assertSee('Portal UMKM Salamnunggal')
            ->assertSee('Desa Salamnunggal');
    }

    public function test_about_page_contains_documented_purpose(): void
    {
        $this->get(route('public.about'))
            ->assertOk()
            ->assertSee('media promosi')
            ->assertSee('publikasi UMKM secara terpusat')
            ->assertSee('mempertemukan masyarakat dengan pelaku UMKM')
            ->assertSee('bukan marketplace');
    }

    public function test_about_page_contains_no_internal_verification_information(): void
    {
        $this->get(route('public.about'))
            ->assertOk()
            ->assertDontSee('pending')
            ->assertDontSee('needs_revision')
            ->assertDontSee('reviewer');
    }

    public function test_about_page_does_not_invent_unsupported_claims(): void
    {
        $this->get(route('public.about'))
            ->assertOk()
            ->assertDontSee('Sejak')
            ->assertDontSee('tahun')
            ->assertDontSee('partner');
    }

    public function test_about_page_has_useful_navigation(): void
    {
        $this->get(route('public.about'))
            ->assertOk()
            ->assertSee('Jelajahi UMKM')
            ->assertSee('Lihat Produk')
            ->assertSee('Daftarkan UMKM')
            ->assertSee(route('public.umkm.index'))
            ->assertSee(route('public.product.index'))
            ->assertSee(route('register'));
    }

    public function test_owner_can_access_about_page(): void
    {
        $owner = $this->owner();

        $this->actingAs($owner)
            ->get(route('public.about'))
            ->assertOk()
            ->assertSee('Tentang Portal');
    }

    public function test_administrator_can_access_about_page(): void
    {
        $admin = $this->administrator();

        $this->actingAs($admin)
            ->get(route('public.about'))
            ->assertOk()
            ->assertSee('Tentang Portal');
    }

    public function test_guest_can_access_contact_page(): void
    {
        $this->get(route('public.contact'))
            ->assertOk()
            ->assertSee('Kontak');
    }

    public function test_contact_route_is_public_kontak(): void
    {
        $this->assertSame('/kontak', parse_url(route('public.contact'), PHP_URL_PATH));
    }

    public function test_contact_page_does_not_fabricate_contact_details(): void
    {
        $this->get(route('public.contact'))
            ->assertOk()
            ->assertDontSee('mailto:')
            ->assertDontSee('tel:')
            ->assertDontSee('wa.me')
            ->assertDontSee('+62')
            ->assertDontSee('@gmail.com');
    }

    public function test_contact_page_shows_unconfigured_state(): void
    {
        $this->get(route('public.contact'))
            ->assertOk()
            ->assertSee('Informasi kontak belum tersedia.');
    }

    public function test_contact_page_displays_configured_contact_information(): void
    {
        $this->seed(\Database\Seeders\SettingSeeder::class);

        $this->get(route('public.contact'))
            ->assertOk()
            ->assertSee('Kantor Desa Salamnunggal')
            ->assertSee('+62 812-3456-7890')
            ->assertSee('portal@umkm-salamnunggal.id')
            ->assertSee('Senin - Jumat, 08.00 - 15.00 WIB')
            ->assertSee('tel:')
            ->assertSee('mailto:');
    }

    public function test_contact_page_links_point_to_real_actions(): void
    {
        $this->seed(\Database\Seeders\SettingSeeder::class);

        $response = $this->get(route('public.contact'))->assertOk();

        $this->assertStringContainsString('mailto:portal@umkm-salamnunggal.id', $response->getContent());
        $this->assertStringContainsString('tel:+6281234567890', $response->getContent());
    }

    public function test_contact_page_has_useful_navigation(): void
    {
        $this->get(route('public.contact'))
            ->assertOk()
            ->assertSee('Lihat UMKM')
            ->assertSee('Lihat Produk')
            ->assertSee(route('public.umkm.index'))
            ->assertSee(route('public.product.index'));
    }

    public function test_owner_can_access_contact_page(): void
    {
        $owner = $this->owner();

        $this->actingAs($owner)
            ->get(route('public.contact'))
            ->assertOk()
            ->assertSee('Kontak');
    }

    public function test_administrator_can_access_contact_page(): void
    {
        $admin = $this->administrator();

        $this->actingAs($admin)
            ->get(route('public.contact'))
            ->assertOk()
            ->assertSee('Kontak');
    }

    public function test_guest_navigation_contains_tentang_and_kontak(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Tentang')
            ->assertSee('Kontak');
    }

    public function test_guest_nav_tentang_link_points_to_public_about(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee(route('public.about'));
    }

    public function test_guest_nav_kontak_link_points_to_public_contact(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee(route('public.contact'));
    }

    public function test_existing_guest_nav_links_remain_intact(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Beranda')
            ->assertSee('UMKM')
            ->assertSee('Produk')
            ->assertSee('Masuk')
            ->assertSee('Daftarkan UMKM')
            ->assertSee(route('public.umkm.index'))
            ->assertSee(route('public.product.index'));
    }

    public function test_owner_navigation_remains_functional(): void
    {
        $owner = $this->owner();

        $this->actingAs($owner)
            ->get('/')
            ->assertOk()
            ->assertSee('/dashboard')
            ->assertDontSee('/admin/dashboard');
    }

    public function test_admin_navigation_remains_functional(): void
    {
        $admin = $this->administrator();

        $this->actingAs($admin)
            ->get('/')
            ->assertOk()
            ->assertSee('/admin/dashboard')
            ->assertSee('/admin/umkm/verification')
            ->assertSee('/admin/products/verification');
    }

    public function test_pages_are_accessible_without_authentication(): void
    {
        $this->get(route('public.about'))->assertOk();
        $this->get(route('public.contact'))->assertOk();
    }

    public function test_get_requests_create_no_records(): void
    {
        $this->get(route('public.about'))->assertOk();
        $this->get(route('public.contact'))->assertOk();

        $this->assertDatabaseCount('umkms', 0);
        $this->assertDatabaseCount('products', 0);
        $this->assertDatabaseCount('verification_requests', 0);
        $this->assertDatabaseCount('media', 0);
    }

    public function test_no_verification_request_data_is_leaked(): void
    {
        $reviewer = $this->administrator();
        $owner = $this->owner();
        $umkm = \App\Models\Umkm::create([
            'user_id' => $owner->id,
            'category_id' => \App\Models\Category::firstOrCreate([
                'type' => 'umkm',
                'name' => 'Kuliner',
                'slug' => 'kuliner',
            ])->id,
            'name' => 'Warung Maju',
            'slug' => 'warung-maju',
            'status' => 'pending',
        ]);
        $umkm->verificationRequests()->create([
            'user_id' => $owner->id,
            'reviewer_id' => $reviewer->id,
            'status' => 'pending',
            'notes' => 'Catatan pemeriksaan internal.',
        ]);

        $this->get(route('public.about'))
            ->assertOk()
            ->assertDontSee($reviewer->name)
            ->assertDontSee('Catatan pemeriksaan internal.');
        $this->get(route('public.contact'))
            ->assertOk()
            ->assertDontSee($reviewer->name)
            ->assertDontSee('Catatan pemeriksaan internal.');
    }

    public function test_no_user_account_data_is_leaked(): void
    {
        $this->owner();

        $user = User::factory()->create(['name' => 'Pengguna Rahasia', 'email' => 'rahasia@example.test']);

        $this->get(route('public.about'))
            ->assertOk()
            ->assertDontSee('Pengguna Rahasia')
            ->assertDontSee('rahasia@example.test');
        $this->get(route('public.contact'))
            ->assertOk()
            ->assertDontSee('Pengguna Rahasia')
            ->assertDontSee('rahasia@example.test');
        $this->assertDatabaseHas('users', ['email' => $user->email]);
    }

    public function test_no_admin_only_action_appears(): void
    {
        $this->get(route('public.about'))
            ->assertOk()
            ->assertDontSee('/admin/dashboard')
            ->assertDontSee('/admin/umkm/verification')
            ->assertDontSee('/admin/products/verification')
            ->assertDontSee('Log Out');
        $this->get(route('public.contact'))
            ->assertOk()
            ->assertDontSee('/admin/dashboard')
            ->assertDontSee('Log Out');
    }
}
