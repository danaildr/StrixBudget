<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Counterparty;
use App\Models\RecurringPayment;
use App\Models\ScheduledPayment;
use App\Models\TransactionType;
use Illuminate\Support\Facades\Auth;

class MasterDataController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display the master data dashboard
     */
    public function index()
    {
        $user = Auth::user();

        $stats = [
            'counterparties_count' => Counterparty::where('user_id', $user->id)->count(),
            'categories_count' => TransactionType::where('user_id', $user->id)->count(),
            'accounts_count' => BankAccount::where('user_id', $user->id)->count(),
            'recurring_payments_count' => RecurringPayment::where('user_id', $user->id)->count(),
            'recurring_payments_active' => RecurringPayment::where('user_id', $user->id)->where('is_active', true)->count(),
            'recurring_payments_inactive' => RecurringPayment::where('user_id', $user->id)->where('is_active', false)->count(),
            'scheduled_payments_count' => ScheduledPayment::where('user_id', $user->id)->count(),
            'scheduled_payments_active' => ScheduledPayment::where('user_id', $user->id)->where('is_active', true)->count(),
            'scheduled_payments_inactive' => ScheduledPayment::where('user_id', $user->id)->where('is_active', false)->count(),
        ];

        return view('master-data.index', compact('stats'));
    }
}
