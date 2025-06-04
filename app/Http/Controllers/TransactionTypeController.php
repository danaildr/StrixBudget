<?php

namespace App\Http\Controllers;

use App\Models\TransactionType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        /** @var User $user */
        $user = Auth::user();
        $query = $user->transactionTypes()
            ->when(request('search'), function ($query, $search) {
                $search = mb_strtolower('%' . $search . '%');
                $query->where(function ($q) use ($search) {
                    $q->whereRaw('lower(name) like ?', [$search])
                      ->orWhereRaw('lower(description) like ?', [$search]);
                });
            })
            ->withCount('transactions')
            ->latest();
        
        $transactionTypes = $query->paginate(25);
        
        return view('transaction-types.index', compact('transactionTypes'));
    }

    public function create()
    {
        return view('transaction-types.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string']
        ]);

        /** @var User $user */
        $user = Auth::user();
        $transactionType = $user->transactionTypes()->create($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => __('Transaction type created successfully'),
                'transactionType' => [
                    'id' => $transactionType->id,
                    'name' => $transactionType->name,
                ]
            ]);
        }

        return redirect()->route('transaction-types.index')
            ->with('success', 'Transaction type created successfully.');
    }

    public function edit(TransactionType $transactionType)
    {
        $this->authorize('update', $transactionType);
        return view('transaction-types.edit', compact('transactionType'));
    }

    public function update(Request $request, TransactionType $transactionType)
    {
        $this->authorize('update', $transactionType);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string']
        ]);

        $transactionType->update($validated);

        return redirect()->route('transaction-types.index')
            ->with('success', 'Transaction type updated successfully.');
    }

    public function destroy(TransactionType $transactionType)
    {
        $this->authorize('delete', $transactionType);

        if ($transactionType->transactions()->exists()) {
            return back()->with('error', 'Cannot delete transaction type that has transactions.');
        }

        $transactionType->delete();

        return redirect()->route('transaction-types.index')
            ->with('success', 'Transaction type deleted successfully.');
    }
} 