<?php

namespace App\Http\Controllers\Api;

use App\Models\BankAccount;
use App\Http\Resources\BankAccountResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class BankAccountApiController extends ApiController
{
    /**
     * Display a listing of the user's bank accounts.
     */
    public function index(): JsonResponse
    {
        $accounts = Auth::user()->bankAccounts()
            ->orderBy('is_default', 'desc')
            ->orderBy('name')
            ->get();

        return $this->successResponse(BankAccountResource::collection($accounts), 'Bank accounts retrieved successfully');
    }

    /**
     * Store a newly created bank account.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'currency' => ['required', 'string', Rule::in(config('strix.business.supported_currencies', ['EUR', 'USD', 'BGN']))],
            'balance' => ['required', 'numeric', 'min:0', 'max:' . config('strix.business.max_amount', 999999999.99)],
            'is_active' => ['boolean'],
            'is_default' => ['boolean'],
        ]);

        $validated['user_id'] = Auth::id();

        // If this is set as default, unset other defaults
        if ($validated['is_default'] ?? false) {
            Auth::user()->bankAccounts()->update(['is_default' => false]);
        }

        $account = BankAccount::create($validated);

        return $this->successResponse(new BankAccountResource($account), 'Bank account created successfully', 201);
    }

    /**
     * Display the specified bank account.
     */
    public function show(BankAccount $bankAccount): JsonResponse
    {
        // Check if the account belongs to the authenticated user
        if ($bankAccount->user_id !== Auth::id()) {
            return $this->forbiddenResponse('You do not have permission to view this bank account');
        }

        return $this->successResponse(new BankAccountResource($bankAccount), 'Bank account retrieved successfully');
    }

    /**
     * Update the specified bank account.
     */
    public function update(Request $request, BankAccount $bankAccount): JsonResponse
    {
        // Check if the account belongs to the authenticated user
        if ($bankAccount->user_id !== Auth::id()) {
            return $this->forbiddenResponse('You do not have permission to update this bank account');
        }

        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'currency' => ['sometimes', 'string', Rule::in(config('strix.business.supported_currencies', ['EUR', 'USD', 'BGN']))],
            'balance' => ['sometimes', 'numeric', 'min:0', 'max:' . config('strix.business.max_amount', 999999999.99)],
            'is_active' => ['sometimes', 'boolean'],
            'is_default' => ['sometimes', 'boolean'],
        ]);

        // If this is set as default, unset other defaults
        if (($validated['is_default'] ?? false) && !$bankAccount->is_default) {
            Auth::user()->bankAccounts()->where('id', '!=', $bankAccount->id)->update(['is_default' => false]);
        }

        $bankAccount->update($validated);

        return $this->successResponse(new BankAccountResource($bankAccount), 'Bank account updated successfully');
    }

    /**
     * Remove the specified bank account.
     */
    public function destroy(BankAccount $bankAccount): JsonResponse
    {
        // Check if the account belongs to the authenticated user
        if ($bankAccount->user_id !== Auth::id()) {
            return $this->forbiddenResponse('You do not have permission to delete this bank account');
        }

        // Check if account has transactions
        if ($bankAccount->transactions()->exists()) {
            return $this->errorResponse('Cannot delete bank account with existing transactions', 409);
        }

        $bankAccount->delete();

        return $this->successResponse(null, 'Bank account deleted successfully');
    }

    /**
     * Get account statistics.
     */
    public function statistics(BankAccount $bankAccount): JsonResponse
    {
        // Check if the account belongs to the authenticated user
        if ($bankAccount->user_id !== Auth::id()) {
            return $this->forbiddenResponse('You do not have permission to view this bank account');
        }

        $stats = [
            'balance' => $bankAccount->balance,
            'currency' => $bankAccount->currency,
            'transactions_count' => $bankAccount->transactions_count,
            'total_income' => $bankAccount->total_income,
            'total_expenses' => $bankAccount->total_expenses,
        ];

        return $this->successResponse($stats, 'Bank account statistics retrieved successfully');
    }
}
