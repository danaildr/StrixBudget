<?php

namespace App\Http\Controllers\Api;

use App\Models\Transaction;
use App\Models\BankAccount;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class TransactionApiController extends ApiController
{
    /**
     * Display a listing of the user's transactions.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Auth::user()->transactions()
            ->with(['bankAccount', 'counterparty', 'transactionType'])
            ->orderBy('executed_at', 'desc');

        // Apply filters
        if ($request->has('bank_account_id')) {
            $query->where('bank_account_id', $request->bank_account_id);
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('counterparty_id')) {
            $query->where('counterparty_id', $request->counterparty_id);
        }

        if ($request->has('transaction_type_id')) {
            $query->where('transaction_type_id', $request->transaction_type_id);
        }

        if ($request->has('from_date')) {
            $query->whereDate('executed_at', '>=', $request->from_date);
        }

        if ($request->has('to_date')) {
            $query->whereDate('executed_at', '<=', $request->to_date);
        }

        $perPage = min($request->get('per_page', 15), 100); // Max 100 per page
        $transactions = $query->paginate($perPage);

        return $this->successResponse($transactions, 'Transactions retrieved successfully');
    }

    /**
     * Store a newly created transaction.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'bank_account_id' => ['required', 'exists:bank_accounts,id'],
            'counterparty_id' => ['nullable', 'exists:counterparties,id'],
            'transaction_type_id' => ['nullable', 'exists:transaction_types,id'],
            'type' => ['required', Rule::in(['income', 'expense'])],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:' . config('strix.business.max_amount', 999999999.99)],
            'currency' => ['required', 'string', Rule::in(config('strix.business.supported_currencies', ['EUR', 'USD', 'BGN']))],
            'description' => ['nullable', 'string', 'max:1000'],
            'executed_at' => ['required', 'date'],
        ]);

        // Check if bank account belongs to user
        $bankAccount = BankAccount::where('id', $validated['bank_account_id'])
            ->where('user_id', Auth::id())
            ->first();

        if (!$bankAccount) {
            return $this->forbiddenResponse('You do not have permission to use this bank account');
        }

        // Check if counterparty belongs to user (if provided)
        if ($validated['counterparty_id']) {
            $counterpartyExists = Auth::user()->counterparties()
                ->where('id', $validated['counterparty_id'])
                ->exists();

            if (!$counterpartyExists) {
                return $this->forbiddenResponse('You do not have permission to use this counterparty');
            }
        }

        // Check if transaction type belongs to user (if provided)
        if ($validated['transaction_type_id']) {
            $transactionTypeExists = Auth::user()->transactionTypes()
                ->where('id', $validated['transaction_type_id'])
                ->exists();

            if (!$transactionTypeExists) {
                return $this->forbiddenResponse('You do not have permission to use this transaction type');
            }
        }

        $validated['user_id'] = Auth::id();

        DB::beginTransaction();
        try {
            $transaction = Transaction::create($validated);
            
            // Update account balance
            $transaction->updateAccountBalance();
            
            DB::commit();

            $transaction->load(['bankAccount', 'counterparty', 'transactionType']);

            return $this->successResponse($transaction, 'Transaction created successfully', 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to create transaction: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Display the specified transaction.
     */
    public function show(Transaction $transaction): JsonResponse
    {
        // Check if the transaction belongs to the authenticated user
        if ($transaction->user_id !== Auth::id()) {
            return $this->forbiddenResponse('You do not have permission to view this transaction');
        }

        $transaction->load(['bankAccount', 'counterparty', 'transactionType']);

        return $this->successResponse($transaction, 'Transaction retrieved successfully');
    }

    /**
     * Update the specified transaction.
     */
    public function update(Request $request, Transaction $transaction): JsonResponse
    {
        // Check if the transaction belongs to the authenticated user
        if ($transaction->user_id !== Auth::id()) {
            return $this->forbiddenResponse('You do not have permission to update this transaction');
        }

        $validated = $request->validate([
            'bank_account_id' => ['sometimes', 'exists:bank_accounts,id'],
            'counterparty_id' => ['nullable', 'exists:counterparties,id'],
            'transaction_type_id' => ['nullable', 'exists:transaction_types,id'],
            'type' => ['sometimes', Rule::in(['income', 'expense'])],
            'amount' => ['sometimes', 'numeric', 'min:0.01', 'max:' . config('strix.business.max_amount', 999999999.99)],
            'currency' => ['sometimes', 'string', Rule::in(config('strix.business.supported_currencies', ['EUR', 'USD', 'BGN']))],
            'description' => ['nullable', 'string', 'max:1000'],
            'executed_at' => ['sometimes', 'date'],
        ]);

        // Additional validation for related resources...
        // (Similar to store method)

        DB::beginTransaction();
        try {
            // Reverse old balance change
            $oldAmount = $transaction->amount;
            $oldType = $transaction->type;
            
            if ($oldType === 'income') {
                $transaction->bankAccount->withdraw($oldAmount);
            } else {
                $transaction->bankAccount->deposit($oldAmount);
            }

            $transaction->update($validated);
            
            // Apply new balance change
            $transaction->updateAccountBalance();
            
            DB::commit();

            $transaction->load(['bankAccount', 'counterparty', 'transactionType']);

            return $this->successResponse($transaction, 'Transaction updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to update transaction: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified transaction.
     */
    public function destroy(Transaction $transaction): JsonResponse
    {
        // Check if the transaction belongs to the authenticated user
        if ($transaction->user_id !== Auth::id()) {
            return $this->forbiddenResponse('You do not have permission to delete this transaction');
        }

        DB::beginTransaction();
        try {
            // Reverse balance change
            if ($transaction->type === 'income') {
                $transaction->bankAccount->withdraw($transaction->amount);
            } else {
                $transaction->bankAccount->deposit($transaction->amount);
            }

            $transaction->delete();
            
            DB::commit();

            return $this->successResponse(null, 'Transaction deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to delete transaction: ' . $e->getMessage(), 500);
        }
    }
}
