<?php

namespace App\Http\Controllers\Api;

use App\Models\Transfer;
use App\Models\BankAccount;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class TransferApiController extends ApiController
{
    /**
     * Display a listing of the user's transfers.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Auth::user()->transfers()
            ->with(['fromAccount', 'toAccount'])
            ->orderBy('executed_at', 'desc');

        // Apply filters
        if ($request->has('from_account_id')) {
            $query->where('from_account_id', $request->from_account_id);
        }

        if ($request->has('to_account_id')) {
            $query->where('to_account_id', $request->to_account_id);
        }

        if ($request->has('from_date')) {
            $query->whereDate('executed_at', '>=', $request->from_date);
        }

        if ($request->has('to_date')) {
            $query->whereDate('executed_at', '<=', $request->to_date);
        }

        $perPage = min($request->get('per_page', 15), 100); // Max 100 per page
        $transfers = $query->paginate($perPage);

        return $this->successResponse($transfers, 'Transfers retrieved successfully');
    }

    /**
     * Store a newly created transfer.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'from_account_id' => ['required', 'exists:bank_accounts,id'],
            'to_account_id' => ['required', 'exists:bank_accounts,id', 'different:from_account_id'],
            'amount_from' => ['required', 'numeric', 'min:0.01', 'max:' . config('strix.business.max_amount', 999999999.99)],
            'currency_from' => ['required', 'string', Rule::in(config('strix.business.supported_currencies', ['EUR', 'USD', 'BGN']))],
            'amount_to' => ['required', 'numeric', 'min:0.01', 'max:' . config('strix.business.max_amount', 999999999.99)],
            'currency_to' => ['required', 'string', Rule::in(config('strix.business.supported_currencies', ['EUR', 'USD', 'BGN']))],
            'exchange_rate' => ['required', 'numeric', 'min:0.0001'],
            'description' => ['nullable', 'string', 'max:1000'],
            'executed_at' => ['required', 'date'],
        ]);

        // Check if both accounts belong to user
        $fromAccount = BankAccount::where('id', $validated['from_account_id'])
            ->where('user_id', Auth::id())
            ->first();

        $toAccount = BankAccount::where('id', $validated['to_account_id'])
            ->where('user_id', Auth::id())
            ->first();

        if (!$fromAccount || !$toAccount) {
            return $this->forbiddenResponse('You do not have permission to use one or both of these bank accounts');
        }

        // Check if source account has sufficient funds
        if (!$fromAccount->hasSufficientFunds($validated['amount_from'])) {
            return $this->errorResponse('Insufficient funds in source account', 400);
        }

        // Validate exchange rate calculation
        $calculatedAmountTo = $validated['amount_from'] * $validated['exchange_rate'];
        if (abs($calculatedAmountTo - $validated['amount_to']) > 0.01) {
            return $this->errorResponse('Exchange rate calculation does not match the provided amounts', 400);
        }

        $validated['user_id'] = Auth::id();

        DB::beginTransaction();
        try {
            $transfer = Transfer::create($validated);
            
            // Update account balances
            $fromAccount->withdraw($validated['amount_from']);
            $toAccount->deposit($validated['amount_to']);
            
            DB::commit();

            $transfer->load(['fromAccount', 'toAccount']);

            return $this->successResponse($transfer, 'Transfer created successfully', 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to create transfer: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Display the specified transfer.
     */
    public function show(Transfer $transfer): JsonResponse
    {
        // Check if the transfer belongs to the authenticated user
        if ($transfer->user_id !== Auth::id()) {
            return $this->forbiddenResponse('You do not have permission to view this transfer');
        }

        $transfer->load(['fromAccount', 'toAccount']);

        return $this->successResponse($transfer, 'Transfer retrieved successfully');
    }

    /**
     * Update the specified transfer.
     */
    public function update(Request $request, Transfer $transfer): JsonResponse
    {
        // Check if the transfer belongs to the authenticated user
        if ($transfer->user_id !== Auth::id()) {
            return $this->forbiddenResponse('You do not have permission to update this transfer');
        }

        $validated = $request->validate([
            'from_account_id' => ['sometimes', 'exists:bank_accounts,id'],
            'to_account_id' => ['sometimes', 'exists:bank_accounts,id', 'different:from_account_id'],
            'amount_from' => ['sometimes', 'numeric', 'min:0.01', 'max:' . config('strix.business.max_amount', 999999999.99)],
            'currency_from' => ['sometimes', 'string', Rule::in(config('strix.business.supported_currencies', ['EUR', 'USD', 'BGN']))],
            'amount_to' => ['sometimes', 'numeric', 'min:0.01', 'max:' . config('strix.business.max_amount', 999999999.99)],
            'currency_to' => ['sometimes', 'string', Rule::in(config('strix.business.supported_currencies', ['EUR', 'USD', 'BGN']))],
            'exchange_rate' => ['sometimes', 'numeric', 'min:0.0001'],
            'description' => ['nullable', 'string', 'max:1000'],
            'executed_at' => ['sometimes', 'date'],
        ]);

        DB::beginTransaction();
        try {
            // Reverse old transfer
            $transfer->fromAccount->deposit($transfer->amount_from);
            $transfer->toAccount->withdraw($transfer->amount_to);

            $transfer->update($validated);
            
            // Apply new transfer
            $transfer->fromAccount->withdraw($transfer->amount_from);
            $transfer->toAccount->deposit($transfer->amount_to);
            
            DB::commit();

            $transfer->load(['fromAccount', 'toAccount']);

            return $this->successResponse($transfer, 'Transfer updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to update transfer: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified transfer.
     */
    public function destroy(Transfer $transfer): JsonResponse
    {
        // Check if the transfer belongs to the authenticated user
        if ($transfer->user_id !== Auth::id()) {
            return $this->forbiddenResponse('You do not have permission to delete this transfer');
        }

        DB::beginTransaction();
        try {
            // Reverse transfer
            $transfer->fromAccount->deposit($transfer->amount_from);
            $transfer->toAccount->withdraw($transfer->amount_to);

            $transfer->delete();
            
            DB::commit();

            return $this->successResponse(null, 'Transfer deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to delete transfer: ' . $e->getMessage(), 500);
        }
    }
}
