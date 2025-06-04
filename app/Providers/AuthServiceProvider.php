<?php

namespace App\Providers;

use App\Models\BankAccount;
use App\Models\Counterparty;
use App\Models\Transaction;
use App\Models\TransactionType;
use App\Models\Transfer;
use App\Policies\BankAccountPolicy;
use App\Policies\CounterpartyPolicy;
use App\Policies\TransactionPolicy;
use App\Policies\TransactionTypePolicy;
use App\Policies\TransferPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        BankAccount::class => BankAccountPolicy::class,
        Counterparty::class => CounterpartyPolicy::class,
        Transfer::class => TransferPolicy::class,
        Transaction::class => TransactionPolicy::class,
        TransactionType::class => TransactionTypePolicy::class,
    ];

    public function boot(): void
    {
        //
    }
} 