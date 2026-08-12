# Portal UMKM Salamnunggal

Portal digital untuk UMKM di Desa Salamnunggal. Sistem menyediakan penemuan UMKM untuk publik, manajemen profil dan produk oleh pemilik UMKM, serta verifikasi dan moderasi oleh Administrator.

## Teknologi

- Laravel 11 (Blade-first, tanpa SPA)
- Blade Components + Tailwind CSS + Vite
- Spatie Laravel Permission untuk roles & permissions

## Peran

| Peran | Fungsi |
| --- | --- |
| Publik | Menjelajah dan mencari UMKM serta produk tanpa masuk akun |
| Owner | Mendaftarkan, mengelola, dan mengirim UMKM beserta produknya untuk diverifikasi |
| Administrator | Memverifikasi dan memoderasi pengajuan UMKM dan produk |

## Instalasi

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
npm install && npm run build
```

`php artisan storage:link` membuat symlink `public/storage` ke `storage/app/public` agar media (logo, banner, galeri UMKM, foto produk) dapat diakses publik.

## Pengujian

```bash
php artisan test
```

## Demo Lokal

Data demo bersifat fiktif (bukan data asli Salamnunggal) dan hanya dapat dijalankan di lingkungan lokal/testing:

```bash
php artisan db:seed --class=DemoDataSeeder
```

Seluruh akun demo menggunakan kata sandi `password`:

| Akun | Email | Peran |
| --- | --- | --- |
| Demo Administrator | `administrator.demo@example.test` | Administrator |
| Demo Owner | `owner.demo@example.test` | Owner (UMKM disetujui + produk) |
| Demo Owner Pending | `owner.pending.demo@example.test` | Owner (UMKM menunggu pemeriksaan) |
| Demo Owner Revisi | `owner.revision.demo@example.test` | Owner (UMKM perlu revisi) |
| Demo Owner Ditolak | `owner.rejected.demo@example.test` | Owner (UMKM ditolak) |

Seeder aman dijalankan ulang (idempotent) tanpa perintah destruktif.

## Deployment

Checklist sebelum hosting:

1. `cp .env.example .env`, set:
   - `APP_ENV=production`
   - `APP_DEBUG=false`
   - `APP_URL=https://domain-anda`
   - `APP_TIMEZONE=Asia/Jakarta`
2. `php artisan key:generate`
3. `php artisan migrate --force --seed` (roles, kategori, settings)
4. `php artisan storage:link` (media logo/banner/galeri/foto produk)
5. `npm install && npm run build` (aset produksi)
6. Aktifkan `SESSION_SECURE_COOKIE=true` bila menggunakan HTTPS
7. Jalankan `php artisan config:cache`, `php artisan route:cache`, `php artisan view:cache`
8. Buat backup database rutin dan pantau log aplikasi

## Fitur yang Belum Tersedia

Fitur berikut terdokumentasi namun belum diimplementasikan dan sengaja tidak diklaim:

- **Moderasi perubahan data approved**: pemilik belum dapat mengajukan perubahan UMKM/produk yang sudah disetujui.
- **Verifikasi akun Owner**: akun baru langsung aktif (`users.status` belum ditegakkan).
- **Kelola pengguna, kategori, dan settings** oleh Administrator: belum ada antarmuka admin.
- **Email verification** terpasang namun belum diaktifkan pada route mana pun.

## Lisensi

Hak cipta dilindungi. Untuk keperluan部署 atau kontribusi, hubungi pengelola Desa Salamnunggal.
