<?php

namespace App\Policies;

use App\Models\User;

/**
 * User Management access control. Only administrators can manage owner
 * accounts; owners and guests are denied. The target user is expected to
 * hold the `owner` role (enforced by the controller via ensureOwner).
 */
class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('administrator');
    }

    public function view(User $user, User $target): bool
    {
        return $user->hasRole('administrator');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('administrator');
    }

    public function update(User $user, User $target): bool
    {
        return $user->hasRole('administrator');
    }

    public function suspend(User $user, User $target): bool
    {
        return $user->hasRole('administrator');
    }

    public function activate(User $user, User $target): bool
    {
        return $user->hasRole('administrator');
    }

    public function resetPassword(User $user, User $target): bool
    {
        return $user->hasRole('administrator');
    }

    /**
     * Lets an owner manage the verification of their own account only
     * (fix account data and resubmit). Owners can never review another
     * account, and administrators never go through this flow.
     */
    public function manageAccountVerification(User $user, User $target): bool
    {
        return $user->id === $target->id && $user->hasRole('owner');
    }
}