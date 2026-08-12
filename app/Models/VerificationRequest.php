<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use LogicException;

#[Fillable([
    'user_id',
    'reviewer_id',
    'verifiable_type',
    'verifiable_id',
    'status',
    'notes',
    'reviewed_at',
])]
class VerificationRequest extends Model
{
    protected static function booted(): void
    {
        static::saving(function (VerificationRequest $request) {
            $verifiable = $request->verifiable()->first();

            if ($verifiable === null || ! ($verifiable instanceof Umkm) && ! ($verifiable instanceof Product)) {
                throw new LogicException('A verification request must target an UMKM or a Product.');
            }
        });
    }

    public function verifiable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }
}
