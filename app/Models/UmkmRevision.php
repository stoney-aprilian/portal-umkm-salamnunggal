<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use LogicException;

/**
 * Working copy of a change an owner wants to apply to an approved UMKM.
 * The record is never queried by the public; the approved UMKM keeps
 * showing its current data until an administrator approves the revision.
 */
#[Fillable([
    'umkm_id',
    'category_id',
    'name',
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
])]
class UmkmRevision extends Model
{
    protected static function booted(): void
    {
        static::saving(function (UmkmRevision $revision) {
            $category = $revision->category()->first();

            if ($category === null || $category->type !== 'umkm') {
                throw new LogicException('An UMKM revision category must have type "umkm".');
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
}