<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Setting;
use App\Models\Umkm;
use App\Models\UmkmRevision;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

/**
 * Phase 10: portal operational settings and content. Administrator
 * manages portal identity, hero content, official contacts, social
 * media, and branding assets through the existing `settings` table.
 * Public pages always fall back to documented defaults when a setting
 * is missing, and deleting an asset also removes its file.
 */
class PortalSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
    }

    private function administrator(): User
    {
        $this->seed(DatabaseSeeder::class);

        $user = User::factory()->create(['status' => 'approved']);
        $user->assignRole('administrator');

        return $user;
    }

    private function owner(): User
    {
        $this->seed(DatabaseSeeder::class);

        $user = User::factory()->create(['status' => 'approved']);
        $user->assignRole('owner');

        return $user;
    }

    private function updateSettings(array $data): TestResponse
    {
        $data = array_replace_recursive([
            'site' => [
                'name' => 'Portal UMKM Salamnunggal',
                'hero_title' => 'Portal UMKM Desa Salamnunggal',
            ],
        ], $data);

        return $this->actingAs($this->administrator())
            ->put(route('admin.settings.update'), $data);
    }

    private function settingValue(string $key): ?string
    {
        return Setting::where('key', $key)->value('value');
    }

    // ---------- Akses & otorisasi ----------

    public function test_administrator_can_access_settings_page(): void
    {
        $this->actingAs($this->administrator())
            ->get(route('admin.settings.index'))
            ->assertOk()
            ->assertSee('Pengaturan Portal')
            ->assertSee('Identitas Portal')
            ->assertSee('Kontak Resmi');
    }

    public function test_owner_cannot_access_settings_page(): void
    {
        $owner = $this->owner();

        $this->actingAs($owner)
            ->get(route('admin.settings.index'))
            ->assertForbidden();

        $this->actingAs($owner)
            ->put(route('admin.settings.update'), ['site' => ['name' => 'Hack']])
            ->assertForbidden();

        $this->assertDatabaseHas('settings', [
            'key' => 'site.name',
            'value' => 'Portal UMKM Salamnunggal',
        ]);
    }

    public function test_guest_cannot_access_settings_page(): void
    {
        $this->get(route('admin.settings.index'))
            ->assertRedirect(route('login'));

        $this->put(route('admin.settings.update'), ['site' => ['name' => 'Hack']])
            ->assertRedirect(route('login'));
    }

    // ---------- Pembaruan setting teks ----------

    public function test_administrator_can_update_portal_name(): void
    {
        $this->updateSettings([
            'site' => ['name' => 'Portal UMKM Salamnunggal Baru'],
        ])->assertRedirect()->assertSessionHas('status');

        $this->assertSame('Portal UMKM Salamnunggal Baru', $this->settingValue('site.name'));
    }

    public function test_administrator_can_update_whatsapp_contact(): void
    {
        $this->updateSettings([
            'contact' => ['whatsapp' => '0812-0000-1111'],
        ])->assertRedirect()->assertSessionHas('status');

        $this->assertSame('0812-0000-1111', $this->settingValue('contact.whatsapp'));

        $this->get(route('public.contact'))
            ->assertOk()
            ->assertSee('https://wa.me/6281200001111', false);
    }

    public function test_administrator_can_update_office_address(): void
    {
        $this->updateSettings([
            'contact' => ['address' => 'Kantor Desa Baru'],
        ])->assertRedirect()->assertSessionHas('status');

        $this->assertSame('Kantor Desa Baru', $this->settingValue('contact.address'));

        $this->get(route('public.contact'))
            ->assertOk()
            ->assertSee('Kantor Desa Baru');
    }

    public function test_administrator_can_update_service_hours(): void
    {
        $this->updateSettings([
            'contact' => ['hours' => 'Senin - Sabtu, 08.00 - 16.00 WIB'],
        ])->assertRedirect()->assertSessionHas('status');

        $this->assertSame('Senin - Sabtu, 08.00 - 16.00 WIB', $this->settingValue('contact.hours'));

        $this->get(route('public.contact'))
            ->assertOk()
            ->assertSee('Senin - Sabtu, 08.00 - 16.00 WIB');
    }

    public function test_empty_setting_removes_row_and_public_page_falls_back(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->actingAs($this->administrator())
            ->put(route('admin.settings.update'), [
                'site' => ['name' => 'Portal UMKM Salamnunggal', 'hero_title' => 'Portal UMKM Desa Salamnunggal'],
                'contact' => ['address' => '', 'phone' => '', 'whatsapp' => '', 'email' => '', 'hours' => '', 'website' => '', 'maps_url' => ''],
            ])
            ->assertRedirect()->assertSessionHas('status');

        $this->assertNull($this->settingValue('contact.address'));
        $this->assertNull($this->settingValue('contact.phone'));
        $this->assertNull($this->settingValue('contact.email'));
        $this->assertNull($this->settingValue('contact.hours'));
        $this->assertNull($this->settingValue('contact.whatsapp'));

        $this->get(route('public.contact'))
            ->assertOk()
            ->assertSee('Informasi kontak belum tersedia.');
    }

    public function test_invalid_url_is_rejected(): void
    {
        $this->actingAs($this->administrator())
            ->from(route('admin.settings.index'))
            ->put(route('admin.settings.update'), [
                'site' => ['name' => 'Portal UMKM Salamnunggal', 'hero_title' => 'Portal UMKM Desa Salamnunggal'],
                'social' => ['instagram' => 'bukan-url'],
            ])
            ->assertRedirect(route('admin.settings.index'))
            ->assertSessionHasErrors('social.instagram');

        $this->assertNull($this->settingValue('social.instagram'));
    }

    // ---------- Hero & konten beranda ----------

    public function test_hero_section_can_be_changed(): void
    {
        $this->updateSettings([
            'site' => [
                'name' => 'Portal UMKM Salamnunggal',
                'hero_title' => 'Selamat Datang di Portal UMKM',
                'hero_description' => 'Deskripsi hero baru yang ditulis oleh pengelola.',
            ],
        ])->assertRedirect()->assertSessionHas('status');

        $this->assertSame('Selamat Datang di Portal UMKM', $this->settingValue('site.hero_title'));
        $this->assertSame('Deskripsi hero baru yang ditulis oleh pengelola.', $this->settingValue('site.hero_description'));

        $this->get('/')
            ->assertOk()
            ->assertSee('Selamat Datang di Portal UMKM')
            ->assertSee('Deskripsi hero baru yang ditulis oleh pengelola.')
            ->assertDontSee('Temukan UMKM serta produk unggulan dari Desa Salamnunggal');
    }

    public function test_content_changes_are_reflected_in_homepage(): void
    {
        $this->updateSettings([
            'site' => ['name' => 'Portal UMKM Salamnunggal', 'hero_title' => 'Portal UMKM Desa Salamnunggal'],
            'file_hero_image' => UploadedFile::fake()->image('hero-baru.png'),
        ])->assertRedirect()->assertSessionHas('status');

        $path = $this->settingValue('site.hero_image');
        $this->assertNotNull($path);
        Storage::disk('public')->assertExists($path);

        $this->get('/')
            ->assertOk()
            ->assertSee('/storage/'.$path, false);
    }

    // ---------- Aset branding ----------

    public function test_logo_can_be_uploaded(): void
    {
        $this->updateSettings([
            'site' => ['name' => 'Portal UMKM Salamnunggal', 'hero_title' => 'Portal UMKM Desa Salamnunggal'],
            'file_logo' => UploadedFile::fake()->image('logo.png'),
        ])->assertRedirect()->assertSessionHas('status');

        $path = $this->settingValue('site.logo');
        $this->assertNotNull($path);
        $this->assertStringStartsWith('branding/', $path);
        Storage::disk('public')->assertExists($path);
    }

    public function test_uploaded_logo_is_rendered_in_navbar(): void
    {
        $this->updateSettings([
            'site' => ['name' => 'Portal UMKM Salamnunggal', 'hero_title' => 'Portal UMKM Desa Salamnunggal'],
            'file_logo' => UploadedFile::fake()->image('logo.png'),
        ]);

        $path = $this->settingValue('site.logo');

        $this->get('/')
            ->assertOk()
            ->assertSee('/storage/'.$path, false)
            ->assertSee('alt="Portal UMKM Salamnunggal"', false);
    }

    public function test_hero_image_can_be_removed(): void
    {
        $this->updateSettings([
            'site' => ['name' => 'Portal UMKM Salamnunggal', 'hero_title' => 'Portal UMKM Desa Salamnunggal'],
            'file_hero_image' => UploadedFile::fake()->image('hero.png'),
        ]);

        $path = $this->settingValue('site.hero_image');
        $this->assertNotNull($path);
        Storage::disk('public')->assertExists($path);

        $this->actingAs($this->administrator())
            ->put(route('admin.settings.update'), [
                'site' => ['name' => 'Portal UMKM Salamnunggal', 'hero_title' => 'Portal UMKM Desa Salamnunggal'],
                'remove_hero_image' => '1',
            ])
            ->assertRedirect()->assertSessionHas('status');

        $this->assertNull($this->settingValue('site.hero_image'));
        Storage::disk('public')->assertMissing($path);

        $this->get('/')
            ->assertOk()
            ->assertDontSee('/storage/'.$path, false);
    }

    public function test_replacing_logo_deletes_previous_file(): void
    {
        $this->updateSettings([
            'site' => ['name' => 'Portal UMKM Salamnunggal', 'hero_title' => 'Portal UMKM Desa Salamnunggal'],
            'file_logo' => UploadedFile::fake()->image('logo-lama.png'),
        ]);

        $oldPath = $this->settingValue('site.logo');
        Storage::disk('public')->assertExists($oldPath);

        $this->actingAs($this->administrator())
            ->put(route('admin.settings.update'), [
                'site' => ['name' => 'Portal UMKM Salamnunggal', 'hero_title' => 'Portal UMKM Desa Salamnunggal'],
                'file_logo' => UploadedFile::fake()->image('logo-baru.png'),
            ])
            ->assertRedirect()->assertSessionHas('status');

        $newPath = $this->settingValue('site.logo');
        $this->assertNotNull($newPath);
        $this->assertNotSame($oldPath, $newPath);
        Storage::disk('public')->assertExists($newPath);
        Storage::disk('public')->assertMissing($oldPath);
    }

    public function test_saving_without_file_keeps_existing_assets(): void
    {
        $this->updateSettings([
            'site' => ['name' => 'Portal UMKM Salamnunggal', 'hero_title' => 'Portal UMKM Desa Salamnunggal'],
            'file_logo' => UploadedFile::fake()->image('logo.png'),
        ]);

        $path = $this->settingValue('site.logo');

        $this->actingAs($this->administrator())
            ->put(route('admin.settings.update'), [
                'site' => ['name' => 'Portal UMKM Salamnunggal', 'hero_title' => 'Judul Hero Diubah Saja'],
                'contact' => ['whatsapp' => '081234567890'],
            ])
            ->assertRedirect()->assertSessionHas('status');

        $this->assertSame($path, $this->settingValue('site.logo'));
        Storage::disk('public')->assertExists($path);
    }

    public function test_invalid_logo_file_is_rejected(): void
    {
        $this->actingAs($this->administrator())
            ->from(route('admin.settings.index'))
            ->put(route('admin.settings.update'), [
                'site' => ['name' => 'Portal UMKM Salamnunggal', 'hero_title' => 'Portal UMKM Desa Salamnunggal'],
                'file_logo' => UploadedFile::fake()->create('dokumen.txt', 10),
            ])
            ->assertRedirect(route('admin.settings.index'))
            ->assertSessionHasErrors('file_logo');

        $this->assertNull($this->settingValue('site.logo'));
    }

    // ---------- Tercermin di halaman publik ----------

    public function test_portal_identity_changes_are_reflected_in_navbar(): void
    {
        $this->updateSettings([
            'site' => ['name' => 'Portal Baru Salamnunggal', 'tagline' => 'Tagline Baru'],
        ])->assertRedirect()->assertSessionHas('status');

        $this->get('/')
            ->assertOk()
            ->assertSee('Portal Baru Salamnunggal')
            ->assertSee('Tagline Baru');
    }

    public function test_portal_identity_changes_are_reflected_in_footer(): void
    {
        $this->updateSettings([
            'site' => [
                'name' => 'Portal Baru Salamnunggal',
                'tagline' => 'Tagline Baru',
                'description' => 'Deskripsi footer baru dari pengelola.',
            ],
        ])->assertRedirect()->assertSessionHas('status');

        $this->get('/')
            ->assertOk()
            ->assertSee('Deskripsi footer baru dari pengelola.')
            ->assertSee('Tagline Baru');
    }

    public function test_portal_identity_changes_are_reflected_in_contact_page(): void
    {
        $this->updateSettings([
            'site' => ['name' => 'Portal Baru Salamnunggal', 'hero_title' => 'Portal UMKM Desa Salamnunggal'],
            'contact' => [
                'whatsapp' => '0812-3456-7890',
                'website' => 'https://salamnunggal.example.com',
                'maps_url' => 'https://maps.google.com/?q=Kantor+Desa',
            ],
            'social' => [
                'instagram' => 'https://instagram.com/portal.salamnunggal',
                'facebook' => 'https://facebook.com/portalsalamnunggal',
            ],
        ])->assertRedirect()->assertSessionHas('status');

        $this->get(route('public.contact'))
            ->assertOk()
            ->assertSee('https://wa.me/6281234567890', false)
            ->assertSee('https://salamnunggal.example.com', false)
            ->assertSee('Lihat di Google Maps')
            ->assertSee('https://maps.google.com/?q=Kantor+Desa', false)
            ->assertSee('https://instagram.com/portal.salamnunggal', false)
            ->assertSee('https://facebook.com/portalsalamnunggal', false);
    }

    public function test_existing_contact_settings_still_work_after_update(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->actingAs($this->administrator())
            ->put(route('admin.settings.update'), [
                'site' => ['name' => 'Portal UMKM Salamnunggal', 'hero_title' => 'Portal UMKM Desa Salamnunggal'],
                'contact' => [
                    'address' => 'Kantor Desa Salamnunggal',
                    'phone' => '+62 812-3456-7890',
                    'whatsapp' => '+62 812-3456-7890',
                    'email' => 'portal@umkm-salamnunggal.id',
                    'hours' => 'Senin - Jumat, 08.00 - 15.00 WIB',
                ],
            ])
            ->assertRedirect()->assertSessionHas('status');

        $response = $this->get(route('public.contact'))->assertOk();

        $this->assertStringContainsString('mailto:portal@umkm-salamnunggal.id', $response->getContent());
        $this->assertStringContainsString('tel:+6281234567890', $response->getContent());
        $this->assertStringContainsString('Kantor Desa Salamnunggal', $response->getContent());
        $this->assertStringContainsString('Senin - Jumat, 08.00 - 15.00 WIB', $response->getContent());
    }

    // ---------- Regression: alur lain tetap berjalan ----------

    public function test_owner_account_verification_still_works(): void
    {
        $this->seed(DatabaseSeeder::class);

        $owner = User::factory()->create([
            'email' => 'owner-baru@example.com',
            'phone' => '081234567890',
            'status' => 'pending',
        ]);
        $owner->assignRole('owner');
        $owner->verificationRequests()->create([
            'user_id' => $owner->id,
            'status' => 'pending',
        ]);

        $this->actingAs($owner)
            ->get('/dashboard')
            ->assertRedirect(route('account.verification.notice'));

        $request = $owner->verificationRequests()->firstOrFail();

        $this->actingAs($this->administrator())
            ->post(route('admin.owner-verification.approve', $request))
            ->assertRedirect()
            ->assertSessionHas('status');

        $this->assertSame('approved', $owner->fresh()->status);

        $this->actingAs($owner->fresh())
            ->get('/dashboard')
            ->assertOk();
    }

    public function test_approved_change_moderation_still_works(): void
    {
        $owner = $this->owner();
        $admin = $this->administrator();
        $category = Category::firstOrCreate(['type' => 'umkm', 'name' => 'Kuliner', 'slug' => 'kuliner']);

        $umkm = Umkm::create([
            'user_id' => $owner->id,
            'category_id' => $category->id,
            'name' => 'Warung Maju',
            'slug' => Umkm::generateUniqueSlug('Warung Maju'),
            'description' => 'Deskripsi lama.',
            'address' => 'Alamat lama.',
            'phone' => '081111111111',
            'operational_hours' => '08.00 - 17.00',
            'status' => 'approved',
        ]);

        $this->actingAs($owner)
            ->post(route('owner.umkm.revisions.store', $umkm), [
                'name' => 'Warung Maju Baru',
                'category_id' => $category->id,
                'description' => 'Deskripsi baru.',
                'address' => 'Alamat baru.',
                'google_maps' => 'https://maps.example.com',
                'phone' => '081234567890',
                'email' => 'warung@example.com',
                'website' => 'https://warung.example.com',
                'instagram' => 'warungmaju',
                'facebook' => 'warungmaju',
                'tiktok' => 'warungmaju',
                'operational_hours' => '10.00 - 20.00',
            ])
            ->assertRedirect();

        $revision = $umkm->revisions()->latest('id')->first();
        $this->assertInstanceOf(UmkmRevision::class, $revision);

        $this->actingAs($owner)
            ->post(route('owner.umkm.revisions.submit', $revision))
            ->assertRedirect()
            ->assertSessionHas('status');

        $request = $revision->verificationRequests()->latest('id')->first();
        $this->assertSame('pending', $revision->fresh()->status);
        $this->assertSame('Warung Maju', $umkm->fresh()->name);

        $this->actingAs($admin)
            ->post(route('admin.umkm.verification.approve', $request))
            ->assertRedirect()
            ->assertSessionHas('status');

        $this->assertSame('approved', $umkm->fresh()->status);
        $this->assertSame('Warung Maju Baru', $umkm->fresh()->name);

        $this->get(route('public.umkm.show', $umkm->fresh()))
            ->assertOk()
            ->assertSee('Warung Maju Baru')
            ->assertSee('Deskripsi baru.')
            ->assertDontSee('Deskripsi lama.');
    }
}
