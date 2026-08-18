<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserStatusEnforcementTest extends TestCase
{
    use RefreshDatabase;

    public function test_suspended_user_cannot_login(): void
    {
        User::factory()->create([
            'email' => 'suspended@example.com',
            'password' => 'password',
            'status' => 'suspended',
        ]);

        $this->post('/login', [
            'email' => 'suspended@example.com',
            'password' => 'password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_approved_user_can_login(): void
    {
        User::factory()->create([
            'email' => 'aktif@example.com',
            'password' => 'password',
            'status' => 'approved',
        ]);

        $this->post('/login', [
            'email' => 'aktif@example.com',
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticated();
    }

    public function test_suspended_user_session_is_terminated_when_accessing_authenticated_area(): void
    {
        $this->seed(DatabaseSeeder::class);

        $user = User::factory()->create(['status' => 'suspended']);
        $user->assignRole('owner');

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_suspended_user_cannot_reach_owner_area(): void
    {
        $this->seed(DatabaseSeeder::class);

        $user = User::factory()->create(['status' => 'suspended']);
        $user->assignRole('owner');

        $this->actingAs($user)
            ->get(route('owner.umkm.create'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_suspended_user_cannot_reach_admin_area(): void
    {
        $this->seed(DatabaseSeeder::class);

        $user = User::factory()->create(['status' => 'suspended']);
        $user->assignRole('administrator');

        $this->actingAs($user)
            ->get(route('admin.dashboard'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_pending_user_is_redirected_from_dashboard_to_verification_notice(): void
    {
        $this->seed(DatabaseSeeder::class);

        $user = User::factory()->create(['status' => 'pending']);
        $user->assignRole('owner');

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertRedirect(route('account.verification.notice'));
    }

    public function test_pending_user_is_redirected_from_owner_area_to_verification_notice(): void
    {
        $this->seed(DatabaseSeeder::class);

        $user = User::factory()->create(['status' => 'pending']);
        $user->assignRole('owner');

        $this->actingAs($user)
            ->get(route('owner.umkm.create'))
            ->assertRedirect(route('account.verification.notice'));
    }

    public function test_needs_revision_user_is_redirected_from_dashboard_to_verification_notice(): void
    {
        $this->seed(DatabaseSeeder::class);

        $user = User::factory()->create(['status' => 'needs_revision']);
        $user->assignRole('owner');

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertRedirect(route('account.verification.notice'));
    }

    public function test_rejected_user_is_redirected_from_dashboard_to_verification_notice(): void
    {
        $this->seed(DatabaseSeeder::class);

        $user = User::factory()->create(['status' => 'rejected']);
        $user->assignRole('owner');

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertRedirect(route('account.verification.notice'));
    }

    public function test_pending_user_can_reach_verification_notice_page(): void
    {
        $this->seed(DatabaseSeeder::class);

        $user = User::factory()->create(['status' => 'pending']);
        $user->assignRole('owner');

        $this->actingAs($user)
            ->get(route('account.verification.notice'))
            ->assertOk();
    }

    public function test_approved_owner_can_access_dashboard(): void
    {
        $this->seed(DatabaseSeeder::class);

        $user = User::factory()->create(['status' => 'approved']);
        $user->assignRole('owner');

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk();
    }
}