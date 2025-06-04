<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ __('Transfers Export') }}</title>
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
        <h1>{{ __('Transfers Export') }}</h1>
        <p>{{ __('Generated at') }}: {{ now()->format('Y-m-d H:i:s') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>{{ __('Date') }}</th>
                <th>{{ __('From Account') }}</th>
                <th>{{ __('To Account') }}</th>
                <th class="text-right">{{ __('Amount From') }}</th>
                <th>{{ __('Currency From') }}</th>
                <th class="text-right">{{ __('Amount To') }}</th>
                <th>{{ __('Currency To') }}</th>
                <th class="text-right">{{ __('Exchange Rate') }}</th>
                <th>{{ __('Description') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transfers as $transfer)
                <tr>
                    <td>{{ $transfer->executed_at->format('Y-m-d H:i') }}</td>
                    <td>{{ $transfer->fromAccount->name }}</td>
                    <td>{{ $transfer->toAccount->name }}</td>
                    <td class="text-right">{{ number_format($transfer->amount_from, 2) }}</td>
                    <td>{{ $transfer->currency_from }}</td>
                    <td class="text-right">{{ number_format($transfer->amount_to, 2) }}</td>
                    <td>{{ $transfer->currency_to }}</td>
                    <td class="text-right">{{ number_format($transfer->exchange_rate, 4) }}</td>
                    <td>{{ $transfer->description }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>{{ __('This document was automatically generated from the financial management system.') }}</p>
    </div>
</body>
</html> 