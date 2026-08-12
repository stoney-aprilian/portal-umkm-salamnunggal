<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\Attributes\ErrorBag;

/**
 * Revision note validation scoped to its own error bag so validation
 * feedback never leaks into the "Tolak" form (and vice versa).
 */
#[ErrorBag('revision')]
class RevisionVerificationRequest extends ReviewVerificationRequest {}
