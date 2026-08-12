<?php

namespace App\Support;

/**
 * Builds a wa.me-compatible phone number from a stored phone value
 * without mutating the value itself.
 *
 * Supported forms: 08… (local), 62… (already international), +62…
 * (international with plus). Everything else is kept as-is.
 */
class WhatsApp
{
    public static function waNumber(?string $phone): string
    {
        $digits = (string) preg_replace('/\D/', '', (string) $phone);

        if (str_starts_with($digits, '08')) {
            return '62'.substr($digits, 1);
        }

        return $digits;
    }
}
