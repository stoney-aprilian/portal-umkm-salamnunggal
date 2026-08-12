<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[Fillable([
    'disk',
    'path',
    'collection',
    'mediable_type',
    'mediable_id',
    'sort_order',
])]
class Media extends Model
{
    public function mediable(): MorphTo
    {
        return $this->morphTo();
    }
}
