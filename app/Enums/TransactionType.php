<?php

namespace App\Enums;

enum TransactionType: string
{
    case INCOME = 'income';
    case EXPENSE = 'expense';

    public function label(): string
    {
        return match($this) {
            self::INCOME => 'Income',
            self::EXPENSE => 'Expense',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function isIncome(): bool
    {
        return $this === self::INCOME;
    }

    public function isExpense(): bool
    {
        return $this === self::EXPENSE;
    }
}
