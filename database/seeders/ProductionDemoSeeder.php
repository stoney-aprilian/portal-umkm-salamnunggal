<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Media;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Umkm;
use App\Models\User;
use App\Models\VerificationRequest;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class ProductionDemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RolePermissionSeeder::class);
        $this->call(CategorySeeder::class);
        $this->call(SettingSeeder::class);

        $adminRole = Role::findByName('administrator', 'web');
        $ownerRole = Role::findByName('owner', 'web');

        $kuliner = Category::query()->where('type', 'umkm')->where('slug', 'kuliner')->firstOrFail();
        $makanan = Category::query()->where('type', 'product')->where('slug', 'makanan')->firstOrFail();

        $admin = User::updateOrCreate(
            ['email' => 'admin@demo.test'],
            [
                'name' => 'Demo Administrator',
                'password' => Hash::make('Demo2026!Render'),
                'status' => 'approved',
            ],
        );
        $admin->syncRoles([$adminRole]);

        $owner = User::updateOrCreate(
            ['email' => 'owner@demo.test'],
            [
                'name' => 'Demo Owner',
                'password' => Hash::make('Demo2026!Render'),
                'status' => 'approved',
            ],
        );
        $owner->syncRoles([$ownerRole]);

        $umkm = Umkm::updateOrCreate(
            ['slug' => Str::slug('UMKM Demo Kuliner Salamnunggal')],
            [
                'user_id' => $owner->id,
                'category_id' => $kuliner->id,
                'name' => 'UMKM Demo Kuliner Salamnunggal',
                'status' => 'approved',
                'description' => 'Demo UMKM untuk presentasi deploy Render.',
                'address' => 'Jl. Demo No. 1, Desa Salamnunggal',
                'operational_hours' => 'Senin-Minggu, 08.00-21.00 WIB',
                'phone' => '081234567890',
                'email' => 'umkm.demo@example.test',
            ],
        );

        VerificationRequest::updateOrCreate(
            [
                'verifiable_type' => Umkm::class,
                'verifiable_id' => $umkm->id,
                'status' => 'approved',
            ],
            [
                'user_id' => $owner->id,
                'reviewer_id' => $admin->id,
                'notes' => 'Demo: disetujui untuk deploy.',
                'reviewed_at' => now(),
            ],
        );

        $product = Product::updateOrCreate(
            ['slug' => Str::slug('Produk Demo Keripik Singkong')],
            [
                'umkm_id' => $umkm->id,
                'category_id' => $makanan->id,
                'name' => 'Produk Demo Keripik Singkong',
                'price' => 15000,
                'status' => 'approved',
                'description' => 'Demo produk untuk presentasi deploy Render.',
            ],
        );

        VerificationRequest::updateOrCreate(
            [
                'verifiable_type' => Product::class,
                'verifiable_id' => $product->id,
                'status' => 'approved',
            ],
            [
                'user_id' => $owner->id,
                'reviewer_id' => $admin->id,
                'notes' => 'Demo: disetujui untuk deploy.',
                'reviewed_at' => now(),
            ],
        );

        Setting::updateOrCreate(
            ['key' => 'site.logo'],
            ['value' => 'branding/Z0QRDjFaqi255BG6XC55txnIKxICGE36aih2JADI.png', 'group' => 'site'],
        );

        Setting::updateOrCreate(
            ['key' => 'site.favicon'],
            ['value' => 'branding/GubwK2rgJTg4dONQV8JZis5SgqHGm7fZjrR2M6hh.png', 'group' => 'site'],
        );

        Media::updateOrCreate(
            [
                'disk' => 'public',
                'path' => 'umkms/1/BSOkmIJSiE7A2ajCZK7naBmPAg1eag8TDAnc0nYN.png',
                'collection' => 'logo',
            ],
            [
                'mediable_type' => Umkm::class,
                'mediable_id' => $umkm->id,
                'sort_order' => 0,
            ],
        );

        Media::updateOrCreate(
            [
                'disk' => 'public',
                'path' => 'umkms/1/gallery/FmrJl9HHSfmKZWzg4nkA40ouipQFVODtRqnPl2DO.jpg',
                'collection' => 'gallery',
            ],
            [
                'mediable_type' => Umkm::class,
                'mediable_id' => $umkm->id,
                'sort_order' => 0,
            ],
        );

        Media::updateOrCreate(
            [
                'disk' => 'public',
                'path' => 'umkms/1/gallery/IjjuLCOnWLkUSMtLPAWRPOyWyUTlhWWltCNMJSMs.jpg',
                'collection' => 'gallery',
            ],
            [
                'mediable_type' => Umkm::class,
                'mediable_id' => $umkm->id,
                'sort_order' => 1,
            ],
        );

        Media::updateOrCreate(
            [
                'disk' => 'public',
                'path' => 'products/1/kf9RKMFWJ1GteSRX00bprtDyBhhH305odPKmGCHQ.jpg',
                'collection' => 'product',
            ],
            [
                'mediable_type' => Product::class,
                'mediable_id' => $product->id,
                'sort_order' => 0,
            ],
        );
    }
}
