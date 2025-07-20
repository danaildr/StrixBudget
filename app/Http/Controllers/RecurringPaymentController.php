<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RecurringPayment;
use App\Models\BankAccount;
use App\Models\Counterparty;
use App\Models\TransactionType;
use Illuminate\Support\Facades\Auth;

class RecurringPaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $payments = RecurringPayment::where('user_id', Auth::id())->paginate(15);
        return view('recurring-payments.index', compact('payments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $accounts = BankAccount::where('user_id', Auth::id())->get();
        $counterparties = Counterparty::where('user_id', Auth::id())->get();
        $categories = TransactionType::where('user_id', Auth::id())->get();
        return view('recurring-payments.create', compact('accounts', 'counterparties', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'bank_account_id' => 'required|exists:bank_accounts,id',
            'counterparty_id' => 'nullable|exists:counterparties,id',
            'transaction_type_id' => 'nullable|exists:transaction_types,id',
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'required|string|size:3',
            'description' => 'nullable|string',
            'repeat_type' => 'required|string',
            'repeat_interval' => 'nullable|integer|min:1',
            'repeat_unit' => 'nullable|string',
            'period_start_day' => 'nullable|integer|min:1|max:31',
            'period_end_day' => 'nullable|integer|min:1|max:31',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);
        $validated['user_id'] = Auth::id();
        RecurringPayment::create($validated);
        return redirect()->route('recurring-payments.index')->with('success', 'Плащането е създадено успешно!');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $payment = RecurringPayment::where('user_id', Auth::id())->findOrFail($id);

        // Статистики
        $transactionsQuery = \App\Models\Transaction::where('user_id', $payment->user_id)
            ->where('bank_account_id', $payment->bank_account_id)
            ->when($payment->counterparty_id, fn($q) => $q->where('counterparty_id', $payment->counterparty_id))
            ->when($payment->transaction_type_id, fn($q) => $q->where('transaction_type_id', $payment->transaction_type_id))
            ->where('amount', $payment->amount)
            ->when($payment->description, fn($q) => $q->where('description', $payment->description));

        $totalStats = [
            'count' => $transactionsQuery->count(),
            'income' => (clone $transactionsQuery)->where('type', 'income')->sum('amount'),
            'expenses' => (clone $transactionsQuery)->where('type', 'expense')->sum('amount'),
        ];

        $year = now()->year;
        $yearStats = [
            'count' => (clone $transactionsQuery)->whereYear('executed_at', $year)->count(),
            'income' => (clone $transactionsQuery)->whereYear('executed_at', $year)->where('type', 'income')->sum('amount'),
            'expenses' => (clone $transactionsQuery)->whereYear('executed_at', $year)->where('type', 'expense')->sum('amount'),
        ];

        $month = now()->month;
        $monthStats = [
            'count' => (clone $transactionsQuery)->whereYear('executed_at', $year)->whereMonth('executed_at', $month)->count(),
            'income' => (clone $transactionsQuery)->whereYear('executed_at', $year)->whereMonth('executed_at', $month)->where('type', 'income')->sum('amount'),
            'expenses' => (clone $transactionsQuery)->whereYear('executed_at', $year)->whereMonth('executed_at', $month)->where('type', 'expense')->sum('amount'),
        ];

        $transactions = (clone $transactionsQuery)->orderByDesc('executed_at')->paginate(10);

        return view('recurring-payments.show', compact('payment', 'totalStats', 'yearStats', 'monthStats', 'transactions'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $payment = RecurringPayment::where('user_id', Auth::id())->findOrFail($id);
        $accounts = BankAccount::where('user_id', Auth::id())->get();
        $counterparties = Counterparty::where('user_id', Auth::id())->get();
        $categories = TransactionType::where('user_id', Auth::id())->get();
        return view('recurring-payments.edit', compact('payment', 'accounts', 'counterparties', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $payment = RecurringPayment::where('user_id', Auth::id())->findOrFail($id);
        $validated = $request->validate([
            'bank_account_id' => 'required|exists:bank_accounts,id',
            'counterparty_id' => 'nullable|exists:counterparties,id',
            'transaction_type_id' => 'nullable|exists:transaction_types,id',
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'required|string|size:3',
            'description' => 'nullable|string',
            'repeat_type' => 'required|string',
            'repeat_interval' => 'nullable|integer|min:1',
            'repeat_unit' => 'nullable|string',
            'period_start_day' => 'nullable|integer|min:1|max:31',
            'period_end_day' => 'nullable|integer|min:1|max:31',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);
        $payment->update($validated);
        return redirect()->route('recurring-payments.index')->with('success', 'Плащането е обновено успешно!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $payment = RecurringPayment::where('user_id', Auth::id())->findOrFail($id);
        $payment->delete();
        return redirect()->route('recurring-payments.index')->with('success', 'Плащането е изтрито успешно!');
    }

    // Създаване на транзакция от шаблон
    public function makeTransaction($id)
    {
        $payment = RecurringPayment::where('user_id', Auth::id())->findOrFail($id);
        // Пренасочване към формата за нова транзакция с попълнени данни
        return redirect()->route('transactions.create', [
            'bank_account_id' => $payment->bank_account_id,
            'counterparty_id' => $payment->counterparty_id,
            'transaction_type_id' => $payment->transaction_type_id,
            'amount' => $payment->amount,
            'currency' => $payment->currency,
            'description' => $payment->description,
        ]);
    }
}
