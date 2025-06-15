<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Transaction;
use App\Models\Transfer;
use App\Traits\NormalizesDecimals;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class BankAccountController extends Controller
{
    use NormalizesDecimals;
    public function __construct()
    {
        $this->middleware('auth');
    }

    // normalizeDecimal method moved to NormalizesDecimals trait

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $accounts = auth()->user()->bankAccounts;
        return view('bank-accounts.index', compact('accounts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('bank-accounts.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Преобразуваме запетая в точка за десетичния разделител
        if ($request->has('initial_balance')) {
            $request->merge(['initial_balance' => $this->normalizeDecimal($request->initial_balance)]);
        }

        $validated = $request->validate($this->getBankAccountValidationRules());

        DB::transaction(function () use ($validated, $request) {
            // If this account is set as default, remove default from other accounts
            if ($request->boolean('is_default')) {
                auth()->user()->bankAccounts()->where('is_default', true)
                    ->update(['is_default' => false]);
            }
            // If this is the first account, make it default
            elseif (auth()->user()->bankAccounts()->count() === 0) {
                $validated['is_default'] = true;
            }

            // Добавяме началния баланс
            $validated['balance'] = $validated['initial_balance'];
            unset($validated['initial_balance']);

            auth()->user()->bankAccounts()->create($validated);
        });

        return redirect()->route('bank-accounts.index')
            ->with('success', 'Bank account created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(BankAccount $bankAccount)
    {
        $this->authorize('view', $bankAccount);

        $transactions = $bankAccount->transactions()
            ->with(['counterparty', 'transactionType'])
            ->latest('executed_at')
            ->paginate(10, ['*'], 'transactions_page');

        $transfers = Transfer::query()
            ->where(function($query) use ($bankAccount) {
                $query->where('from_account_id', $bankAccount->id)
                    ->orWhere('to_account_id', $bankAccount->id);
            })
            ->latest('executed_at')
            ->paginate(10, ['*'], 'transfers_page');

        return view('bank-accounts.show', compact('bankAccount', 'transactions', 'transfers'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BankAccount $bankAccount)
    {
        $this->authorize('update', $bankAccount);
        return view('bank-accounts.edit', compact('bankAccount'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BankAccount $bankAccount)
    {
        $this->authorize('update', $bankAccount);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'currency' => ['required', 'string', 'size:3'],
            'is_active' => ['boolean'],
            'is_default' => ['boolean']
        ]);

        DB::transaction(function () use ($validated, $request, $bankAccount) {
            // If this account is set as default, remove default from other accounts
            if ($request->boolean('is_default') && !$bankAccount->is_default) {
                auth()->user()->bankAccounts()->where('is_default', true)
                    ->update(['is_default' => false]);
            }

            $bankAccount->update($validated);
        });

        return redirect()->route('bank-accounts.index')
            ->with('success', 'Bank account updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BankAccount $bankAccount)
    {
        $this->authorize('delete', $bankAccount);

        if ($bankAccount->is_default) {
            // Намираме други активни акаунти
            $otherAccounts = Auth::user()->bankAccounts()
                ->where('id', '!=', $bankAccount->id)
                ->where('is_active', true)
                ->get();
            
            // Ако няма други акаунти, само информираме
            if ($otherAccounts->isEmpty()) {
                $bankAccount->delete();
                return redirect()->route('bank-accounts.index')
                    ->with('success', __('Bank account deleted successfully. You have no other accounts to set as default.'));
            }

            // Ако има други акаунти, добавяме ги в сесията за показване във view
            session()->flash('select_default_account', true);
            session()->flash('deleted_account_id', $bankAccount->id);
            session()->flash('other_accounts', $otherAccounts);
            
            return redirect()->route('bank-accounts.index')
                ->with('warning', __('Please select a new default account before deleting the current default account.'));
        }

        $bankAccount->delete();

        return redirect()->route('bank-accounts.index')
            ->with('success', __('Bank account deleted successfully.'));
    }

    public function setDefault(Request $request)
    {
        $validated = $request->validate([
            'new_default_account_id' => ['required', 'exists:bank_accounts,id'],
            'deleted_account_id' => ['required', 'exists:bank_accounts,id'],
        ]);

        DB::transaction(function () use ($validated) {
            // Задаваме новия default акаунт
            Auth::user()->bankAccounts()
                ->where('id', $validated['new_default_account_id'])
                ->update(['is_default' => true]);

            // Изтриваме стария акаунт
            Auth::user()->bankAccounts()
                ->where('id', $validated['deleted_account_id'])
                ->delete();
        });

        return redirect()->route('bank-accounts.index')
            ->with('success', __('Default account updated and old account deleted successfully.'));
    }
}
