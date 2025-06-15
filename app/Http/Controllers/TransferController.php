<?php

namespace App\Http\Controllers;

use App\Models\Transfer;
use App\Models\BankAccount;
use App\Models\User;
use App\Services\ExchangeRateService;
use App\Traits\HasDateFilters;
use App\Traits\NormalizesDecimals;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransferController extends Controller
{
    use HasDateFilters, NormalizesDecimals;
    private $exchangeRateService;

    public function __construct(ExchangeRateService $exchangeRateService)
    {
        $this->middleware('auth');
        $this->exchangeRateService = $exchangeRateService;
    }

    /**
     * Нормализира десетичен разделител от запетая към точка
     */
    private function normalizeDecimal($value)
    {
        if (is_string($value)) {
            // Заменяме запетая с точка за десетичен разделител
            return str_replace(',', '.', $value);
        }
        return $value;
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

        // Apply date filters using trait
        $this->applyDateFilters($query, $request);

        // Apply other filters
        if ($request->filled('from_account_id')) {
            $query->where('from_account_id', $request->from_account_id);
        }

        if ($request->filled('to_account_id')) {
            $query->where('to_account_id', $request->to_account_id);
        }

        // Apply amount filters using trait
        $this->applyAmountFilters($query, $request, 'amount_from');

        $transfers = $query->latest('executed_at')->paginate($this->getPaginationSize());
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
        // Преобразуваме запетая в точка за десетичните разделители
        if ($request->has('amount_from')) {
            $request->merge(['amount_from' => $this->normalizeDecimal($request->amount_from)]);
        }
        if ($request->has('exchange_rate')) {
            $request->merge(['exchange_rate' => $this->normalizeDecimal($request->exchange_rate)]);
        }

        $validated = $request->validate($this->getTransferValidationRules());

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

    // Edit, update and destroy methods not implemented yet
    // TODO: Implement transfer editing and deletion functionality
}
