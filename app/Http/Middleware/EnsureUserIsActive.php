<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Enforces the owner account verification lifecycle.
 *
 * - `suspended` accounts are logged out and redirected to login, exactly
 *   as in Phase 1.
 * - Accounts that are not yet `approved` (pending, needs_revision,
 *   rejected) are steered away from the owner dashboard, the owner
 *   Self-Service area, and the profile pages toward the account
 *   verification notice page, which explains their current state and
 *   the next step. Public portal pages stay reachable.
 */
class EnsureUserIsActive
{
    private const NOT_APPROVED = ['pending', 'needs_revision', 'rejected'];

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user === null) {
            return $next($request);
        }

        if ($user->status === 'suspended') {
            Auth::guard('web')->logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'email' => 'Akun Anda sedang dinonaktifkan. Silakan hubungi Administrator.',
            ]);
        }

        if (in_array($user->status, self::NOT_APPROVED, true)
            && $request->routeIs('dashboard', 'owner.*', 'profile.*')) {
            return redirect()->route('account.verification.notice');
        }

        return $next($request);
    }
}