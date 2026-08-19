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
    'user_id',
    'category_id',
    'name',
    'slug',
    'description',
    'address',
    'google_maps',
    'phone',
    'email',
    'website',
    'instagram',
    'facebook',
    'tiktok',
    'operational_hours',
    'status',
    'is_featured',
])]
class Umkm extends Model
{
    protected $casts = [
        'is_featured' => 'boolean',
    ];
    public static function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'umkm';
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
        static::saving(function (Umkm $umkm) {
            $category = $umkm->category()->first();

            if ($category === null || $category->type !== 'umkm') {
                throw new LogicException('An UMKM category must have type "umkm".');
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
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
        return $this->hasMany(UmkmRevision::class);
    }
}
