<?php

namespace App\Http\Controllers;

use App\Services\ExchangeRateService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    private $exchangeRateService;

    public function __construct(ExchangeRateService $exchangeRateService)
    {
        $this->middleware('auth');
        $this->exchangeRateService = $exchangeRateService;
    }

    public function index()
    {
        $user = Auth::user();
        $accounts = $user->bankAccounts;

        // Подготвяме данните за графиката
        $chartData = [
            'labels' => [],
            'values' => []
        ];

        foreach ($accounts as $account) {
            $chartData['labels'][] = $account->name;
            $euroValue = $this->exchangeRateService->convertToEuro(
                $account->balance,
                $account->currency
            );
            $chartData['values'][] = round($euroValue, 2);
        }

        Log::info('Chart data:', $chartData);

        return view('dashboard', [
            'chartData' => $chartData,
            'balanceByCurrency' => $accounts->groupBy('currency')
                ->map(function ($accounts) {
                    return $accounts->sum('balance');
                }),
            'exchangeRateService' => $this->exchangeRateService
        ]);
    }
} 