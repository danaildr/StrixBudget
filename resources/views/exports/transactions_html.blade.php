<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ __('Transactions Export') }}</title>
    <style>
        body {
            font-family: system-ui, -apple-system, sans-serif;
            line-height: 1.5;
            margin: 2rem;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1rem;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
        }
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        tr:hover {
            background-color: #f2f2f2;
        }
        .text-right {
            text-align: right;
        }
        .header {
            margin-bottom: 2rem;
        }
        .footer {
            margin-top: 2rem;
            font-size: 0.875rem;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ __('Transactions Export') }}</h1>
        <p>{{ __('Generated at') }}: {{ now()->format('Y-m-d H:i:s') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>{{ __('Date') }}</th>
                <th>{{ __('Type') }}</th>
                <th>{{ __('Category') }}</th>
                <th>{{ __('Account') }}</th>
                <th>{{ __('Counterparty') }}</th>
                <th class="text-right">{{ __('Amount') }}</th>
                <th>{{ __('Currency') }}</th>
                <th>{{ __('Description') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $transaction)
                <tr>
                    <td>{{ $transaction->executed_at->format('Y-m-d H:i') }}</td>
                    <td>{{ ucfirst($transaction->type) }}</td>
                    <td>{{ $transaction->transactionType->name }}</td>
                    <td>{{ $transaction->bankAccount->name }}</td>
                    <td>{{ $transaction->counterparty->name }}</td>
                    <td class="text-right">{{ number_format($transaction->amount, 2) }}</td>
                    <td>{{ $transaction->currency }}</td>
                    <td>{{ $transaction->description }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>{{ __('This document was automatically generated from the financial management system.') }}</p>
    </div>
</body>
</html> 