<?php

namespace App\Support;

use App\Models\Product;
use App\Models\Umkm;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use Spatie\Activitylog\Models\Activity;

/**
 * Logs verification lifecycle events (submit, approve, needs revision,
 * reject) for UMKM and Product subjects using the documented Spatie
 * activity log table. Descriptions are written in Indonesian so they can
 * be displayed verbatim on the owner dashboard.
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
        return match ($event) {
            'submitted' => $subject instanceof Umkm
                ? 'Pengajuan UMKM Anda dikirim untuk diperiksa'
                : "Pengajuan produk {$subject->name} dikirim untuk diperiksa",
            'approved' => $subject instanceof Umkm
                ? 'UMKM Anda disetujui'
                : "Produk {$subject->name} disetujui",
            'needs_revision' => $subject instanceof Umkm
                ? 'UMKM Anda perlu diperbaiki'
                : "Produk {$subject->name} perlu diperbaiki",
            'rejected' => $subject instanceof Umkm
                ? 'UMKM Anda ditolak'
                : "Produk {$subject->name} ditolak",
            default => throw new InvalidArgumentException("Unsupported verification activity event [{$event}]."),
        };
    }
}
