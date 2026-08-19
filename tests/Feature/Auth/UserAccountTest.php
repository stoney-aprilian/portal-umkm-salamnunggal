<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class UserAccountTest extends TestCase
{
    use RefreshDatabase;

    public function test_phone_column_exists(): void
    {
        $this->assertTrue(Schema::hasColumn('users', 'phone'));
    }

    public function test_status_column_exists(): void
    {
        $this->assertTrue(Schema::hasColumn('users', 'status'));
    }

    public function test_phone_is_nullable(): void
    {
        $user = User::factory()->create(['phone' => null]);

        $this->assertNull($user->fresh()->phone);
    }

    public function test_phone_can_be_stored(): void
    {
        $user = User::factory()->create(['phone' => '081234567890']);

        $this->assertSame('081234567890', $user->fresh()->phone);
    }

    public function test_status_defaults_to_pending_at_database_level(): void
    {
        $id = DB::table('users')->insertGetId([
            'name' => 'Baru',
            'email' => 'baru@example.com',
            'password' => Hash::make('password'),
        ]);

        $this->assertSame('pending', User::find($id)->status);
    }

    public function test_factory_defaults_to_approved_for_test_actors(): void
    {
        $user = User::factory()->create();

        $this->assertSame('approved', $user->fresh()->status);
    }

    public function test_all_documented_status_values_are_valid(): void
    {
        $statuses = ['pending', 'approved', 'needs_revision', 'rejected', 'suspended'];

        foreach ($statuses as $status) {
            User::factory()->create(['status' => $status]);
        }

        foreach ($statuses as $status) {
            $this->assertDatabaseHas('users', ['status' => $status]);
        }
    }

    public function test_invalid_status_is_rejected(): void
    {
        $this->assertThrows(
            fn () => DB::table('users')->insert([
                'name' => 'Invalid',
                'email' => 'invalid@example.com',
                'password' => Hash::make('password'),
                'status' => 'active',
            ]),
            QueryException::class,
        );

        $this->assertDatabaseCount('users', 0);
    }

    public function test_registration_creates_user_with_pending_status(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->post('/register', [
            'name' => 'Budi',
            'email' => 'budi@example.com',
            'phone' => '081234567890',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertRedirect(route('account.verification.notice'));

        $this->assertSame('pending', User::where('email', 'budi@example.com')->first()->status);
    }

    public function test_registration_requires_phone(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->post('/register', [
            'name' => 'Siti',
            'email' => 'siti@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertSessionHasErrors('phone');

        $this->assertDatabaseMissing('users', ['email' => 'siti@example.com']);
    }

    public function test_registration_stores_valid_phone(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->post('/register', [
            'name' => 'Siti',
            'email' => 'siti@example.com',
            'phone' => '081234567890',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertRedirect(route('account.verification.notice'));

        $user = User::where('email', 'siti@example.com')->first();

        $this->assertNotNull($user);
        $this->assertSame('081234567890', $user->phone);
    }

    public function test_registration_ignores_submitted_status(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->post('/register', [
            'name' => 'Agus',
            'email' => 'agus@example.com',
            'phone' => '081234567890',
            'password' => 'password',
            'password_confirmation' => 'password',
            'status' => 'suspended',
        ])->assertRedirect(route('account.verification.notice'));

        $user = User::where('email', 'agus@example.com')->first();

        $this->assertSame('pending', $user->status);
    }

    public function test_unverified_user_can_access_dashboard_because_verification_is_optional(): void
    {
        $this->seed(DatabaseSeeder::class);

        $user = User::factory()->unverified()->create();
        $user->assignRole('owner');

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk();
    }
}
