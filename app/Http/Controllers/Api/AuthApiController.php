<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthApiController extends ApiController
{
    /**
     * Login user and create token.
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->errorResponse('Invalid credentials', 401);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return $this->successResponse([
            'user' => new UserResource($user),
            'token' => $token,
            'token_type' => 'Bearer',
        ], 'Login successful');
    }

    /**
     * Logout user (revoke token).
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return $this->successResponse(null, 'Logout successful');
    }

    /**
     * Get authenticated user.
     */
    public function user(Request $request): JsonResponse
    {
        return $this->successResponse(new UserResource($request->user()), 'User retrieved successfully');
    }

    /**
     * Refresh token.
     */
    public function refresh(Request $request): JsonResponse
    {
        $user = $request->user();
        
        // Delete current token
        $request->user()->currentAccessToken()->delete();
        
        // Create new token
        $token = $user->createToken('api-token')->plainTextToken;

        return $this->successResponse([
            'user' => new UserResource($user),
            'token' => $token,
            'token_type' => 'Bearer',
        ], 'Token refreshed successfully');
    }

    /**
     * Update user profile.
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', 'max:255', 'unique:users,email,' . $request->user()->id],
            'locale' => ['sometimes', 'string', 'in:en,bg'],
        ]);

        $user = $request->user();
        $user->update($validated);

        return $this->successResponse(new UserResource($user), 'Profile updated successfully');
    }

    /**
     * Change password.
     */
    public function changePassword(Request $request): JsonResponse
    {
        $request->validate([
            'current_password' => ['required'],
            'password' => ['required', 'min:8', 'confirmed'],
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return $this->errorResponse('Current password is incorrect', 400);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        // Revoke all tokens except current one
        $currentTokenId = $request->user()->currentAccessToken()->id;
        $user->tokens()->where('id', '!=', $currentTokenId)->delete();

        return $this->successResponse(null, 'Password changed successfully');
    }

    /**
     * Get user statistics.
     */
    public function statistics(Request $request): JsonResponse
    {
        $user = $request->user();

        $stats = [
            'bank_accounts_count' => $user->bankAccounts()->count(),
            'transactions_count' => $user->transactions()->count(),
            'transfers_count' => $user->transfers()->count(),
            'counterparties_count' => $user->counterparties()->count(),
            'transaction_types_count' => $user->transactionTypes()->count(),
            'total_balance_by_currency' => $user->bankAccounts()
                ->selectRaw('currency, SUM(balance) as total')
                ->groupBy('currency')
                ->pluck('total', 'currency'),
            'recent_transactions_count' => $user->transactions()
                ->where('executed_at', '>=', now()->subDays(30))
                ->count(),
        ];

        return $this->successResponse($stats, 'User statistics retrieved successfully');
    }
}
