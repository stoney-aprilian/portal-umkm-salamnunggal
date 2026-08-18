<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\AccountVerificationUpdateRequest;
use App\Support\VerificationActivity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Owner-facing account verification flow for self-registered owners.
 *
 * The owner can only ever act on their own account (manageAccountVerification
 * policy): review the current state, fix their account data while in
 * `needs_revision`, and resubmit for verification. Approving, requesting
 * revision, or rejecting an account is an Administrator responsibility
 * (Admin\OwnerVerificationController).
 */
class AccountVerificationController extends Controller
{
    public function notice(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if ($user->status === 'approved') {
            return redirect()->route('dashboard');
        }

        return view('auth.account-verification-notice', [
            'user' => $user,
            'latest' => $user->verificationRequests()->latest('id')->first(),
        ]);
    }

    public function edit(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        $this->authorize('manageAccountVerification', $user);

        if ($user->status !== 'needs_revision') {
            return redirect()->route('account.verification.notice');
        }

        return view('auth.account-verification-edit', [
            'user' => $user,
        ]);
    }

    public function update(AccountVerificationUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        $this->authorize('manageAccountVerification', $user);

        if ($user->status !== 'needs_revision') {
            return redirect()->route('account.verification.notice');
        }

        $user->update([
            'name' => $request->string('name')->toString(),
            'email' => $request->filled('email') ? $request->string('email')->toString() : null,
            'phone' => $request->filled('phone') ? $request->string('phone')->toString() : null,
        ]);

        return redirect()->route('account.verification.notice')
            ->with('status', 'Data akun Anda berhasil diperbarui.');
    }

    public function submit(Request $request): RedirectResponse
    {
        $user = $request->user();

        $this->authorize('manageAccountVerification', $user);

        if ($user->status !== 'needs_revision') {
            return redirect()->route('account.verification.notice')
                ->with('error', 'Akun Anda tidak dalam status yang membutuhkan pengajuan ulang.');
        }

        DB::transaction(function () use ($user) {
            $user->update(['status' => 'pending']);

            $user->verificationRequests()->create([
                'user_id' => $user->id,
                'status' => 'pending',
            ]);

            VerificationActivity::log('submitted', $user, $user);
        });

        return redirect()->route('account.verification.notice')
            ->with('status', 'Akun Anda diajukan kembali untuk verifikasi Administrator.');
    }
}