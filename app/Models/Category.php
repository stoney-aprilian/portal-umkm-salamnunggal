<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

#[Fillable(['type', 'name', 'slug', 'description'])]
class Category extends \Illuminate\Database\Eloquent\Model
{
    /**
     * Generates a globally unique slug for the given name, following the
     * same mechanism used by Umkm and Product. The slug column is unique
     * across all categories regardless of type.
     */
    public static function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'kategori';
        $slug = $base;
        $counter = 2;

        while (static::where('slug', $slug)
            ->when($ignoreId !== null, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->exists()
        ) {
            $slug = $base.'-'.$counter;
            $counter++;
        }

        return $slug;
    }

    public function umkms(): HasMany
    {
        return $this->hasMany(Umkm::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
