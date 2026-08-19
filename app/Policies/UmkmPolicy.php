<?php

namespace App\Policies;

use App\Models\Umkm;
use App\Models\User;

class UmkmPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('administrator');
    }

    public function create(User $user): bool
    {
        if ($user->hasRole('administrator')) {
            return true;
        }

        return $user->hasRole('owner')
            && ! $user->umkm()->exists();
    }

    public function view(User $user, Umkm $umkm): bool
    {
        if ($user->hasRole('administrator')) {
            return true;
        }

        return $umkm->user_id === $user->id;
    }

    public function submit(User $user, Umkm $umkm): bool
    {
        return $user->hasRole('owner')
            && $umkm->user_id === $user->id;
    }

    public function update(User $user, Umkm $umkm): bool
    {
        if ($user->hasRole('administrator')) {
            return true;
        }

        return $user->hasRole('owner')
            && $umkm->user_id === $user->id;
    }

    public function delete(User $user, Umkm $umkm): bool
    {
        return $user->hasRole('administrator');
    }

    public function feature(User $user, Umkm $umkm): bool
    {
        return $user->hasRole('administrator');
    }

    public function unfeature(User $user, Umkm $umkm): bool
    {
        return $user->hasRole('administrator');
    }
}