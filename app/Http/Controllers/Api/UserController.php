<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreUserRequest;
use App\Http\Requests\Api\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of system users.
     *
     * GET /api/users
     */
    public function index(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', User::class);

        $users = User::filter($request->only(['search', 'role', 'status']))
            ->latest()
            ->paginate(15);

        return response()->json([
            'success' => true,
            'message' => 'Users retrieved successfully',
            'data' => UserResource::collection($users),
            'meta' => [
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
            ],
        ], 200);
    }

    /**
     * Display the specified user account details.
     *
     * GET /api/users/{user}
     */
    public function show(User $user): JsonResponse
    {
        Gate::authorize('view', $user);

        return response()->json([
            'success' => true,
            'message' => 'User retrieved successfully',
            'data' => new UserResource($user),
        ], 200);
    }

    /**
     * Store a newly created system user in storage.
     *
     * POST /api/users
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        Gate::authorize('create', User::class);

        $validated = $request->validated();
        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'User account created successfully',
            'data' => new UserResource($user),
        ], 201);
    }

    /**
     * Update the specified system user in storage.
     *
     * PUT/PATCH /api/users/{user}
     */
    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        Gate::authorize('update', $user);

        $validated = $request->validated();

        // Last Super Admin Protection check
        if ($user->isSuperAdmin()) {
            $isDemoting = $validated['role'] !== 'super_admin';
            $isDeactivating = $validated['status'] == false;

            if ($isDemoting || $isDeactivating) {
                $activeSuperAdminCount = User::where('role', 'super_admin')->where('status', true)->count();
                if ($activeSuperAdminCount <= 1) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cannot demote or deactivate the last active Super Admin account in the system.',
                        'errors' => [
                            'role' => ['Cannot demote or deactivate the last active Super Admin account in the system.'],
                        ],
                    ], 422);
                }
            }
        }

        if (empty($validated['password'])) {
            unset($validated['password']);
        } else {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'User account updated successfully',
            'data' => new UserResource($user),
        ], 200);
    }

    /**
     * Toggle active/inactive status of a system user.
     *
     * PATCH /api/users/{user}/toggle-status
     */
    public function toggleStatus(User $user): JsonResponse
    {
        Gate::authorize('update', $user);

        if ($user->id === auth()->id() && $user->status === true) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot deactivate your own logged-in account.',
                'errors' => new \stdClass(),
            ], 422);
        }

        if ($user->isSuperAdmin() && $user->status === true) {
            $activeSuperAdminCount = User::where('role', 'super_admin')->where('status', true)->count();
            if ($activeSuperAdminCount <= 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot deactivate the last remaining active Super Admin account.',
                    'errors' => new \stdClass(),
                ], 422);
            }
        }

        $user->update(['status' => ! $user->status]);

        $statusText = $user->status ? 'activated' : 'deactivated';

        return response()->json([
            'success' => true,
            'message' => "User account {$statusText} successfully",
            'data' => new UserResource($user),
        ], 200);
    }

    /**
     * Remove the specified system user from storage.
     *
     * DELETE /api/users/{user}
     */
    public function destroy(User $user): JsonResponse
    {
        Gate::authorize('delete', $user);

        if ($user->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot delete your own logged-in account.',
                'errors' => new \stdClass(),
            ], 422);
        }

        if ($user->isSuperAdmin()) {
            $superAdminCount = User::where('role', 'super_admin')->count();
            if ($superAdminCount <= 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete the last remaining Super Admin account.',
                    'errors' => new \stdClass(),
                ], 422);
            }
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User account deleted successfully',
        ], 200);
    }
}
