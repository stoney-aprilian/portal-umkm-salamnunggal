<?php

namespace App\Support;

use App\Models\Umkm;
use App\Models\User;
use InvalidArgumentException;

/**
 * Logs media lifecycle events performed by the administrator (upload,
 * replace, delete) for UMKM subjects using the documented Spatie
 * activity log table. The administrator is always the causer.
 */
class MediaManagementActivity
{
    public static function log(string $event, Umkm $umkm, User $causer, string $collection): void
    {
        activity()
            ->causedBy($causer)
            ->performedOn($umkm)
            ->event($event)
            ->log(self::descriptionFor($event, $umkm, $collection));
    }

    private static function collectionLabel(string $collection): string
    {
        return match ($collection) {
            'logo' => 'Logo',
            'banner' => 'Banner',
            'gallery' => 'Galeri',
            default => $collection,
        };
    }

    private static function descriptionFor(string $event, Umkm $umkm, string $collection): string
    {
        $label = self::collectionLabel($collection);

        return match ($event) {
            'media_uploaded' => "{$label} diunggah oleh administrator untuk UMKM {$umkm->name}",
            'media_replaced' => "{$label} diganti oleh administrator untuk UMKM {$umkm->name}",
            'media_deleted' => "{$label} dihapus oleh administrator dari UMKM {$umkm->name}",
            default => throw new InvalidArgumentException("Unsupported media management activity event [{$event}]."),
        };
    }
}