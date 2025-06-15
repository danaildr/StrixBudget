<?php

namespace App\Traits;

trait NormalizesDecimals
{
    /**
     * Normalize decimal separator from comma to dot
     */
    protected function normalizeDecimal($value)
    {
        if (is_string($value)) {
            // Replace comma with dot for decimal separator
            return str_replace(',', '.', $value);
        }
        return $value;
    }

    /**
     * Get common transaction validation rules
     */
    protected function getTransactionValidationRules(): array
    {
        $maxAmount = config('strix.business.max_amount', 999999999.99);
        $allowedMimes = implode(',', config('strix.file_uploads.allowed_mimes', ['jpg', 'jpeg', 'png', 'pdf']));
        $maxFileSize = config('strix.file_uploads.max_size_kb', 5120);

        return [
            'bank_account_id' => ['required', 'exists:bank_accounts,id'],
            'type' => ['required', 'in:' . implode(',', config('strix.business.transaction_types', ['income', 'expense']))],
            'amount' => ['required', 'numeric', 'min:0.01', "max:{$maxAmount}"],
            'description' => ['nullable', 'string', 'max:1000'],
            'executed_at' => ['required', 'date', 'before_or_equal:now'],
            'attachment' => ['nullable', 'file', "mimes:{$allowedMimes}", "max:{$maxFileSize}"],
            'counterparty_id' => ['required', 'exists:counterparties,id'],
            'transaction_type_id' => ['required', 'exists:transaction_types,id'],
        ];
    }

    /**
     * Get common bank account validation rules
     */
    protected function getBankAccountValidationRules(): array
    {
        $supportedCurrencies = implode(',', config('strix.business.supported_currencies', ['EUR', 'USD', 'BGN']));
        $maxAmount = config('strix.business.max_amount', 999999999.99);

        return [
            'name' => ['required', 'string', 'max:255'],
            'currency' => ['required', 'string', 'size:3', "in:{$supportedCurrencies}"],
            'is_active' => ['boolean'],
            'is_default' => ['boolean'],
            'initial_balance' => ['required', 'numeric', 'min:0', "max:{$maxAmount}"]
        ];
    }

    /**
     * Get common transfer validation rules
     */
    protected function getTransferValidationRules(): array
    {
        $maxAmount = config('strix.business.max_amount', 999999999.99);

        return [
            'from_account_id' => ['required', 'exists:bank_accounts,id'],
            'to_account_id' => ['required', 'exists:bank_accounts,id', 'different:from_account_id'],
            'amount_from' => ['required', 'numeric', 'min:0.01', "max:{$maxAmount}"],
            'exchange_rate' => ['required', 'numeric', 'min:0.000001'],
            'description' => ['nullable', 'string', 'max:1000'],
            'executed_at' => ['required', 'date', 'before_or_equal:now']
        ];
    }
}
