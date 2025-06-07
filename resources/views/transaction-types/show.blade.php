<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Transaction Type Details') }}
            </h2>
            <div class="flex space-x-4">
                <a href="{{ route('transaction-types.edit', $transactionType) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                    {{ __('Edit Type') }}
                </a>
                <a href="{{ route('transaction-types.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
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
                                <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('Basic Information') }}</h3>
                                <div class="bg-gray-50 rounded-lg p-4 space-y-2">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">{{ __('Name') }}:</span>
                                        <span class="text-gray-900">{{ $transactionType->name }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">{{ __('Category') }}:</span>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $transactionType->category === 'income' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ __(ucfirst($transactionType->category)) }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            @if($transactionType->description)
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('Description') }}</h3>
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <p class="text-gray-900 whitespace-pre-wrap">{{ $transactionType->description }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Системна информация и статистика -->
                        <div class="space-y-4">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('System Information') }}</h3>
                                <div class="bg-gray-50 rounded-lg p-4 space-y-2">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">{{ __('Created At') }}:</span>
                                        <span class="text-gray-900">{{ $transactionType->created_at->format('Y-m-d H:i:s') }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">{{ __('Last Updated') }}:</span>
                                        <span class="text-gray-900">{{ $transactionType->updated_at->format('Y-m-d H:i:s') }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Статистики -->
                            <div class="space-y-4">
                                <!-- Общи статистики -->
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('Total Statistics') }}</h3>
                                    <div class="bg-gray-50 rounded-lg p-4 space-y-2">
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">{{ __('Total Transactions') }}:</span>
                                            <span class="text-gray-900">{{ $totalStats['count'] }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">{{ __('Total Amount') }}:</span>
                                            <span class="text-gray-900">
                                                {{ number_format($totalStats['amount'], 2) }} лв.
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Статистики за последната година -->
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('Last Year Statistics') }}</h3>
                                    <div class="bg-blue-50 rounded-lg p-4 space-y-2">
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">{{ __('Transactions') }}:</span>
                                            <span class="text-gray-900">{{ $yearStats['count'] }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">{{ __('Amount') }}:</span>
                                            <span class="text-gray-900">
                                                {{ number_format($yearStats['amount'], 2) }} лв.
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Статистики за последния месец -->
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('Last Month Statistics') }}</h3>
                                    <div class="bg-green-50 rounded-lg p-4 space-y-2">
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">{{ __('Transactions') }}:</span>
                                            <span class="text-gray-900">{{ $monthStats['count'] }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">{{ __('Amount') }}:</span>
                                            <span class="text-gray-900">
                                                {{ number_format($monthStats['amount'], 2) }} лв.
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Списък с транзакции -->
                    <div class="mt-8">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Recent Transactions') }}</h3>

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
                                                            {{ $transaction->counterparty->name ?? __('No counterparty') }}
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
                                                        {{ $transaction->type === 'income' ? '+' : '-' }}{{ number_format($transaction->amount, 2) }} лв.
                                                    </div>
                                                    <div class="ml-4">
                                                        <a href="{{ route('transactions.show', $transaction) }}" class="text-indigo-600 hover:text-indigo-900 text-sm">
                                                            {{ __('View') }}
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>

                            <!-- Пагинация -->
                            <div class="mt-4">
                                {{ $transactions->links() }}
                            </div>
                        @else
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">{{ __('No transactions') }}</h3>
                                <p class="mt-1 text-sm text-gray-500">{{ __('No transactions found for this category.') }}</p>
                            </div>
                        @endif
                    </div>

                    <!-- Бутон за изтриване -->
                    <div class="mt-6 border-t pt-6">
                        <form method="POST" action="{{ route('transaction-types.destroy', $transactionType) }}" class="inline" onsubmit="return confirm('{{ __('Are you sure you want to delete this transaction type? This action cannot be undone.') }}');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">
                                {{ __('Delete Transaction Type') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>