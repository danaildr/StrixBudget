<?php

namespace App\Http\Controllers;

use App\Models\Counterparty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use App\Models\User;

class CounterpartyController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = Auth::user()->counterparties()
            ->when(request('search'), function ($query, $search) {
                $search = mb_strtolower('%' . $search . '%');
                $query->where(function ($q) use ($search) {
                    $q->whereRaw('lower(name) like ?', [$search])
                      ->orWhereRaw('lower(email) like ?', [$search])
                      ->orWhereRaw('lower(phone) like ?', [$search]);
                });
            })
            ->orderBy('name');
        
        $counterparties = $query->paginate(25);
        
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

        // Вземаме всички транзакции с пагинация
        $transactions = $counterparty->transactions()
            ->with(['bankAccount', 'transactionType'])
            ->latest('executed_at')
            ->paginate(10);

        return view('counterparties.show', compact('counterparty', 'transactions'));
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
