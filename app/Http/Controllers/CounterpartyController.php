<?php

namespace App\Http\Controllers;

use App\Models\Counterparty;
use App\Traits\HasDateFilters;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use App\Models\User;

class CounterpartyController extends Controller
{
    use AuthorizesRequests, ValidatesRequests, HasDateFilters;

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Auth::user()->counterparties();

        // Apply search filter using trait
        $this->applySearchFilter($query, $request, ['name', 'email', 'phone']);

        // Load statistics efficiently to avoid N+1 queries
        $query->withStatistics()->orderBy('name');

        $counterparties = $query->paginate($this->getPaginationSize('search'));

        return view('counterparties.index', compact('counterparties'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('counterparties.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        /** @var User $user */
        $user = Auth::user();
        $counterparty = $user->counterparties()->create($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => __('Counterparty created successfully'),
                'counterparty' => [
                    'id' => $counterparty->id,
                    'name' => $counterparty->name,
                ]
            ]);
        }

        return redirect()->route('counterparties.index')
            ->with('success', 'Counterparty created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Counterparty $counterparty)
    {
        $this->authorize('view', $counterparty);

        // Изчисляваме статистики за различни периоди
        $now = now();
        $lastMonth = $now->copy()->subMonth();
        $lastYear = $now->copy()->subYear();

        // Общи статистики
        $totalStats = [
            'count' => $counterparty->transactions()->count(),
            'income' => $counterparty->transactions()->where('type', 'income')->sum('amount'),
            'expenses' => $counterparty->transactions()->where('type', 'expense')->sum('amount')
        ];

        // Статистики за последния месец
        $monthStats = [
            'count' => $counterparty->transactions()
                ->where('executed_at', '>=', $lastMonth)
                ->count(),
            'income' => $counterparty->transactions()
                ->where('type', 'income')
                ->where('executed_at', '>=', $lastMonth)
                ->sum('amount'),
            'expenses' => $counterparty->transactions()
                ->where('type', 'expense')
                ->where('executed_at', '>=', $lastMonth)
                ->sum('amount')
        ];

        // Статистики за последната година
        $yearStats = [
            'count' => $counterparty->transactions()
                ->where('executed_at', '>=', $lastYear)
                ->count(),
            'income' => $counterparty->transactions()
                ->where('type', 'income')
                ->where('executed_at', '>=', $lastYear)
                ->sum('amount'),
            'expenses' => $counterparty->transactions()
                ->where('type', 'expense')
                ->where('executed_at', '>=', $lastYear)
                ->sum('amount')
        ];

        // Вземаме всички транзакции с пагинация
        $transactions = $counterparty->transactions()
            ->with(['bankAccount', 'transactionType'])
            ->latest('executed_at')
            ->paginate(20);

        return view('counterparties.show', compact(
            'counterparty',
            'transactions',
            'totalStats',
            'monthStats',
            'yearStats'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Counterparty $counterparty)
    {
        $this->authorize('update', $counterparty);
        return view('counterparties.edit', compact('counterparty'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Counterparty $counterparty)
    {
        $this->authorize('update', $counterparty);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        $counterparty->update($validated);

        return redirect()->route('counterparties.index')
            ->with('success', 'Counterparty updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Counterparty $counterparty)
    {
        $this->authorize('delete', $counterparty);
        
        $counterparty->delete();

        return redirect()->route('counterparties.index')
            ->with('success', 'Counterparty deleted successfully.');
    }
}
