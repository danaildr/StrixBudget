<?php

namespace App\Http\Controllers;

use App\Services\ExchangeRateService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\RecurringPayment;
use App\Models\ScheduledPayment;
use Carbon\Carbon;

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
        $accounts = $user->bankAccounts()->where('is_active', true)->get();

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

        // Извличане на предстоящи повтарящи се и планирани плащания за следващите 7 дни
        $today = Carbon::today();
        $in7days = Carbon::today()->addDays(7);

        // Предстоящи планирани плащания
        $upcomingScheduled = ScheduledPayment::where('user_id', $user->id)
            ->where('is_active', true)
            ->whereDate('scheduled_date', '>=', $today)
            ->whereDate('scheduled_date', '<=', $in7days)
            ->get();

        // Предстоящи повтарящи се плащания (груба логика: ако периодът включва някой от следващите 7 дни)
        $upcomingRecurring = RecurringPayment::where('user_id', $user->id)
            ->where('is_active', true)
            ->where(function($q) use ($today, $in7days) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', $today);
            })
            ->get()
            ->filter(function($payment) use ($today, $in7days) {
                // Проверка дали има падеж в следващите 7 дни
                $dates = [];
                $current = Carbon::parse($payment->start_date);
                $end = $payment->end_date ? Carbon::parse($payment->end_date) : $in7days;
                if ($end->lt($today)) return false;
                while ($current->lte($in7days) && $current->lte($end)) {
                    if ($current->gte($today) && $current->lte($in7days)) {
                        // Ако има период в месеца, проверяваме дали денят е в този диапазон
                        if ($payment->period_start_day && $payment->period_end_day) {
                            $day = $current->day;
                            if ($day >= $payment->period_start_day && $day <= $payment->period_end_day) {
                                return true;
                            }
                        } else {
                            return true;
                        }
                    }
                    // Изчисляваме следващата дата според repeat_type
                    switch ($payment->repeat_type) {
                        case 'daily': $current->addDay(); break;
                        case 'weekly': $current->addWeek(); break;
                        case 'monthly': $current->addMonth(); break;
                        case 'yearly': $current->addYear(); break;
                        case 'custom':
                            $unit = $payment->repeat_unit ?? 'days';
                            $interval = $payment->repeat_interval ?? 1;
                            $current->add($unit, $interval); break;
                        default: $current->addDay(); break;
                    }
                }
                return false;
            });

        return view('dashboard', [
            'accounts' => $accounts,
            'chartData' => $chartData,
            'balanceByCurrency' => $accounts->groupBy('currency')
                ->map(function ($accounts) {
                    return $accounts->sum('balance');
                }),
            'exchangeRateService' => $this->exchangeRateService,
            'upcomingScheduled' => $upcomingScheduled,
            'upcomingRecurring' => $upcomingRecurring,
        ]);
    }
} 