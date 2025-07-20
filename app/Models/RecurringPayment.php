<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecurringPayment extends Model
{
    protected $fillable = [
        'user_id',
        'bank_account_id',
        'counterparty_id',
        'transaction_type_id',
        'amount',
        'currency',
        'description',
        'repeat_type',
        'repeat_interval',
        'repeat_unit',
        'period_start_day',
        'period_end_day',
        'start_date',
        'end_date',
        'is_active',
        'last_transaction_at',
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function bankAccount() { return $this->belongsTo(BankAccount::class); }
    public function counterparty() { return $this->belongsTo(Counterparty::class); }
    public function transactionType() { return $this->belongsTo(TransactionType::class); }
}
