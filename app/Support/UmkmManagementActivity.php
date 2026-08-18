<?php

namespace App\Support;

use App\Models\Umkm;
use App\Models\User;
use InvalidArgumentException;

/**
 * Logs UMKM lifecycle events performed by the administrator (create,
 * update, delete) so every assisted-service action is recorded with the
 * administrator as causer and the UMKM as subject.
 */
class UmkmManagementActivity
{
    public static function log(string $event, Umkm $umkm, User $causer): void
    {
        activity()
            ->causedBy($causer)
            ->performedOn($umkm)
            ->event($event)
            ->log(self::descriptionFor($event, $umkm));
    }

    private static function descriptionFor(string $event, Umkm $umkm): string
    {
        return match ($event) {
            'umkm_created' => "UMKM {$umkm->name} dibuat oleh administrator atas nama pemilik",
            'umkm_updated' => "UMKM {$umkm->name} diperbarui oleh administrator",
            'umkm_deleted' => "UMKM {$umkm->name} dihapus oleh administrator",
            default => throw new InvalidArgumentException("Unsupported UMKM management activity event [{$event}]."),
        };
    }
}