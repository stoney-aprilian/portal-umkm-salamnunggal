<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\User;

/**
 * Category Management access control. Only administrators can manage
 * categories; owners and guests are denied. Categories are shared master
 * data used by both UMKM and Product, so type integrity is handled by
 * the Umkm/Product model guards and this policy.
 */
class CategoryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('administrator');
    }

    public function view(User $user, Category $category): bool
    {
        return $user->hasRole('administrator');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('administrator');
    }

    public function update(User $user, Category $category): bool
    {
        return $user->hasRole('administrator');
    }

    public function delete(User $user, Category $category): bool
    {
        return $user->hasRole('administrator');
    }
}