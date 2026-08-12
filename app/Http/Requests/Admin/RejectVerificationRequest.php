<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\Attributes\ErrorBag;

/**
 * Rejection note validation scoped to its own error bag so validation
 * feedback never leaks into the "Perlu Revisi" form (and vice versa).
 */
#[ErrorBag('reject')]
class RejectVerificationRequest extends ReviewVerificationRequest {}
