<?php

namespace App\Policies;

use App\Models\Media;
use App\Models\Product;
use App\Models\ProductRevision;
use App\Models\Umkm;
use App\Models\UmkmRevision;
use App\Models\User;

class MediaPolicy
{
    public function delete(User $user, Media $media): bool
    {
        if ($user->hasRole('administrator')) {
            return $media->mediable instanceof Umkm
                || $media->mediable instanceof Product
                || $media->mediable instanceof UmkmRevision
                || $media->mediable instanceof ProductRevision;
        }

        if (! $user->hasRole('owner')) {
            return false;
        }

        return match (true) {
            $media->mediable instanceof Umkm => $media->mediable->user_id === $user->id,
            $media->mediable instanceof Product => $media->mediable->umkm->user_id === $user->id,
            $media->mediable instanceof UmkmRevision => $media->mediable->umkm->user_id === $user->id,
            $media->mediable instanceof ProductRevision => $media->mediable->product->umkm->user_id === $user->id,
            default => false,
        };
    }
}