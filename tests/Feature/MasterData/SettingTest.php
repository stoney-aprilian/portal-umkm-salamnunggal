<?php

namespace Tests\Feature\MasterData;

use App\Models\Setting;
use Database\Seeders\SettingSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SettingTest extends TestCase
{
    use RefreshDatabase;

    public function test_migration_creates_expected_table_and_columns(): void
    {
        $this->assertTrue(Schema::hasTable('settings'));

        $columns = ['id', 'key', 'value', 'group', 'created_at', 'updated_at'];

        foreach ($columns as $column) {
            $this->assertTrue(Schema::hasColumn('settings', $column), "missing column: {$column}");
        }

        $this->assertTrue(Schema::hasIndex('settings', 'settings_key_unique'));
    }

    public function test_valid_setting_can_be_created(): void
    {
        $setting = Setting::create([
            'key' => 'site_name',
            'value' => 'Portal UMKM Salamnunggal',
            'group' => 'site',
        ]);

        $this->assertDatabaseHas('settings', [
            'id' => $setting->id,
            'key' => 'site_name',
            'value' => 'Portal UMKM Salamnunggal',
            'group' => 'site',
        ]);
    }

    public function test_key_must_be_unique(): void
    {
        Setting::create([
            'key' => 'site_name',
            'value' => 'first',
        ]);

        $this->assertThrows(
            fn () => Setting::create([
                'key' => 'site_name',
                'value' => 'second',
            ]),
            QueryException::class,
        );

        $this->assertDatabaseCount('settings', 1);
    }

    public function test_group_is_optional(): void
    {
        Setting::create([
            'key' => 'site_name',
            'value' => 'Portal UMKM Salamnunggal',
        ]);

        $this->assertNull(Setting::where('key', 'site_name')->first()->group);
    }

    public function test_value_is_required(): void
    {
        $this->assertThrows(
            fn () => DB::table('settings')->insert([
                'key' => 'empty_value',
                'value' => null,
            ]),
            QueryException::class,
        );

        $this->assertDatabaseCount('settings', 0);
    }

    public function test_value_stores_text_content(): void
    {
        $longValue = str_repeat('a', 2000);

        Setting::create([
            'key' => 'long_value',
            'value' => $longValue,
        ]);

        $this->assertSame($longValue, Setting::where('key', 'long_value')->first()->value);
    }

    public function test_seeder_creates_documented_default_settings(): void
    {
        $this->seed(SettingSeeder::class);

        $this->assertDatabaseCount('settings', 5);

        foreach (['site.name', 'contact.address', 'contact.phone', 'contact.email', 'contact.hours'] as $key) {
            $this->assertDatabaseHas('settings', ['key' => $key]);
        }
    }

    public function test_seeder_is_idempotent(): void
    {
        $this->seed(SettingSeeder::class);
        $this->seed(SettingSeeder::class);

        $this->assertDatabaseCount('settings', 5);
    }
}
