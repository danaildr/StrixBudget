<?php

namespace App\Http\Controllers;

use App\Models\Transfer;
use App\Models\BankAccount;
use App\Models\User;
use App\Services\ExchangeRateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransferController extends Controller
{
    private $exchangeRateService;

    public function __construct(ExchangeRateService $exchangeRateService)
    {
        $this->middleware('auth');
        $this->exchangeRateService = $exchangeRateService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        
        $query = $user->transfers()
            ->with(['fromAccount', 'toAccount']);

        // Филтър по дата
        if ($request->filled('start_date')) {
            $query->whereDate('executed_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('executed_at', '<=', $request->end_date);
        }

        // Филтър по сметка-източник
        if ($request->filled('from_account_id')) {
            $query->where('from_account_id', $request->from_account_id);
        }

        // Филтър по сметка-получател
        if ($request->filled('to_account_id')) {
            $query->where('to_account_id', $request->to_account_id);
        }

        // Филтър по сума (от)
        if ($request->filled('min_amount')) {
            $query->where('amount_from', '>=', $request->min_amount);
        }

        // Филтър по сума (до)
        if ($request->filled('max_amount')) {
            $query->where('amount_from', '<=', $request->max_amount);
        }

        $transfers = $query->latest('executed_at')->paginate(25);
        $accounts = $user->bankAccounts;
        
        return view('transfers.index', compact('transfers', 'accounts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $accounts = Auth::user()->bankAccounts()
            ->where('is_active', true)
            ->get();
            
        return view('transfers.create', [
            'accounts' => $accounts,
            'exchangeRateService' => $this->exchangeRateService
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'from_account_id' => ['required', 'exists:bank_accounts,id'],
            'to_account_id' => ['required', 'exists:bank_accounts,id', 'different:from_account_id'],
            'amount_from' => ['required', 'numeric', 'min:0.01'],
            'exchange_rate' => ['required', 'numeric', 'min:0.000001'],
            'description' => ['nullable', 'string'],
            'executed_at' => ['required', 'date', 'before_or_equal:now']
        ]);

        // Получаване на сметките и проверка на собствеността
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $fromAccount = $user->bankAccounts()->findOrFail($validated['from_account_id']);
        $toAccount = $user->bankAccounts()->findOrFail($validated['to_account_id']);

        // Проверка за достатъчно средства
        if (!$fromAccount->hasSufficientFunds($validated['amount_from'])) {
            $formattedAmount = number_format($validated['amount_from'], 2, ',', ' ');
            $formattedBalance = number_format($fromAccount->balance, 2, ',', ' ');
            return back()
                ->withErrors(['amount_from' => "Недостатъчна наличност. Опитвате да преведете {$formattedAmount} {$fromAccount->currency}, но разполагате само с {$formattedBalance} {$fromAccount->currency} в избраната сметка."])
                ->withInput();
        }

        // Изчисляване на сумата в целевата валута
        $amountTo = $validated['amount_from'] * $validated['exchange_rate'];

        DB::transaction(function () use ($validated, $fromAccount, $toAccount, $amountTo, $user) {
            // Създаване на трансфера
            $user->transfers()->create([
                'from_account_id' => $fromAccount->id,
                'to_account_id' => $toAccount->id,
                'amount_from' => $validated['amount_from'],
                'currency_from' => $fromAccount->currency,
                'amount_to' => $amountTo,
                'currency_to' => $toAccount->currency,
                'exchange_rate' => $validated['exchange_rate'],
                'description' => $validated['description'],
                'executed_at' => $validated['executed_at'],
            ]);

            // Актуализиране на балансите
            $fromAccount->withdraw($validated['amount_from']);
            $toAccount->deposit($amountTo);
        });

        return redirect()->route('transfers.index')
            ->with('success', 'Transfer completed successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Transfer $transfer)
    {
        $this->authorize('view', $transfer);
        return view('transfers.show', compact('transfer'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
