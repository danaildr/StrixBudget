<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScheduledPayment extends Model
{
    protected $fillable = [
        'user_id',
        'bank_account_id',
        'counterparty_id',
        'transaction_type_id',
        'amount',
        'currency',
        'description',
        'scheduled_date',
        'period_start_date',
        'period_end_date',
        'is_active',
        'transaction_id',
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function bankAccount() { return $this->belongsTo(BankAccount::class); }
    public function counterparty() { return $this->belongsTo(Counterparty::class); }
    public function transactionType() { return $this->belongsTo(TransactionType::class); }
    public function transaction() { return $this->belongsTo(Transaction::class); }
}
