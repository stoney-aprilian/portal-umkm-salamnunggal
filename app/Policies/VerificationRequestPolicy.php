<?php

namespace App\Policies;

use App\Models\User;
use App\Models\VerificationRequest;

class VerificationRequestPolicy
{
    public function review(User $user, VerificationRequest $verificationRequest): bool
    {
        return $user->hasRole('administrator');
    }
}
