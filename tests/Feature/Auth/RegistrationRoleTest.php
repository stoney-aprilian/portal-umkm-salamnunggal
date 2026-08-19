<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationRoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_newly_registered_user_receives_owner_role(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $this->post('/register', [
            'name' => 'Owner User',
            'email' => 'owner@example.com',
            'phone' => '081234567890',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $user = User::where('email', 'owner@example.com')->firstOrFail();

        $this->assertTrue($user->hasRole('owner'));
        $this->assertTrue($user->hasExactRoles('owner'));
        $this->assertFalse($user->hasRole('administrator'));
    }

    public function test_registration_does_not_accept_a_role_field(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $response = $this->post('/register', [
            'name' => 'Role User',
            'email' => 'role@example.com',
            'phone' => '081234567890',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'administrator',
        ]);

        $response->assertRedirect(route('account.verification.notice', absolute: false));

        $user = User::where('email', 'role@example.com')->firstOrFail();

        $this->assertTrue($user->hasExactRoles('owner'));
    }
}
