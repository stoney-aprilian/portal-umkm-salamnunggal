<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\Umkm;
use App\Models\User;

class ProductPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('administrator');
    }

    public function create(User $user, ?Umkm $umkm = null): bool
    {
        if ($user->hasRole('administrator')) {
            return true;
        }

        return $user->hasRole('owner')
            && $umkm !== null
            && $umkm->user_id === $user->id;
    }

    public function view(User $user, Product $product): bool
    {
        if ($user->hasRole('administrator')) {
            return true;
        }

        return $product->umkm->user_id === $user->id;
    }

    public function update(User $user, Product $product): bool
    {
        if ($user->hasRole('administrator')) {
            return true;
        }

        return $user->hasRole('owner')
            && $product->umkm->user_id === $user->id;
    }

    public function delete(User $user, Product $product): bool
    {
        if ($user->hasRole('administrator')) {
            return true;
        }

        return $user->hasRole('owner')
            && $product->umkm->user_id === $user->id;
    }

    public function submit(User $user, Product $product): bool
    {
        return $user->hasRole('owner')
            && $product->umkm->user_id === $user->id;
    }
}