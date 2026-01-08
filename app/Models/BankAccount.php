<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Transaction;

class BankAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'currency',
        'is_active',
        'is_default',
        'user_id',
        'balance',
        'iban'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'balance' => 'decimal:2'
    ];

    protected $appends = [
        'transactions_count',
        'total_income',
        'total_expenses'
    ];

    /**
     * Get the user that owns the bank account.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the transactions for this bank account.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Get the total number of transactions.
     */
    public function getTransactionsCountAttribute(): int
    {
        return $this->transactions()->count();
    }

    /**
     * Get the total income for this account.
     */
    public function getTotalIncomeAttribute(): float
    {
        return $this->transactions()
            ->where('type', 'income')
            ->sum('amount');
    }

    /**
     * Get the total expenses for this account.
     */
    public function getTotalExpensesAttribute(): float
    {
        return $this->transactions()
            ->where('type', 'expense')
            ->sum('amount');
    }

    /**
     * Добавя сума към баланса на сметката
     */
    public function deposit(float $amount): void
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Amount must be positive');
        }
        
        $this->balance += $amount;
        $this->save();
    }

    /**
     * Изважда сума от баланса на сметката
     */
    public function withdraw(float $amount): void
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Amount must be positive');
        }

        if ($amount > $this->balance) {
            throw new \InvalidArgumentException('Insufficient funds');
        }

        $this->balance -= $amount;
        $this->save();
    }

    /**
     * Проверява дали има достатъчно средства
     */
    public function hasSufficientFunds(float $amount): bool
    {
        return $this->balance >= $amount;
    }
}
