<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;
use LogicException;

#[Fillable([
    'umkm_id',
    'category_id',
    'name',
    'slug',
    'description',
    'price',
    'status',
    'is_featured',
])]
class Product extends Model
{
    protected $casts = [
        'is_featured' => 'boolean',
    ];
    public static function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'produk';
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

    protected static function booted(): void
    {
        static::saving(function (Product $product) {
            $category = $product->category()->first();

            if ($category === null || $category->type !== 'product') {
                throw new LogicException('A product category must have type "product".');
            }
        });
    }

    public function umkm(): BelongsTo
    {
        return $this->belongsTo(Umkm::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable');
    }

    public function verificationRequests(): MorphMany
    {
        return $this->morphMany(VerificationRequest::class, 'verifiable');
    }

    public function revisions(): HasMany
    {
        return $this->hasMany(ProductRevision::class);
    }
}
