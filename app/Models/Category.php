<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['type', 'name', 'slug', 'description'])]
class Category extends \Illuminate\Database\Eloquent\Model
{
    public function umkms(): HasMany
    {
        return $this->hasMany(Umkm::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
