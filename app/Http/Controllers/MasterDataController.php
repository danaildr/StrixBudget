<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Counterparty;
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
        ];

        return view('master-data.index', compact('stats'));
    }
}
