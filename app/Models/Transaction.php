<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'bank_account_id',
        'counterparty_id',
        'transaction_type_id',
        'type',
        'amount',
        'currency',
        'description',
        'attachment_path',
        'executed_at',
    ];

    protected $casts = [
        'executed_at' => 'datetime',
        'amount' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function counterparty(): BelongsTo
    {
        return $this->belongsTo(Counterparty::class);
    }

    public function transactionType(): BelongsTo
    {
        return $this->belongsTo(TransactionType::class);
    }

    public function updateAccountBalance(): void
    {
        if ($this->type === 'income') {
            $this->bankAccount->deposit($this->amount);
        } else {
            $this->bankAccount->withdraw($this->amount);
        }
    }
} 