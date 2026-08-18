<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use LogicException;

/**
 * Working copy of a change an owner wants to apply to an approved
 * product. The record is never queried by the public; the approved
 * product keeps showing its current data until an administrator approves
 * the revision.
 */
#[Fillable([
    'product_id',
    'category_id',
    'name',
    'description',
    'price',
    'status',
])]
class ProductRevision extends Model
{
    protected static function booted(): void
    {
        static::saving(function (ProductRevision $revision) {
            $category = $revision->category()->first();

            if ($category === null || $category->type !== 'product') {
                throw new LogicException('A product revision category must have type "product".');
            }
        });
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
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