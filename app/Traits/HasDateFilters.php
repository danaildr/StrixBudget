<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait HasDateFilters
{
    /**
     * Apply date filters to a query
     */
    public function applyDateFilters($query, Request $request, string $dateColumn = 'executed_at')
    {
        if ($request->filled('start_date')) {
            $query->whereDate($dateColumn, '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate($dateColumn, '<=', $request->end_date);
        }

        return $query;
    }

    /**
     * Apply amount range filters to a query
     */
    public function applyAmountFilters($query, Request $request, string $amountColumn = 'amount')
    {
        if ($request->filled('min_amount')) {
            $query->where($amountColumn, '>=', $request->min_amount);
        }

        if ($request->filled('max_amount')) {
            $query->where($amountColumn, '<=', $request->max_amount);
        }

        return $query;
    }

    /**
     * Apply search filters to a query
     */
    public function applySearchFilter($query, Request $request, array $searchColumns)
    {
        if ($request->filled('search')) {
            $search = mb_strtolower('%' . $request->search . '%');
            
            $query->where(function ($q) use ($search, $searchColumns) {
                foreach ($searchColumns as $column) {
                    $q->orWhereRaw('lower(' . $column . ') like ?', [$search]);
                }
            });
        }

        return $query;
    }

    /**
     * Get pagination size from config
     */
    protected function getPaginationSize(string $type = 'default'): int
    {
        return config("strix.pagination.{$type}_per_page", 25);
    }
}
