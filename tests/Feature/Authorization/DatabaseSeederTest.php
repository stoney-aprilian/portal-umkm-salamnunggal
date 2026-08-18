<?php

namespace Tests\Feature\Authorization;

use App\Models\Category;
use App\Models\Setting;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_seeder_creates_owner_and_administrator_roles(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseHas('roles', ['name' => 'owner', 'guard_name' => 'web']);
        $this->assertDatabaseHas('roles', ['name' => 'administrator', 'guard_name' => 'web']);
    }

    public function test_database_seeder_is_idempotent(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->seed(DatabaseSeeder::class);

        $this->assertSame(2, Role::count());
    }

    public function test_database_seeder_does_not_create_users(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseCount('users', 0);
    }

    public function test_database_seeder_creates_master_categories(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseHas('categories', ['type' => 'umkm', 'slug' => 'kuliner']);
        $this->assertDatabaseHas('categories', ['type' => 'product', 'slug' => 'makanan']);
    }

    public function test_database_seeder_creates_portal_settings(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseHas('settings', ['key' => 'site.name']);
        $this->assertDatabaseHas('settings', ['key' => 'site.tagline']);
        $this->assertDatabaseHas('settings', ['key' => 'site.description']);
        $this->assertDatabaseHas('settings', ['key' => 'site.hero_title']);
        $this->assertDatabaseHas('settings', ['key' => 'site.hero_description']);
        $this->assertDatabaseHas('settings', ['key' => 'contact.address']);
        $this->assertDatabaseHas('settings', ['key' => 'contact.phone']);
        $this->assertDatabaseHas('settings', ['key' => 'contact.whatsapp']);
        $this->assertDatabaseHas('settings', ['key' => 'contact.email']);
        $this->assertDatabaseHas('settings', ['key' => 'contact.hours']);
    }

    public function test_database_seeder_is_idempotent_with_master_data(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->seed(DatabaseSeeder::class);

        $this->assertSame(2, Role::count());
        $this->assertSame(2, Category::count());
        $this->assertSame(10, Setting::count());
    }
}
