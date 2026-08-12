<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Seed the canonical portal settings.
     *
     * The values below are documented placeholders pending official
     * confirmation from the village administration. They keep the portal
     * functional for development, testing, and workshop presentations, and
     * can be updated in the `settings` table without code changes.
     */
    public function run(): void
    {
        $settings = [
            'site.name' => 'Portal UMKM Salamnunggal',
            'contact.address' => 'Kantor Desa Salamnunggal',
            'contact.phone' => '+62 812-3456-7890',
            'contact.email' => 'portal@umkm-salamnunggal.id',
            'contact.hours' => 'Senin - Jumat, 08.00 - 15.00 WIB',
        ];

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value, 'group' => str_contains($key, '.') ? explode('.', $key)[0] : 'general']);
        }
    }
}
