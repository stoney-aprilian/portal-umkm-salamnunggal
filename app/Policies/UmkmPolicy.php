<?php

namespace App\Policies;

use App\Models\Umkm;
use App\Models\User;

class UmkmPolicy
{
    public function create(User $user): bool
    {
        return $user->hasRole('owner')
            && ! $user->umkm()->exists();
    }

    public function view(User $user, Umkm $umkm): bool
    {
        return $umkm->user_id === $user->id;
    }

    public function submit(User $user, Umkm $umkm): bool
    {
        return $user->hasRole('owner')
            && $umkm->user_id === $user->id;
    }

    public function update(User $user, Umkm $umkm): bool
    {
        return $user->hasRole('owner')
            && $umkm->user_id === $user->id;
    }
}
