<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Authenticate user and generate Sanctum access token.
     *
     * POST /api/login
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->validated();

        $user = User::where('email', $credentials['email'])->first();

        // Check if user exists and password is correct
        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials',
                'errors' => new \stdClass(),
            ], 401);
        }

        // Check if account is active
        if (! $user->isActive()) {
            return response()->json([
                'success' => false,
                'message' => 'Your account is inactive. Please contact system administrator.',
                'errors' => new \stdClass(),
            ], 403);
        }

        // Generate Sanctum access token for mobile/API access
        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => new UserResource($user),
                'token' => $token,
                'token_type' => 'Bearer',
            ],
        ], 200);
    }

    /**
     * Get authenticated user profile and roles.
     *
     * GET /api/me
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'message' => 'User profile retrieved successfully',
            'data' => [
                'user' => new UserResource($user),
            ],
        ], 200);
    }

    /**
     * Revoke current access token and logout user.
     *
     * POST /api/logout
     */
    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();

        // Revoke the token that was used to authenticate the current request
        if ($user && $user->currentAccessToken()) {
            $user->currentAccessToken()->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Successfully logged out',
        ], 200);
    }
}
