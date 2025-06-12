<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionType;
use App\Models\Counterparty;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class TransactionController extends Controller
{
    /** @var User */
    protected $user;

    public function __construct()
    {
        $this->middleware('auth');
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

    /** @return \Illuminate\View\View */
    public function index(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        
        $query = $user->transactions()
            ->with(['bankAccount', 'counterparty', 'transactionType']);

        // Филтър по дата
        if ($request->filled('start_date')) {
            $query->whereDate('executed_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('executed_at', '<=', $request->end_date);
        }

        // Филтър по тип транзакция (income/expense)
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Филтър по банкова сметка
        if ($request->filled('bank_account_id')) {
            $query->where('bank_account_id', $request->bank_account_id);
        }

        // Филтър по контрагент
        if ($request->filled('counterparty_id')) {
            $query->where('counterparty_id', $request->counterparty_id);
        }

        // Филтър по тип транзакция (категория)
        if ($request->filled('transaction_type_id')) {
            $query->where('transaction_type_id', $request->transaction_type_id);
        }

        $transactions = $query->latest('executed_at')->paginate(25);
        
        // Данни за филтрите
        $bankAccounts = $user->bankAccounts;
        $counterparties = $user->counterparties;
        $transactionTypes = $user->transactionTypes;
        
        $hasCounterparties = $user->counterparties()->exists();
        $hasTransactionTypes = $user->transactionTypes()->exists();
        
        return view('transactions.index', compact(
            'transactions',
            'hasCounterparties',
            'hasTransactionTypes',
            'bankAccounts',
            'counterparties',
            'transactionTypes'
        ));
    }

    /** @return \Illuminate\View\View */
    public function create()
    {
        /** @var User $user */
        $user = Auth::user();
        $accounts = $user->bankAccounts()->where('is_active', true)->get();
        $counterparties = $user->counterparties;
        $transactionTypes = $user->transactionTypes;
        
        return view('transactions.create', compact('accounts', 'counterparties', 'transactionTypes'));
    }

    /** @return \Illuminate\Http\RedirectResponse */
    public function store(Request $request)
    {
        // Преобразуваме запетая в точка за десетичния разделител
        if ($request->has('amount')) {
            $request->merge(['amount' => $this->normalizeDecimal($request->amount)]);
        }

        $validated = $request->validate([
            'bank_account_id' => ['required', 'exists:bank_accounts,id'],
            'type' => ['required', 'in:income,expense'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'description' => ['nullable', 'string'],
            'executed_at' => ['required', 'date', 'before_or_equal:now'],
            'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'counterparty_id' => ['required', 'exists:counterparties,id'],
            'transaction_type_id' => ['required', 'exists:transaction_types,id'],
        ]);

        return DB::transaction(function () use ($request, $validated) {
            /** @var User $user */
            $user = Auth::user();
            
            // Проверка за достатъчно средства при разходни транзакции
            $bankAccount = $user->bankAccounts()->findOrFail($validated['bank_account_id']);
            if ($validated['type'] === 'expense' && !$bankAccount->hasSufficientFunds($validated['amount'])) {
                $formattedAmount = number_format($validated['amount'], 2, ',', ' ');
                $formattedBalance = number_format($bankAccount->balance, 2, ',', ' ');
                return back()
                    ->withErrors(['amount' => "Недостатъчна наличност. Опитвате да преведете {$formattedAmount} {$bankAccount->currency}, но разполагате само с {$formattedBalance} {$bankAccount->currency}."])
                    ->withInput();
            }

            // Обработка на прикачения файл
            $attachmentPath = null;
            if ($request->hasFile('attachment')) {
                $attachmentPath = $request->file('attachment')->store('attachments', 'public');
            }

            // Създаване на транзакцията
            $bankAccount = $user->bankAccounts()->findOrFail($validated['bank_account_id']);
            
            $transaction = $user->transactions()->create([
                'bank_account_id' => $bankAccount->id,
                'counterparty_id' => $validated['counterparty_id'],
                'transaction_type_id' => $validated['transaction_type_id'],
                'type' => $validated['type'],
                'amount' => $validated['amount'],
                'currency' => $bankAccount->currency,
                'description' => $validated['description'],
                'attachment_path' => $attachmentPath,
                'executed_at' => $validated['executed_at'],
            ]);

            // Актуализиране на баланса на сметката
            $transaction->updateAccountBalance();

            return redirect()->route('transactions.index')
                ->with('success', 'Transaction created successfully.');
        });
    }

    public function show(Transaction $transaction)
    {
        $this->authorize('view', $transaction);
        return view('transactions.show', compact('transaction'));
    }

    public function edit(Transaction $transaction)
    {
        $this->authorize('update', $transaction);
        
        /** @var User $user */
        $user = Auth::user();
        $accounts = $user->bankAccounts()->where('is_active', true)->get();
        $counterparties = $user->counterparties;
        $transactionTypes = $user->transactionTypes;
        
        return view('transactions.edit', compact('transaction', 'accounts', 'counterparties', 'transactionTypes'));
    }

    public function update(Request $request, Transaction $transaction)
    {
        $this->authorize('update', $transaction);

        // Преобразуваме запетая в точка за десетичния разделител
        if ($request->has('amount')) {
            $request->merge(['amount' => $this->normalizeDecimal($request->amount)]);
        }

        $validated = $request->validate([
            'bank_account_id' => ['required', 'exists:bank_accounts,id'],
            'type' => ['required', 'in:income,expense'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'description' => ['nullable', 'string'],
            'executed_at' => ['required', 'date', 'before_or_equal:now'],
            'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'counterparty_id' => ['required', 'exists:counterparties,id'],
            'transaction_type_id' => ['required', 'exists:transaction_types,id'],
        ]);

        return DB::transaction(function () use ($request, $validated, $transaction) {
            /** @var User $user */
            $user = Auth::user();
            $newBankAccount = $user->bankAccounts()->findOrFail($validated['bank_account_id']);
            $oldBankAccount = $transaction->bankAccount;

            // Запазваме старите стойности преди обновяването
            $oldType = $transaction->type;
            $oldAmount = $transaction->amount;
            $oldBankAccountId = $transaction->bank_account_id;

            // Първо възстановяваме предишния баланс от старата сметка
            if ($oldType === 'income') {
                $oldBankAccount->withdraw($oldAmount);
            } else {
                $oldBankAccount->deposit($oldAmount);
            }

            // Ако сметката е различна, refresh новата сметка за да получим актуалния баланс
            if ($oldBankAccountId !== $validated['bank_account_id']) {
                $newBankAccount->refresh();
            }

            // Проверяваме дали има достатъчно средства в новата сметка за новата сума ако е разход
            if ($validated['type'] === 'expense' && !$newBankAccount->hasSufficientFunds($validated['amount'])) {
                // Възстановяваме оригиналния баланс в старата сметка
                if ($oldType === 'income') {
                    $oldBankAccount->deposit($oldAmount);
                } else {
                    $oldBankAccount->withdraw($oldAmount);
                }

                $formattedAmount = number_format($validated['amount'], 2, ',', ' ');
                $formattedBalance = number_format($newBankAccount->balance, 2, ',', ' ');
                return back()
                    ->withErrors(['amount' => "Недостатъчна наличност. Опитвате да преведете {$formattedAmount} {$newBankAccount->currency}, но разполагате само с {$formattedBalance} {$newBankAccount->currency}."])
                    ->withInput();
            }

            // Обработваме новия прикачен файл, ако има такъв
            if ($request->hasFile('attachment')) {
                // Изтриваме стария файл, ако има такъв
                if ($transaction->attachment_path) {
                    Storage::disk('public')->delete($transaction->attachment_path);
                }
                $attachmentPath = $request->file('attachment')->store('attachments', 'public');
                $validated['attachment_path'] = $attachmentPath;
            }

            // Актуализираме транзакцията
            $transaction->update([
                'bank_account_id' => $newBankAccount->id,
                'counterparty_id' => $validated['counterparty_id'],
                'transaction_type_id' => $validated['transaction_type_id'],
                'type' => $validated['type'],
                'amount' => $validated['amount'],
                'currency' => $newBankAccount->currency,
                'description' => $validated['description'],
                'attachment_path' => $validated['attachment_path'] ?? $transaction->attachment_path,
                'executed_at' => $validated['executed_at'],
            ]);

            // Прилагаме новия баланс в новата сметка
            // Ако сметката е същата, тя вече е била обновена по-горе, така че трябва да използваме fresh инстанция
            if ($oldBankAccountId === $validated['bank_account_id']) {
                $newBankAccount->refresh();
            }

            if ($validated['type'] === 'income') {
                $newBankAccount->deposit($validated['amount']);
            } else {
                $newBankAccount->withdraw($validated['amount']);
            }

            return redirect()->route('transactions.index')
                ->with('success', 'Transaction updated successfully.');
        });
    }

    public function destroy(Transaction $transaction)
    {
        $this->authorize('delete', $transaction);

        DB::transaction(function () use ($transaction) {
            // Възстановяваме баланса преди изтриване
            if ($transaction->type === 'income') {
                $transaction->bankAccount->withdraw($transaction->amount);
            } else {
                $transaction->bankAccount->deposit($transaction->amount);
            }

            // Изтриваме прикачения файл, ако има такъв
            if ($transaction->attachment_path) {
                Storage::disk('public')->delete($transaction->attachment_path);
            }

            $transaction->delete();
        });

        return redirect()->route('transactions.index')
            ->with('success', 'Transaction deleted successfully.');
    }
} 