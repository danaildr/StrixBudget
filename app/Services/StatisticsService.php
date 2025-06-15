<?php

namespace App\Services;

use App\Models\User;
use App\Models\Counterparty;
use App\Models\TransactionType;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class StatisticsService
{
    /**
     * Get counterparty statistics efficiently
     */
    public function getCounterpartyStatistics(Counterparty $counterparty, array $periods = ['total', 'month', 'year']): array
    {
        $cacheKey = "counterparty_stats_{$counterparty->id}_" . implode('_', $periods);
        $cacheTtl = config('strix.cache_ttl.user_stats', 1800);

        return Cache::remember($cacheKey, $cacheTtl, function () use ($counterparty, $periods) {
            $stats = [];
            $now = now();

            foreach ($periods as $period) {
                $query = $counterparty->transactions();

                switch ($period) {
                    case 'month':
                        $query->where('executed_at', '>=', $now->copy()->subMonth());
                        break;
                    case 'year':
                        $query->where('executed_at', '>=', $now->copy()->subYear());
                        break;
                    case 'total':
                    default:
                        // No additional filter for total
                        break;
                }

                $stats[$period] = [
                    'count' => $query->count(),
                    'income' => $query->where('type', 'income')->sum('amount'),
                    'expenses' => $query->where('type', 'expense')->sum('amount'),
                ];
            }

            return $stats;
        });
    }

    /**
     * Get transaction type statistics efficiently
     */
    public function getTransactionTypeStatistics(TransactionType $transactionType, array $periods = ['total', 'month', 'year']): array
    {
        $cacheKey = "transaction_type_stats_{$transactionType->id}_" . implode('_', $periods);
        $cacheTtl = config('strix.cache_ttl.user_stats', 1800);

        return Cache::remember($cacheKey, $cacheTtl, function () use ($transactionType, $periods) {
            $stats = [];
            $now = now();

            foreach ($periods as $period) {
                $query = $transactionType->transactions();

                switch ($period) {
                    case 'month':
                        $query->where('executed_at', '>=', $now->copy()->subMonth());
                        break;
                    case 'year':
                        $query->where('executed_at', '>=', $now->copy()->subYear());
                        break;
                    case 'total':
                    default:
                        // No additional filter for total
                        break;
                }

                $stats[$period] = [
                    'count' => $query->count(),
                    'amount' => $query->sum('amount'),
                ];
            }

            return $stats;
        });
    }

    /**
     * Get user dashboard statistics efficiently
     */
    public function getUserDashboardStatistics(User $user): array
    {
        $cacheKey = "user_dashboard_stats_{$user->id}";
        $cacheTtl = config('strix.cache_ttl.dashboard_data', 900);

        return Cache::remember($cacheKey, $cacheTtl, function () use ($user) {
            return [
                'total_accounts' => $user->bankAccounts()->count(),
                'active_accounts' => $user->bankAccounts()->where('is_active', true)->count(),
                'total_transactions' => $user->transactions()->count(),
                'total_transfers' => $user->transfers()->count(),
                'total_counterparties' => $user->counterparties()->count(),
                'total_transaction_types' => $user->transactionTypes()->count(),
                'recent_transactions' => $user->transactions()
                    ->with(['bankAccount', 'counterparty', 'transactionType'])
                    ->latest('executed_at')
                    ->limit(5)
                    ->get(),
                'recent_transfers' => $user->transfers()
                    ->with(['fromAccount', 'toAccount'])
                    ->latest('executed_at')
                    ->limit(5)
                    ->get(),
            ];
        });
    }

    /**
     * Clear statistics cache for a user
     */
    public function clearUserStatisticsCache(User $user): void
    {
        $patterns = [
            "user_dashboard_stats_{$user->id}",
            "counterparty_stats_*",
            "transaction_type_stats_*",
        ];

        foreach ($patterns as $pattern) {
            if (str_contains($pattern, '*')) {
                // For wildcard patterns, we'd need to implement cache tag clearing
                // For now, just clear the specific user cache
                continue;
            }
            Cache::forget($pattern);
        }
    }

    /**
     * Get admin statistics efficiently
     */
    public function getAdminStatistics(): array
    {
        $cacheKey = 'admin_dashboard_stats';
        $cacheTtl = config('strix.cache_ttl.dashboard_data', 900);

        return Cache::remember($cacheKey, $cacheTtl, function () {
            return [
                'total_users' => DB::table('users')->count(),
                'admin_users' => DB::table('users')->where('role', 'admin')->count(),
                'power_users' => DB::table('users')->where('role', 'power_user')->count(),
                'regular_users' => DB::table('users')->where('role', 'user')->count(),
                'total_registration_keys' => DB::table('registration_keys')->count(),
                'used_registration_keys' => DB::table('registration_keys')->where('is_used', true)->count(),
                'available_registration_keys' => DB::table('registration_keys')->where('is_used', false)->count(),
                'total_transactions' => DB::table('transactions')->count(),
                'total_transfers' => DB::table('transfers')->count(),
                'total_bank_accounts' => DB::table('bank_accounts')->count(),
            ];
        });
    }

    /**
     * Clear admin statistics cache
     */
    public function clearAdminStatisticsCache(): void
    {
        Cache::forget('admin_dashboard_stats');
    }
}
