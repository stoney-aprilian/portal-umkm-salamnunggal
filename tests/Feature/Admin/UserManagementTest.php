<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Umkm;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

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

    private function ownerWithUmkm(): User
    {
        $this->seed(DatabaseSeeder::class);

        $user = User::factory()->create(['status' => 'approved']);
        $user->assignRole('owner');

        Umkm::create([
            'user_id' => $user->id,
            'category_id' => Category::where('type', 'umkm')->first()->id,
            'name' => 'Warung Maju',
            'slug' => Umkm::generateUniqueSlug('Warung Maju'),
            'status' => 'approved',
        ]);

        return $user;
    }

    public function test_administrator_can_access_user_management_index(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();

        $this->actingAs($admin)
            ->get(route('admin.users.index'))
            ->assertOk()
            ->assertSee($owner->name);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('admin.users.index'))
            ->assertRedirect(route('login'));
    }

    public function test_owner_cannot_access_user_management(): void
    {
        $owner = $this->owner();

        $this->actingAs($owner)
            ->get(route('admin.users.index'))
            ->assertForbidden();
    }

    public function test_administrator_can_create_owner_with_email(): void
    {
        $admin = $this->administrator();

        $this->actingAs($admin)
            ->post(route('admin.users.store'), [
                'name' => 'Pemilik Baru',
                'email' => 'pemilik.baru@example.com',
                'phone' => '081234567890',
                'password' => 'password',
                'password_confirmation' => 'password',
            ])
            ->assertRedirect(route('admin.users.show', User::where('email', 'pemilik.baru@example.com')->first()));

        $this->assertDatabaseHas('users', [
            'name' => 'Pemilik Baru',
            'email' => 'pemilik.baru@example.com',
            'phone' => '081234567890',
            'status' => 'approved',
        ]);
    }

    public function test_administrator_can_create_owner_without_email(): void
    {
        $admin = $this->administrator();

        $this->actingAs($admin)
            ->post(route('admin.users.store'), [
                'name' => 'Pemilik Tanpa Email',
                'email' => '',
                'phone' => '',
                'password' => 'password',
                'password_confirmation' => 'password',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('users', [
            'name' => 'Pemilik Tanpa Email',
            'email' => null,
            'phone' => null,
        ]);
    }

    public function test_created_user_always_gets_owner_role(): void
    {
        $admin = $this->administrator();

        $this->actingAs($admin)
            ->post(route('admin.users.store'), [
                'name' => 'Pemilik Baru',
                'email' => 'pemilik.baru@example.com',
                'password' => 'password',
                'password_confirmation' => 'password',
            ]);

        $user = User::where('email', 'pemilik.baru@example.com')->firstOrFail();

        $this->assertTrue($user->hasRole('owner'));
        $this->assertFalse($user->hasRole('administrator'));
        $this->assertSame(1, $user->roles->count());
    }

    public function test_email_duplicate_is_rejected(): void
    {
        $admin = $this->administrator();
        $existing = User::factory()->create(['email' => 'sama@example.com']);
        $existing->assignRole('owner');

        $this->actingAs($admin)
            ->post(route('admin.users.store'), [
                'name' => 'Pemilik Duplikat',
                'email' => 'sama@example.com',
                'password' => 'password',
                'password_confirmation' => 'password',
            ])
            ->assertSessionHasErrors('email');

        $this->assertSame(1, User::where('email', 'sama@example.com')->count());
    }

    public function test_administrator_can_edit_owner(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();

        $this->actingAs($admin)
            ->put(route('admin.users.update', $owner), [
                'name' => 'Nama Baru Owner',
                'email' => 'owner.baru@example.com',
                'phone' => '081298765432',
            ])
            ->assertRedirect(route('admin.users.show', $owner))
            ->assertSessionHas('status');

        $this->assertDatabaseHas('users', [
            'id' => $owner->id,
            'name' => 'Nama Baru Owner',
            'email' => 'owner.baru@example.com',
            'phone' => '081298765432',
        ]);
    }

    public function test_edit_does_not_change_password_or_role(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $originalPassword = $owner->password;

        $this->actingAs($admin)
            ->put(route('admin.users.update', $owner), [
                'name' => 'Nama Baru Owner',
                'email' => 'owner.baru@example.com',
            ]);

        $fresh = $owner->fresh();

        $this->assertSame($originalPassword, $fresh->password);
        $this->assertTrue($fresh->hasRole('owner'));
        $this->assertFalse($fresh->hasRole('administrator'));
    }

    public function test_edit_allows_removing_email(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();

        $this->actingAs($admin)
            ->put(route('admin.users.update', $owner), [
                'name' => $owner->name,
                'email' => '',
            ])
            ->assertSessionHasNoErrors();

        $this->assertNull($owner->fresh()->email);
    }

    public function test_administrator_can_suspend_owner(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();

        $this->actingAs($admin)
            ->post(route('admin.users.suspend', $owner))
            ->assertSessionHas('status');

        $this->assertSame('suspended', $owner->fresh()->status);
    }

    public function test_suspended_owner_cannot_login(): void
    {
        $this->seed(DatabaseSeeder::class);

        $owner = User::factory()->create([
            'email' => 'suspended.owner@example.com',
            'password' => 'password',
            'status' => 'suspended',
        ]);
        $owner->assignRole('owner');

        $this->post('/login', [
            'email' => 'suspended.owner@example.com',
            'password' => 'password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_administrator_can_reactivate_owner(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();
        $owner->update(['status' => 'suspended']);

        $this->actingAs($admin)
            ->post(route('admin.users.activate', $owner))
            ->assertSessionHas('status');

        $this->assertSame('approved', $owner->fresh()->status);
    }

    public function test_reactivated_owner_can_login_again(): void
    {
        $this->seed(DatabaseSeeder::class);

        $owner = User::factory()->create([
            'email' => 'owner.aktif@example.com',
            'password' => 'password',
            'status' => 'approved',
        ]);
        $owner->assignRole('owner');

        $this->post('/login', [
            'email' => 'owner.aktif@example.com',
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticated();
    }

    public function test_suspending_own_account_is_rejected(): void
    {
        $admin = $this->administrator();
        $admin->assignRole('owner');

        $this->actingAs($admin)
            ->post(route('admin.users.suspend', $admin))
            ->assertSessionHas('error');

        $this->assertSame('approved', $admin->fresh()->status);
    }

    public function test_administrator_cannot_manage_non_owner_users(): void
    {
        $admin = $this->administrator();
        $otherAdmin = User::factory()->create();
        $otherAdmin->assignRole('administrator');

        $this->actingAs($admin)
            ->get(route('admin.users.show', $otherAdmin))
            ->assertNotFound();

        $this->actingAs($admin)
            ->post(route('admin.users.suspend', $otherAdmin))
            ->assertNotFound();
    }

    public function test_administrator_can_reset_owner_password(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();

        $this->actingAs($admin)
            ->post(route('admin.users.reset-password', $owner), [
                'password' => 'kata-sandi-baru',
                'password_confirmation' => 'kata-sandi-baru',
            ])
            ->assertSessionHas('status');

        $this->assertTrue(Hash::check('kata-sandi-baru', $owner->fresh()->password));
    }

    public function test_password_is_never_stored_in_plaintext(): void
    {
        $admin = $this->administrator();

        $this->actingAs($admin)
            ->post(route('admin.users.store'), [
                'name' => 'Pemilik Aman',
                'email' => 'aman@example.com',
                'password' => 'password',
                'password_confirmation' => 'password',
            ]);

        $user = User::where('email', 'aman@example.com')->firstOrFail();

        $this->assertNotSame('password', $user->password);
        $this->assertTrue(Hash::check('password', $user->password));
        $this->assertStringNotContainsString('password', $user->password);
    }

    public function test_existing_owner_can_still_login_after_management_actions(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();

        $this->actingAs($admin)
            ->put(route('admin.users.update', $owner), [
                'name' => 'Owner Tetap Aktif',
                'email' => 'owner.tetap@example.com',
            ]);

        $this->actingAs($admin)
            ->post(route('admin.users.activate', $owner));

        \Illuminate\Support\Facades\Auth::logout();

        $this->post('/login', [
            'email' => 'owner.tetap@example.com',
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($owner);
    }

    public function test_umkm_ownership_is_unchanged_by_user_management(): void
    {
        $admin = $this->administrator();
        $owner = $this->ownerWithUmkm();
        $umkmId = $owner->umkm->id;

        $this->actingAs($admin)
            ->put(route('admin.users.update', $owner), [
                'name' => 'Owner Diubah',
                'email' => 'owner.diubah@example.com',
            ]);

        $this->actingAs($admin)
            ->post(route('admin.users.suspend', $owner));

        $this->assertSame($owner->id, Umkm::findOrFail($umkmId)->fresh()->user_id);
        $this->assertSame($owner->id, $owner->umkm->user_id);
    }

    public function test_actions_are_recorded_in_activity_log(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();

        $this->actingAs($admin)
            ->post(route('admin.users.suspend', $owner));

        $this->actingAs($admin)
            ->post(route('admin.users.activate', $owner));

        $this->assertDatabaseHas('activity_log', [
            'event' => 'user_suspended',
            'subject_type' => User::class,
            'subject_id' => $owner->id,
            'causer_id' => $admin->id,
        ]);

        $this->assertDatabaseHas('activity_log', [
            'event' => 'user_activated',
            'subject_type' => User::class,
            'subject_id' => $owner->id,
            'causer_id' => $admin->id,
        ]);
    }

    public function test_activity_log_never_contains_passwords(): void
    {
        $admin = $this->administrator();
        $owner = $this->owner();

        $this->actingAs($admin)
            ->post(route('admin.users.reset-password', $owner), [
                'password' => 'rahasia-super',
                'password_confirmation' => 'rahasia-super',
            ]);

        $activities = \Spatie\Activitylog\Models\Activity::query()
            ->where('subject_type', User::class)
            ->where('subject_id', $owner->id)
            ->get();

        $this->assertNotEmpty($activities);
        foreach ($activities as $activity) {
            $this->assertStringNotContainsString('rahasia-super', $activity->description);
            $this->assertStringNotContainsString('rahasia-super', $activity->properties->toJson());
        }
    }

    public function test_index_shows_owner_count_and_empty_state(): void
    {
        $admin = $this->administrator();

        $this->actingAs($admin)
            ->get(route('admin.users.index'))
            ->assertOk()
            ->assertSee('Belum ada pengguna');
    }
}