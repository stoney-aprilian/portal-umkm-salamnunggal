<?php

namespace App\Support;

use App\Models\User;
use InvalidArgumentException;

/**
 * Logs administrator user-management actions (create, update, suspend,
 * activate, reset password) on the owner subject using the documented
 * Spatie activity log table. Passwords are never written to the log,
 * only a human-readable description of the event.
 */
class UserManagementActivity
{
    public static function log(string $event, User $subject, User $causer): void
    {
        activity()
            ->causedBy($causer)
            ->performedOn($subject)
            ->event($event)
            ->log(self::descriptionFor($event, $subject));
    }

    private static function descriptionFor(string $event, User $subject): string
    {
        return match ($event) {
            'user_created' => "Akun owner {$subject->name} dibuat",
            'user_updated' => "Data akun owner {$subject->name} diperbarui",
            'user_suspended' => "Akun owner {$subject->name} dinonaktifkan",
            'user_activated' => "Akun owner {$subject->name} diaktifkan kembali",
            'user_password_reset' => "Kata sandi akun owner {$subject->name} direset",
            default => throw new InvalidArgumentException("Unsupported user management activity event [{$event}]."),
        };
    }
}