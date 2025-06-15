<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
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

    // Remove appends to avoid N+1 queries - calculate these when needed
    // protected $appends = [
    //     'transactions_count',
    //     'total_income',
    //     'total_expenses'
    // ];

    /**
     * Get the total number of transactions (use with loadCount for performance).
     */
    public function getTransactionsCountAttribute(): int
    {
        // Check if already loaded via loadCount
        if (isset($this->attributes['transactions_count'])) {
            return $this->attributes['transactions_count'];
        }

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
     * Scope to load statistics efficiently
     */
    public function scopeWithStatistics($query)
    {
        return $query->withCount('transactions')
            ->addSelect([
                'total_income' => DB::table('transactions')
                    ->selectRaw('COALESCE(SUM(amount), 0)')
                    ->whereColumn('counterparty_id', 'counterparties.id')
                    ->where('type', 'income'),
                'total_expenses' => DB::table('transactions')
                    ->selectRaw('COALESCE(SUM(amount), 0)')
                    ->whereColumn('counterparty_id', 'counterparties.id')
                    ->where('type', 'expense')
            ]);
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
