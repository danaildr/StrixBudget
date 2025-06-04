<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Counterparty extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'email',
        'phone',
        'user_id'
    ];

    protected $appends = [
        'transactions_count',
        'total_income',
        'total_expenses'
    ];

    /**
     * Get the total number of transactions.
     */
    public function getTransactionsCountAttribute(): int
    {
        return $this->transactions()->count();
    }

    /**
     * Get the total income from this counterparty.
     */
    public function getTotalIncomeAttribute(): float
    {
        return $this->transactions()
            ->where('type', 'income')
            ->sum('amount');
    }

    /**
     * Get the total expenses for this counterparty.
     */
    public function getTotalExpensesAttribute(): float
    {
        return $this->transactions()
            ->where('type', 'expense')
            ->sum('amount');
    }

    /**
     * Get the user that owns the counterparty.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the transactions for the counterparty.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}
