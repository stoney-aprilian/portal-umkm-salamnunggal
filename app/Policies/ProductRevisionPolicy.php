<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\ProductRevision;
use App\Models\User;

class ProductRevisionPolicy
{
    public function create(User $user, Product $product): bool
    {
        return $user->hasRole('owner')
            && $product->umkm->user_id === $user->id;
    }

    public function view(User $user, ProductRevision $revision): bool
    {
        return $user->hasRole('owner')
            && $revision->product->umkm->user_id === $user->id;
    }

    public function update(User $user, ProductRevision $revision): bool
    {
        return $user->hasRole('owner')
            && $revision->product->umkm->user_id === $user->id;
    }

    public function submit(User $user, ProductRevision $revision): bool
    {
        return $user->hasRole('owner')
            && $revision->product->umkm->user_id === $user->id;
    }
}