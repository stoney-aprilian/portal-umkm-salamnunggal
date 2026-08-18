<?php

namespace App\Support;

use App\Models\Category;
use App\Models\User;
use InvalidArgumentException;

/**
 * Logs administrator category-management actions (create, update,
 * delete) on the category subject using the documented Spatie activity
 * log table. The administrator performing the action is recorded as the
 * causer.
 */
class CategoryActivity
{
    public static function log(string $event, Category $subject, User $causer): void
    {
        activity()
            ->causedBy($causer)
            ->performedOn($subject)
            ->event($event)
            ->log(self::descriptionFor($event, $subject));
    }

    private static function descriptionFor(string $event, Category $subject): string
    {
        $label = $subject->type === 'umkm' ? 'UMKM' : 'Produk';

        return match ($event) {
            'category_created' => "Kategori {$label} {$subject->name} dibuat",
            'category_updated' => "Kategori {$label} {$subject->name} diperbarui",
            'category_deleted' => "Kategori {$label} {$subject->name} dihapus",
            default => throw new InvalidArgumentException("Unsupported category activity event [{$event}]."),
        };
    }
}