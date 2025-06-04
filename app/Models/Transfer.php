<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'from_account_id',
        'to_account_id',
        'amount_from',
        'currency_from',
        'amount_to',
        'currency_to',
        'exchange_rate',
        'description',
        'executed_at'
    ];

    protected $casts = [
        'amount_from' => 'decimal:2',
        'amount_to' => 'decimal:2',
        'exchange_rate' => 'decimal:4',
        'executed_at' => 'datetime'
    ];

    /**
     * Get the user that owns the transfer.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the source bank account.
     */
    public function fromAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class, 'from_account_id');
    }

    /**
     * Get the destination bank account.
     */
    public function toAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class, 'to_account_id');
    }
}
