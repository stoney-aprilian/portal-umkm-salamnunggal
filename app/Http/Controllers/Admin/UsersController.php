<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ResetPasswordRequest;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use App\Support\UserManagementActivity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Spatie\Activitylog\Models\Activity;

/**
 * Administrator User Management for owner accounts. Administrators act
 * only as operators: the owner stays the owner of their UMKM and the
 * Self-Service flow is untouched. Only users with the `owner` role are
 * manageable; administrators can never be created or altered here.
 */
class UsersController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', User::class);

        $search = $request->string('search')->trim()->toString();
        $status = $request->string('status')->trim()->toString();
        $sort = $request->string('sort')->trim()->toString();

        $baseQuery = User::query()->role('owner')->with('umkm');

        $totalCount = (clone $baseQuery)->count();
        $approvedCount = (clone $baseQuery)->where('status', 'approved')->count();
        $suspendedCount = (clone $baseQuery)->where('status', 'suspended')->count();
        $pendingCount = (clone $baseQuery)->whereIn('status', ['pending', 'needs_revision', 'rejected'])->count();

        $query = $baseQuery;

        if ($search !== '') {
            $term = mb_strtolower($search);
            $query->where(function ($q) use ($term): void {
                $q->whereRaw('LOWER(name) LIKE ?', ['%'.$term.'%'])
                    ->orWhereRaw('LOWER(email) LIKE ?', ['%'.$term.'%']);
            });
        }

        if ($status !== '' && in_array($status, ['pending', 'approved', 'needs_revision', 'rejected', 'suspended'], true)) {
            $query->where('status', $status);
        }

        $sort = match ($sort) {
            'oldest' => 'asc',
            'name_asc' => 'name_asc',
            'name_desc' => 'name_desc',
            default => 'desc',
        };

        if ($sort === 'name_asc') {
            $query->orderBy('name', 'asc');
        } elseif ($sort === 'name_desc') {
            $query->orderBy('name', 'desc');
        } else {
            $query->orderBy('created_at', $sort);
        }

        $users = $query->get();

        return view('admin.users.index', [
            'users' => $users,
            'totalCount' => $totalCount,
            'approvedCount' => $approvedCount,
            'suspendedCount' => $suspendedCount,
            'pendingCount' => $pendingCount,
            'search' => $search,
            'status' => $status,
            'sort' => $sort,
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', User::class);

        return view('admin.users.create');
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $this->authorize('create', User::class);

        $user = DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->string('name')->toString(),
                'email' => $request->filled('email') ? $request->string('email')->toString() : null,
                'phone' => $request->filled('phone') ? $request->string('phone')->toString() : null,
                'password' => $request->string('password')->toString(),
                'status' => 'approved',
            ]);

            $user->assignRole('owner');

            UserManagementActivity::log('user_created', $user, $request->user());

            return $user;
        });

        return redirect()->route('admin.users.show', $user)
            ->with('status', "Akun owner {$user->name} berhasil dibuat.");
    }

    public function show(Request $request, User $user): View
    {
        $this->authorize('view', $user);
        $this->ensureOwner($user);

        $user->load('umkm');

        $activities = Activity::query()
            ->where('subject_type', User::class)
            ->where('subject_id', $user->id)
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.users.show', [
            'user' => $user,
            'activities' => $activities,
        ]);
    }

    public function edit(Request $request, User $user): View
    {
        $this->authorize('update', $user);
        $this->ensureOwner($user);

        return view('admin.users.edit', [
            'user' => $user,
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $this->authorize('update', $user);
        $this->ensureOwner($user);

        DB::transaction(function () use ($request, $user) {
            $user->update([
                'name' => $request->string('name')->toString(),
                'email' => $request->filled('email') ? $request->string('email')->toString() : null,
                'phone' => $request->filled('phone') ? $request->string('phone')->toString() : null,
            ]);

            UserManagementActivity::log('user_updated', $user, $request->user());
        });

        return redirect()->route('admin.users.show', $user)
            ->with('status', "Data akun owner {$user->name} berhasil diperbarui.");
    }

    public function suspend(Request $request, User $user): RedirectResponse
    {
        $this->authorize('suspend', $user);
        $this->ensureOwner($user);

        if ($user->id === $request->user()->id) {
            return redirect()->back()
                ->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri.');
        }

        $suspended = DB::transaction(function () use ($request, $user) {
            $affected = $user->whereKey($user->id)
                ->where('status', '!=', 'suspended')
                ->update(['status' => 'suspended']);

            if ($affected === 0) {
                return false;
            }

            UserManagementActivity::log('user_suspended', $user, $request->user());

            return true;
        });

        if (! $suspended) {
            return redirect()->back()
                ->with('error', 'Akun owner ini sudah dinonaktifkan.');
        }

        return redirect()->back()
            ->with('status', "Akun owner {$user->name} berhasil dinonaktifkan.");
    }

    public function activate(Request $request, User $user): RedirectResponse
    {
        $this->authorize('activate', $user);
        $this->ensureOwner($user);

        $activated = DB::transaction(function () use ($request, $user) {
            $affected = $user->whereKey($user->id)
                ->where('status', '!=', 'approved')
                ->update(['status' => 'approved']);

            if ($affected === 0) {
                return false;
            }

            UserManagementActivity::log('user_activated', $user, $request->user());

            return true;
        });

        if (! $activated) {
            return redirect()->back()
                ->with('error', 'Akun owner ini sudah aktif.');
        }

        return redirect()->back()
            ->with('status', "Akun owner {$user->name} berhasil diaktifkan kembali.");
    }

    public function resetPassword(ResetPasswordRequest $request, User $user): RedirectResponse
    {
        $this->authorize('resetPassword', $user);
        $this->ensureOwner($user);

        DB::transaction(function () use ($request, $user) {
            $user->update([
                'password' => $request->string('password')->toString(),
            ]);

            UserManagementActivity::log('user_password_reset', $user, $request->user());
        });

        return redirect()->back()
            ->with('status', "Kata sandi akun owner {$user->name} berhasil direset.");
    }

    private function ensureOwner(User $user): void
    {
        if (! $user->hasRole('owner')) {
            abort(404);
        }
    }
}