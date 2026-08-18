<?php

namespace App\Support;

use App\Models\Product;
use App\Models\ProductRevision;
use App\Models\Umkm;
use App\Models\UmkmRevision;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use Spatie\Activitylog\Models\Activity;

/**
 * Logs verification lifecycle events (submit, approve, needs revision,
 * reject) for UMKM, Product, change revision, and owner account subjects
 * using the documented Spatie activity log table. Descriptions are
 * written in Indonesian so they can be displayed verbatim on the owner
 * dashboard.
 */
class VerificationActivity
{
    public static function log(string $event, Model $subject, User $causer): void
    {
        activity()
            ->causedBy($causer)
            ->performedOn($subject)
            ->event($event)
            ->log(self::descriptionFor($event, $subject));
    }

    /**
     * Idempotent variant used by seeders so rerunning demo data never
     * duplicates activity entries.
     */
    public static function logOnce(string $event, Model $subject, User $causer): void
    {
        Activity::firstOrCreate(
            [
                'description' => self::descriptionFor($event, $subject),
                'subject_type' => $subject::class,
                'subject_id' => $subject->getKey(),
                'event' => $event,
                'causer_type' => $causer::class,
                'causer_id' => $causer->getKey(),
            ],
            ['log_name' => 'default'],
        );
    }

    private static function descriptionFor(string $event, Model $subject): string
    {
        return match (true) {
            $event === 'submitted' && $subject instanceof Umkm => 'Pengajuan UMKM Anda dikirim untuk diperiksa',
            $event === 'submitted' && $subject instanceof UmkmRevision => 'Pengajuan perubahan UMKM Anda dikirim untuk diperiksa',
            $event === 'submitted' && $subject instanceof Product => "Pengajuan produk {$subject->name} dikirim untuk diperiksa",
            $event === 'submitted' && $subject instanceof ProductRevision => "Pengajuan perubahan produk {$subject->name} dikirim untuk diperiksa",
            $event === 'approved' && $subject instanceof Umkm => 'UMKM Anda disetujui',
            $event === 'approved' && $subject instanceof UmkmRevision => 'Perubahan UMKM Anda disetujui',
            $event === 'approved' && $subject instanceof Product => "Produk {$subject->name} disetujui",
            $event === 'approved' && $subject instanceof ProductRevision => "Perubahan produk {$subject->name} disetujui",
            $event === 'needs_revision' && $subject instanceof Umkm => 'UMKM Anda perlu diperbaiki',
            $event === 'needs_revision' && $subject instanceof UmkmRevision => 'Perubahan UMKM Anda perlu diperbaiki',
            $event === 'needs_revision' && $subject instanceof Product => "Produk {$subject->name} perlu diperbaiki",
            $event === 'needs_revision' && $subject instanceof ProductRevision => "Perubahan produk {$subject->name} perlu diperbaiki",
            $event === 'rejected' && $subject instanceof Umkm => 'UMKM Anda ditolak',
            $event === 'rejected' && $subject instanceof UmkmRevision => 'Perubahan UMKM Anda ditolak',
            $event === 'rejected' && $subject instanceof Product => "Produk {$subject->name} ditolak",
            $event === 'rejected' && $subject instanceof ProductRevision => "Perubahan produk {$subject->name} ditolak",
            $event === 'submitted' && $subject instanceof User => 'Pengajuan verifikasi akun Anda dikirim untuk diperiksa',
            $event === 'approved' && $subject instanceof User => 'Akun Anda disetujui',
            $event === 'needs_revision' && $subject instanceof User => 'Akun Anda perlu diperbaiki',
            $event === 'rejected' && $subject instanceof User => 'Akun Anda ditolak',
            default => throw new InvalidArgumentException("Unsupported verification activity event [{$event}]."),
        };
    }
}