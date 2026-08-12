<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\Umkm;
use App\Models\User;

class ProductPolicy
{
    public function create(User $user, Umkm $umkm): bool
    {
        return $user->hasRole('owner')
            && $umkm->user_id === $user->id;
    }

    public function view(User $user, Product $product): bool
    {
        return $product->umkm->user_id === $user->id;
    }

    public function update(User $user, Product $product): bool
    {
        return $user->hasRole('owner')
            && $product->umkm->user_id === $user->id;
    }

    public function submit(User $user, Product $product): bool
    {
        return $user->hasRole('owner')
            && $product->umkm->user_id === $user->id;
    }
}
