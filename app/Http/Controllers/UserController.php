<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display a listing of system users.
     */
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', User::class);

        $users = User::filter($request->only(['search', 'role', 'status']))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new system user.
     */
    public function create(): View
    {
        Gate::authorize('create', User::class);

        return view('users.create');
    }

    /**
     * Store a newly created system user in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create', User::class);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string', Rule::in(['super_admin', 'manager', 'collection_staff', 'center_staff'])],
            'status' => ['required', 'boolean'],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return redirect()->route('users.index')
            ->with('success', __('User account created successfully.'));
    }

    /**
     * Display the specified user account details.
     */
    public function show(User $user): View
    {
        Gate::authorize('view', $user);

        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user account.
     */
    public function edit(User $user): View
    {
        Gate::authorize('update', $user);

        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified system user in storage.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        Gate::authorize('update', $user);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string', Rule::in(['super_admin', 'manager', 'collection_staff', 'center_staff'])],
            'status' => ['required', 'boolean'],
        ]);

        // Last Super Admin Protection check
        if ($user->isSuperAdmin()) {
            $isDemoting = $validated['role'] !== 'super_admin';
            $isDeactivating = $validated['status'] == false;

            if ($isDemoting || $isDeactivating) {
                $activeSuperAdminCount = User::where('role', 'super_admin')->where('status', true)->count();
                if ($activeSuperAdminCount <= 1) {
                    return back()->withErrors([
                        'role' => __('Cannot demote or deactivate the last active Super Admin account in the system.'),
                    ])->withInput();
                }
            }
        }

        if (empty($validated['password'])) {
            unset($validated['password']);
        } else {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('users.index')
            ->with('success', __('User account updated successfully.'));
    }

    /**
     * Remove the specified system user from storage.
     */
    public function destroy(User $user): RedirectResponse
    {
        Gate::authorize('delete', $user);

        if ($user->id === auth()->id()) {
            return back()->with('error', __('You cannot delete your own logged-in account.'));
        }

        if ($user->isSuperAdmin()) {
            $superAdminCount = User::where('role', 'super_admin')->count();
            if ($superAdminCount <= 1) {
                return back()->with('error', __('Cannot delete the last remaining Super Admin account.'));
            }
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', __('User account deleted successfully.'));
    }

    /**
     * Toggle active/inactive status of a system user.
     */
    public function toggleStatus(User $user): RedirectResponse
    {
        Gate::authorize('update', $user);

        if ($user->id === auth()->id() && $user->status === true) {
            return back()->with('error', __('You cannot deactivate your own logged-in account.'));
        }

        if ($user->isSuperAdmin() && $user->status === true) {
            $activeSuperAdminCount = User::where('role', 'super_admin')->where('status', true)->count();
            if ($activeSuperAdminCount <= 1) {
                return back()->with('error', __('Cannot deactivate the last remaining active Super Admin account.'));
            }
        }

        $user->update(['status' => !$user->status]);

        $statusText = $user->status ? __('activated') : __('deactivated');

        return redirect()->route('users.index')
            ->with('success', __("User account {$statusText} successfully."));
    }
}
