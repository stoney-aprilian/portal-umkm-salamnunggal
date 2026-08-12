<?php

namespace Tests\Feature\Authorization;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class RoleAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
    }

    public function test_user_with_owner_role_is_recognized_as_owner(): void
    {
        $owner = User::factory()->create();
        $owner->assignRole('owner');

        $this->assertTrue($owner->hasRole('owner'));
    }

    public function test_user_with_administrator_role_is_recognized_as_administrator(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('administrator');

        $this->assertTrue($admin->hasRole('administrator'));
    }

    public function test_owner_does_not_satisfy_administrator_role_check(): void
    {
        $owner = User::factory()->create();
        $owner->assignRole('owner');

        $this->assertFalse($owner->hasRole('administrator'));
        $this->assertFalse($owner->hasAnyRole(['administrator']));
    }

    public function test_administrator_does_not_satisfy_owner_role_check(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('administrator');

        $this->assertFalse($admin->hasRole('owner'));
        $this->assertFalse($admin->hasAnyRole(['owner']));
    }

    public function test_role_middleware_allows_owner_on_owner_protected_route(): void
    {
        Route::get('/_test/owner-only', fn () => 'ok')->middleware('role:owner');

        $owner = User::factory()->create();
        $owner->assignRole('owner');

        $this->actingAs($owner)
            ->get('/_test/owner-only')
            ->assertOk();
    }

    public function test_role_middleware_blocks_administrator_on_owner_protected_route(): void
    {
        Route::get('/_test/owner-only', fn () => 'ok')->middleware('role:owner');

        $admin = User::factory()->create();
        $admin->assignRole('administrator');

        $this->actingAs($admin)
            ->get('/_test/owner-only')
            ->assertForbidden();
    }

    public function test_role_middleware_blocks_guest_on_protected_route(): void
    {
        Route::get('/_test/owner-only', fn () => 'ok')->middleware('role:owner');

        $this->get('/_test/owner-only')
            ->assertForbidden();
    }

    public function test_authenticated_role_middleware_redirects_guest_to_login(): void
    {
        Route::get('/_test/owner-only', fn () => 'ok')->middleware(['auth', 'role:owner']);

        $this->get('/_test/owner-only')
            ->assertRedirect(route('login'));
    }
}
