<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Umkm;
use App\Models\User;
use App\Models\VerificationRequest;
use App\Support\VerificationActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Development/demo dataset for local inspection of the public portal,
 * owner, and administrator workflows.
 *
 * - Demo data only: names, emails, and phone numbers are fictional and
 *   must never be treated as canonical Salamnunggal data.
 * - Deliberately separate from DatabaseSeeder (canonical provisioning);
 *   invoke explicitly with: php artisan db:seed --class=DemoDataSeeder.
 * - Safe to rerun: all records are keyed on deterministic identifiers
 *   (emails, slugs, statuses) and never use destructive commands.
 * - Blocked outside the local/testing environments.
 */
class DemoDataSeeder extends Seeder
{
    private const DEMO_PASSWORD = 'password';

    public function run(): void
    {
        $this->guardEnvironment();

        $this->call(RolePermissionSeeder::class);
        $this->call(CategorySeeder::class);

        $kuliner = Category::query()->where('type', 'umkm')->where('slug', 'kuliner')->firstOrFail();
        $makanan = Category::query()->where('type', 'product')->where('slug', 'makanan')->firstOrFail();

        $demoAdmin = $this->demoUser('administrator.demo@example.test', 'Demo Administrator', 'administrator');
        $demoOwner = $this->demoUser('owner.demo@example.test', 'Demo Owner', 'owner');

        $approvedUmkm = $this->demoUmkm($demoOwner, $kuliner, 'UMKM Demo Kuliner Salamnunggal', 'approved', [
            'description' => 'Demo: usaha kuliner contoh untuk keperluan pengembangan. Bukan data usaha asli.',
            'address' => 'Jl. Demo No. 1, Desa Salamnunggal',
            'operational_hours' => 'Senin-Minggu, 08.00-21.00 WIB',
            'phone' => '081234567890',
            'email' => 'umkm.demo@example.test',
        ]);
        $this->demoVerification($approvedUmkm, $demoOwner, $demoAdmin, 'approved', 'Demo: disetujui.');

        $pendingOwner = $this->demoUser('owner.pending.demo@example.test', 'Demo Owner Pending', 'owner');
        $pendingUmkm = $this->demoUmkm($pendingOwner, $kuliner, 'UMKM Demo Katering Salamnunggal', 'pending');
        $this->demoVerification($pendingUmkm, $pendingOwner, null, 'pending');

        $revisionOwner = $this->demoUser('owner.revision.demo@example.test', 'Demo Owner Revisi', 'owner');
        $revisionUmkm = $this->demoUmkm($revisionOwner, $kuliner, 'UMKM Demo Cemilan Salamnunggal', 'needs_revision');
        $this->demoVerification($revisionUmkm, $revisionOwner, $demoAdmin, 'needs_revision', 'Demo: mohon lengkapi alamat dan jam operasional.');

        $rejectedOwner = $this->demoUser('owner.rejected.demo@example.test', 'Demo Owner Ditolak', 'owner');
        $rejectedUmkm = $this->demoUmkm($rejectedOwner, $kuliner, 'UMKM Demo Minuman Salamnunggal', 'rejected');
        $this->demoVerification($rejectedUmkm, $rejectedOwner, $demoAdmin, 'rejected', 'Demo: profil belum memenuhi ketentuan.');

        $approvedProduct = $this->demoProduct($approvedUmkm, $makanan, 'Produk Demo Keripik Singkong', 'approved', 15000, [
            'description' => 'Demo: keripik singkong contoh untuk keperluan pengembangan. Bukan produk asli.',
        ]);
        $this->demoVerification($approvedProduct, $demoOwner, $demoAdmin, 'approved', 'Demo: disetujui.');

        $pendingProduct = $this->demoProduct($approvedUmkm, $makanan, 'Produk Demo Sambal Rumahan', 'pending', 25000);
        $this->demoVerification($pendingProduct, $demoOwner, null, 'pending');

        $revisionProduct = $this->demoProduct($approvedUmkm, $makanan, 'Produk Demo Abon Sapi', 'needs_revision', 45000);
        $this->demoVerification($revisionProduct, $demoOwner, $demoAdmin, 'needs_revision', 'Demo: tambahkan deskripsi produk.');

        $rejectedProduct = $this->demoProduct($approvedUmkm, $makanan, 'Produk Demo Kue Kering', 'rejected', 30000);
        $this->demoVerification($rejectedProduct, $demoOwner, $demoAdmin, 'rejected', 'Demo: harga tidak sesuai ketentuan.');
    }

    private function guardEnvironment(): void
    {
        if (! app()->environment('local', 'testing')) {
            throw new RuntimeException('DemoDataSeeder is development-only and may only run in the local or testing environment.');
        }
    }

    private function demoUser(string $email, string $name, string $role): User
    {
        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make(self::DEMO_PASSWORD),
                'email_verified_at' => now(),
                'status' => 'approved',
            ],
        );

        $user->assignRole($role);

        return $user;
    }

    private function demoUmkm(User $owner, Category $category, string $name, string $status, array $extra = []): Umkm
    {
        return Umkm::updateOrCreate(
            ['slug' => Str::slug($name)],
            array_merge([
                'user_id' => $owner->id,
                'category_id' => $category->id,
                'name' => $name,
                'status' => $status,
            ], $extra),
        );
    }

    private function demoProduct(Umkm $umkm, Category $category, string $name, string $status, int $price, array $extra = []): Product
    {
        return Product::updateOrCreate(
            ['slug' => Str::slug($name)],
            array_merge([
                'umkm_id' => $umkm->id,
                'category_id' => $category->id,
                'name' => $name,
                'price' => $price,
                'status' => $status,
            ], $extra),
        );
    }

    private function demoVerification(Model $verifiable, User $owner, ?User $reviewer, string $status, ?string $notes = null): void
    {
        $verifiable->verificationRequests()->firstOrCreate(
            ['status' => $status],
            [
                'user_id' => $owner->id,
                'reviewer_id' => $reviewer?->id,
                'notes' => $notes,
                'reviewed_at' => $status === 'pending' ? null : now(),
            ],
        );

        if ($reviewer === null) {
            VerificationActivity::logOnce('submitted', $verifiable, $owner);

            return;
        }

        VerificationActivity::logOnce($status, $verifiable, $reviewer);
    }
}
