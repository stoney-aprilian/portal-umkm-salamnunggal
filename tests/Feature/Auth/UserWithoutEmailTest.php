<?php

namespace Tests\Feature\Auth;

use App\Models\Umkm;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserWithoutEmailTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_table_allows_null_email(): void
    {
        $user = User::create([
            'name' => 'Budi Tanpa Email',
            'email' => null,
            'password' => Hash::make('password'),
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'email' => null,
        ]);
    }

    public function test_unique_index_still_prevents_duplicate_emails(): void
    {
        User::create([
            'name' => 'Pemilik A',
            'email' => 'pemilik.a@example.com',
            'password' => Hash::make('password'),
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);

        User::create([
            'name' => 'Pemilik B',
            'email' => 'pemilik.a@example.com',
            'password' => Hash::make('password'),
        ]);
    }

    public function test_owner_without_email_can_be_created_through_backend(): void
    {
        $user = User::create([
            'name' => 'Ibu Siti',
            'email' => null,
            'phone' => '081234567890',
            'password' => Hash::make('rahasia'),
            'status' => 'approved',
        ]);

        $this->assertNotNull($user->id);
        $this->assertNull($user->fresh()->email);
        $this->assertSame('081234567890', $user->fresh()->phone);
    }

    public function test_owner_role_can_be_assigned_to_user_without_email(): void
    {
        $this->seed(DatabaseSeeder::class);

        $user = User::create([
            'name' => 'Pak Joko',
            'email' => null,
            'password' => Hash::make('rahasia'),
        ]);

        $user->assignRole('owner');

        $this->assertTrue($user->fresh()->hasRole('owner'));
        $this->assertDatabaseHas('model_has_roles', [
            'role_id' => $user->roles->first()->id,
            'model_id' => $user->id,
        ]);
    }

    public function test_umkm_relationship_works_for_user_without_email(): void
    {
        $this->seed(DatabaseSeeder::class);

        $user = User::create([
            'name' => 'Bu Endah',
            'email' => null,
            'password' => Hash::make('rahasia'),
        ]);

        $umkm = Umkm::create([
            'user_id' => $user->id,
            'category_id' => \App\Models\Category::where('type', 'umkm')->first()->id,
            'name' => 'Warung Endah',
            'slug' => Umkm::generateUniqueSlug('Warung Endah'),
            'status' => 'draft',
        ]);

        $this->assertTrue($user->umkm()->exists());
        $this->assertSame($umkm->id, $user->umkm->id);
        $this->assertSame($user->id, $umkm->fresh()->user_id);
    }

    public function test_registration_still_requires_email(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->post('/register', [
            'name' => 'Pendaftar Baru',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertSessionHasErrors('email');

        $this->assertDatabaseCount('users', 0);
    }

    public function test_login_still_requires_email_field(): void
    {
        $this->post('/login', [
            'password' => 'password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_owner_with_email_can_still_login(): void
    {
        $this->seed(DatabaseSeeder::class);

        $user = User::factory()->create(['email' => 'pemilik@example.com']);
        $user->assignRole('owner');

        $this->post('/login', [
            'email' => 'pemilik@example.com',
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
    }
}