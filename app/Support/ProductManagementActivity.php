<?php

namespace App\Support;

use App\Models\Product;
use App\Models\User;
use InvalidArgumentException;

/**
 * Logs product lifecycle events (create, update, delete) and product
 * media events (upload, replace, delete) using the documented Spatie
 * activity log table. The acting user is the causer and the product is
 * the subject; deletion may be performed by an administrator or by the
 * owner of the product's UMKM.
 */
class ProductManagementActivity
{
    public static function log(string $event, Product $product, User $causer): void
    {
        activity()
            ->causedBy($causer)
            ->performedOn($product)
            ->event($event)
            ->log(self::descriptionFor($event, $product));
    }

    private static function descriptionFor(string $event, Product $product): string
    {
        $umkmName = $product->umkm?->name ?? '—';

        return match ($event) {
            'product_created' => "Produk {$product->name} dibuat oleh administrator untuk UMKM {$umkmName}",
            'product_updated' => "Produk {$product->name} diperbarui oleh administrator",
            'product_deleted' => "Produk {$product->name} dihapus",
            'product_media_uploaded' => "Foto produk {$product->name} diunggah oleh administrator",
            'product_media_replaced' => "Foto produk {$product->name} diganti oleh administrator",
            'product_media_deleted' => "Foto produk {$product->name} dihapus oleh administrator",
            default => throw new InvalidArgumentException("Unsupported product management activity event [{$event}]."),
        };
    }
}