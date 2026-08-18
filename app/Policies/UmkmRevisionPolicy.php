<?php

namespace App\Policies;

use App\Models\Umkm;
use App\Models\UmkmRevision;
use App\Models\User;

class UmkmRevisionPolicy
{
    public function create(User $user, Umkm $umkm): bool
    {
        return $user->hasRole('owner')
            && $umkm->user_id === $user->id;
    }

    public function view(User $user, UmkmRevision $revision): bool
    {
        return $user->hasRole('owner')
            && $revision->umkm->user_id === $user->id;
    }

    public function update(User $user, UmkmRevision $revision): bool
    {
        return $user->hasRole('owner')
            && $revision->umkm->user_id === $user->id;
    }

    public function submit(User $user, UmkmRevision $revision): bool
    {
        return $user->hasRole('owner')
            && $revision->umkm->user_id === $user->id;
    }
}