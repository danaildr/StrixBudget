<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Recurring Payment Details') }}
            </h2>
            <div class="flex space-x-4">
                <a href="{{ route('recurring-payments.edit', $payment) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                    {{ __('Edit Recurring Payment') }}
                </a>
                <a href="{{ route('recurring-payments.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    {{ __('Back to List') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Основна информация -->
                        <div class="space-y-4">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">
                                    {{ __('Main Information') }}
                                </h3>
                                <div class="bg-gray-50 rounded-lg p-4 space-y-2">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">
                                            {{ __('Account') }}:
                                        </span>
                                        <span class="text-gray-900">
                                            {{ $payment->bankAccount->name ?? __('N/A') }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">
                                            {{ __('Counterparty') }}:
                                        </span>
                                        <span class="text-gray-900">
                                            {{ $payment->counterparty->name ?? __('N/A') }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">
                                            {{ __('Category') }}:
                                        </span>
                                        <span class="text-gray-900">
                                            {{ $payment->transactionType->name ?? __('N/A') }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">
                                            {{ __('Amount') }}:
                                        </span>
                                        <span class="text-gray-900">
                                            {{ number_format($payment->amount, 2) }} {{ $payment->currency }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">
                                            {{ __('Repeat Type') }}:
                                        </span>
                                        <span class="text-gray-900">
                                            {{ $payment->repeat_type }}
                                            @if($payment->repeat_type === 'custom')
                                                ({{ $payment->repeat_interval }} {{ $payment->repeat_unit }})
                                            @endif
                                        </span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">
                                            {{ __('Period in Month') }}:
                                        </span>
                                        <span class="text-gray-900">
                                            @if($payment->period_start_day && $payment->period_end_day)
                                                {{ __('From') }} {{ $payment->period_start_day }} {{ __('to') }} {{ $payment->period_end_day }}
                                            @else
                                                {{ __('N/A') }}
                                            @endif
                                        </span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">
                                            {{ __('Active') }}:
                                        </span>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $payment->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $payment->is_active ? __('Yes') : __('No') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            @if($payment->description)
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">
                                        {{ __('Description') }}
                                    </h3>
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <p class="text-gray-900 whitespace-pre-wrap">
                                            {{ $payment->description }}
                                        </p>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <!-- Системна информация и статистики -->
                        <div class="space-y-4">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">
                                    {{ __('System Information') }}
                                </h3>
                                <div class="bg-gray-50 rounded-lg p-4 space-y-2">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">
                                            {{ __('Start Date') }}:
                                        </span>
                                        <span class="text-gray-900">
                                            {{ $payment->start_date }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">
                                            {{ __('End Date') }}:
                                        </span>
                                        <span class="text-gray-900">
                                            {{ $payment->end_date ?? __('N/A') }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">
                                            {{ __('Created At') }}:
                                        </span>
                                        <span class="text-gray-900">
                                            {{ $payment->created_at->format('Y-m-d H:i:s') }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">
                                            {{ __('Last Updated') }}:
                                        </span>
                                        <span class="text-gray-900">
                                            {{ $payment->updated_at->format('Y-m-d H:i:s') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">
                                    {{ __('General Statistics') }}
                                </h3>
                                <div class="bg-gray-50 rounded-lg p-4 space-y-2">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">
                                            {{ __('Number of Transactions') }}:
                                        </span>
                                        <span class="text-gray-900">
                                            {{ $totalStats['count'] }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">
                                            {{ __('Total Income') }}:
                                        </span>
                                        <span class="text-green-600">
                                            {{ number_format($totalStats['income'], 2) }} {{ $payment->currency }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">
                                            {{ __('Total Expenses') }}:
                                        </span>
                                        <span class="text-red-600">
                                            {{ number_format($totalStats['expenses'], 2) }} {{ $payment->currency }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">
                                    {{ __('Statistics for This Year') }}
                                </h3>
                                <div class="bg-blue-50 rounded-lg p-4 space-y-2">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">
                                            {{ __('Transactions') }}:
                                        </span>
                                        <span class="text-gray-900">
                                            {{ $yearStats['count'] }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">
                                            {{ __('Income') }}:
                                        </span>
                                        <span class="text-green-600">
                                            {{ number_format($yearStats['income'], 2) }} {{ $payment->currency }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">
                                            {{ __('Expenses') }}:
                                        </span>
                                        <span class="text-red-600">
                                            {{ number_format($yearStats['expenses'], 2) }} {{ $payment->currency }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">
                                    {{ __('Statistics for This Month') }}
                                </h3>
                                <div class="bg-green-50 rounded-lg p-4 space-y-2">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">
                                            {{ __('Transactions') }}:
                                        </span>
                                        <span class="text-gray-900">
                                            {{ $monthStats['count'] }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">
                                            {{ __('Income') }}:
                                        </span>
                                        <span class="text-green-600">
                                            {{ number_format($monthStats['income'], 2) }} {{ $payment->currency }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">
                                            {{ __('Expenses') }}:
                                        </span>
                                        <span class="text-red-600">
                                            {{ number_format($monthStats['expenses'], 2) }} {{ $payment->currency }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Списък с транзакции -->
                    <div class="mt-8">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">
                            {{ __('Latest Transactions for This Template') }}
                        </h3>
                        @if($transactions->count() > 0)
                            <div class="bg-white shadow overflow-hidden sm:rounded-md">
                                <ul class="divide-y divide-gray-200">
                                    @foreach($transactions as $transaction)
                                        <li>
                                            <div class="px-4 py-4 flex items-center justify-between">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0">
                                                        <div class="h-10 w-10 rounded-full {{ $transaction->type === 'income' ? 'bg-green-100' : 'bg-red-100' }} flex items-center justify-center">
                                                            @if($transaction->type === 'income')
                                                                <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                                                </svg>
                                                            @else
                                                                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                                                </svg>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium text-gray-900">
                                                            {{ $transaction->counterparty->name ?? __('No Counterparty') }}
                                                        </div>
                                                        <div class="text-sm text-gray-500">
                                                            {{ $transaction->bankAccount->name }} • {{ $transaction->executed_at->format('d.m.Y H:i') }}
                                                        </div>
                                                        @if($transaction->description)
                                                            <div class="text-sm text-gray-500 mt-1">
                                                                {{ str($transaction->description)->limit(50) }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="flex items-center">
                                                    <div class="text-sm font-medium {{ $transaction->type === 'income' ? 'text-green-600' : 'text-red-600' }}">
                                                        {{ $transaction->type === 'income' ? '+' : '-' }}{{ number_format($transaction->amount, 2) }} {{ $transaction->currency }}
                                                    </div>
                                                    <div class="ml-4">
                                                        <a href="{{ route('transactions.show', $transaction) }}" class="text-indigo-600 hover:text-indigo-900 text-sm">
                                                            {{ __('Details') }}
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            <div class="mt-4">
                                {{ $transactions->links() }}
                            </div>
                        @else
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">
                                    {{ __('No Transactions') }}
                                </h3>
                                <p class="mt-1 text-sm text-gray-500">
                                    {{ __('No transactions for this recurring payment.') }}
                                </p>
                            </div>
                        @endif
                    </div>

                    <!-- Бутон за изтриване -->
                    <div class="mt-6 border-t pt-6 flex gap-2">
                        <a href="{{ route('recurring-payments.make-transaction', $payment) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                            {{ __('Make Transaction') }}
                        </a>
                        <form method="POST" action="{{ route('recurring-payments.destroy', $payment) }}" class="inline" onsubmit="return confirm('{{ __('Are you sure you want to delete this recurring payment? This action is irreversible.') }}');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">
                                {{ __('Delete') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 