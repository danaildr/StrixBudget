<?php

namespace App\Http\Controllers\Api;

use App\Models\TransactionType;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class TransactionTypeApiController extends ApiController
{
    /**
     * Display a listing of the user's transaction types.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Auth::user()->transactionTypes();

        // Apply search filter
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $query->withCount('transactions')->orderBy('name');

        $perPage = min($request->get('per_page', 15), 100); // Max 100 per page
        $transactionTypes = $query->paginate($perPage);

        return $this->successResponse($transactionTypes, 'Transaction types retrieved successfully');
    }

    /**
     * Store a newly created transaction type.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $validated['user_id'] = Auth::id();

        $transactionType = TransactionType::create($validated);

        return $this->successResponse($transactionType, 'Transaction type created successfully', 201);
    }

    /**
     * Display the specified transaction type.
     */
    public function show(TransactionType $transactionType): JsonResponse
    {
        // Check if the transaction type belongs to the authenticated user
        if ($transactionType->user_id !== Auth::id()) {
            return $this->forbiddenResponse('You do not have permission to view this transaction type');
        }

        $transactionType->loadCount('transactions');

        return $this->successResponse($transactionType, 'Transaction type retrieved successfully');
    }

    /**
     * Update the specified transaction type.
     */
    public function update(Request $request, TransactionType $transactionType): JsonResponse
    {
        // Check if the transaction type belongs to the authenticated user
        if ($transactionType->user_id !== Auth::id()) {
            return $this->forbiddenResponse('You do not have permission to update this transaction type');
        }

        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $transactionType->update($validated);

        return $this->successResponse($transactionType, 'Transaction type updated successfully');
    }

    /**
     * Remove the specified transaction type.
     */
    public function destroy(TransactionType $transactionType): JsonResponse
    {
        // Check if the transaction type belongs to the authenticated user
        if ($transactionType->user_id !== Auth::id()) {
            return $this->forbiddenResponse('You do not have permission to delete this transaction type');
        }

        // Check if transaction type has transactions
        if ($transactionType->transactions()->exists()) {
            return $this->errorResponse('Cannot delete transaction type with existing transactions', 409);
        }

        $transactionType->delete();

        return $this->successResponse(null, 'Transaction type deleted successfully');
    }

    /**
     * Get transaction type statistics.
     */
    public function statistics(TransactionType $transactionType): JsonResponse
    {
        // Check if the transaction type belongs to the authenticated user
        if ($transactionType->user_id !== Auth::id()) {
            return $this->forbiddenResponse('You do not have permission to view this transaction type');
        }

        $stats = [
            'transactions_count' => $transactionType->transactions()->count(),
            'total_income' => $transactionType->transactions()->where('type', 'income')->sum('amount'),
            'total_expenses' => $transactionType->transactions()->where('type', 'expense')->sum('amount'),
            'last_transaction_date' => $transactionType->transactions()
                ->orderBy('executed_at', 'desc')
                ->value('executed_at'),
        ];

        return $this->successResponse($stats, 'Transaction type statistics retrieved successfully');
    }

    /**
     * Get transactions for a specific transaction type.
     */
    public function transactions(Request $request, TransactionType $transactionType): JsonResponse
    {
        // Check if the transaction type belongs to the authenticated user
        if ($transactionType->user_id !== Auth::id()) {
            return $this->forbiddenResponse('You do not have permission to view this transaction type');
        }

        $query = $transactionType->transactions()
            ->with(['bankAccount', 'counterparty'])
            ->orderBy('executed_at', 'desc');

        // Apply filters
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('from_date')) {
            $query->whereDate('executed_at', '>=', $request->from_date);
        }

        if ($request->has('to_date')) {
            $query->whereDate('executed_at', '<=', $request->to_date);
        }

        $perPage = min($request->get('per_page', 15), 100); // Max 100 per page
        $transactions = $query->paginate($perPage);

        return $this->successResponse($transactions, 'Transaction type transactions retrieved successfully');
    }
}
