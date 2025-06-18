<?php

namespace App\Http\Controllers\Api;

use App\Models\Counterparty;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class CounterpartyApiController extends ApiController
{
    /**
     * Display a listing of the user's counterparties.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Auth::user()->counterparties();

        // Apply search filter
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Load statistics efficiently
        $query->withStatistics()->orderBy('name');

        $perPage = min($request->get('per_page', 15), 100); // Max 100 per page
        $counterparties = $query->paginate($perPage);

        return $this->successResponse($counterparties, 'Counterparties retrieved successfully');
    }

    /**
     * Store a newly created counterparty.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        $validated['user_id'] = Auth::id();

        $counterparty = Counterparty::create($validated);

        return $this->successResponse($counterparty, 'Counterparty created successfully', 201);
    }

    /**
     * Display the specified counterparty.
     */
    public function show(Counterparty $counterparty): JsonResponse
    {
        // Check if the counterparty belongs to the authenticated user
        if ($counterparty->user_id !== Auth::id()) {
            return $this->forbiddenResponse('You do not have permission to view this counterparty');
        }

        // Load statistics
        $counterparty->loadCount('transactions');
        $counterparty->total_income = $counterparty->transactions()
            ->where('type', 'income')
            ->sum('amount');
        $counterparty->total_expenses = $counterparty->transactions()
            ->where('type', 'expense')
            ->sum('amount');

        return $this->successResponse($counterparty, 'Counterparty retrieved successfully');
    }

    /**
     * Update the specified counterparty.
     */
    public function update(Request $request, Counterparty $counterparty): JsonResponse
    {
        // Check if the counterparty belongs to the authenticated user
        if ($counterparty->user_id !== Auth::id()) {
            return $this->forbiddenResponse('You do not have permission to update this counterparty');
        }

        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        $counterparty->update($validated);

        return $this->successResponse($counterparty, 'Counterparty updated successfully');
    }

    /**
     * Remove the specified counterparty.
     */
    public function destroy(Counterparty $counterparty): JsonResponse
    {
        // Check if the counterparty belongs to the authenticated user
        if ($counterparty->user_id !== Auth::id()) {
            return $this->forbiddenResponse('You do not have permission to delete this counterparty');
        }

        // Check if counterparty has transactions
        if ($counterparty->transactions()->exists()) {
            return $this->errorResponse('Cannot delete counterparty with existing transactions', 409);
        }

        $counterparty->delete();

        return $this->successResponse(null, 'Counterparty deleted successfully');
    }

    /**
     * Get counterparty statistics.
     */
    public function statistics(Counterparty $counterparty): JsonResponse
    {
        // Check if the counterparty belongs to the authenticated user
        if ($counterparty->user_id !== Auth::id()) {
            return $this->forbiddenResponse('You do not have permission to view this counterparty');
        }

        $stats = [
            'transactions_count' => $counterparty->transactions()->count(),
            'total_income' => $counterparty->transactions()->where('type', 'income')->sum('amount'),
            'total_expenses' => $counterparty->transactions()->where('type', 'expense')->sum('amount'),
            'last_transaction_date' => $counterparty->transactions()
                ->orderBy('executed_at', 'desc')
                ->value('executed_at'),
        ];

        return $this->successResponse($stats, 'Counterparty statistics retrieved successfully');
    }

    /**
     * Get transactions for a specific counterparty.
     */
    public function transactions(Request $request, Counterparty $counterparty): JsonResponse
    {
        // Check if the counterparty belongs to the authenticated user
        if ($counterparty->user_id !== Auth::id()) {
            return $this->forbiddenResponse('You do not have permission to view this counterparty');
        }

        $query = $counterparty->transactions()
            ->with(['bankAccount', 'transactionType'])
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

        return $this->successResponse($transactions, 'Counterparty transactions retrieved successfully');
    }
}
