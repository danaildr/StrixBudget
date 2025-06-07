<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Counterparty Details') }}
            </h2>
            <div class="flex space-x-4">
                <a href="{{ route('counterparties.edit', $counterparty) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                    {{ __('Edit Counterparty') }}
                </a>
                <a href="{{ route('counterparties.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
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
                                        <span class="text-gray-900">{{ $counterparty->name }}</span>
                                    </div>
                                    @if($counterparty->email)
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">{{ __('Email') }}:</span>
                                            <span class="text-gray-900">{{ $counterparty->email }}</span>
                                        </div>
                                    @endif
                                    @if($counterparty->phone)
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">{{ __('Phone') }}:</span>
                                            <span class="text-gray-900">{{ $counterparty->phone }}</span>
                                        </div>
                                    @endif
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">{{ __('Status') }}:</span>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $counterparty->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $counterparty->is_active ? __('Active') : __('Inactive') }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            @if($counterparty->description)
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('Description') }}</h3>
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <p class="text-gray-900 whitespace-pre-wrap">{{ $counterparty->description }}</p>
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
                                        <span class="text-gray-900">{{ $counterparty->created_at->format('Y-m-d H:i:s') }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">{{ __('Last Updated') }}:</span>
                                        <span class="text-gray-900">{{ $counterparty->updated_at->format('Y-m-d H:i:s') }}</span>
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
                                            <span class="text-gray-600">{{ __('Total Income') }}:</span>
                                            <span class="text-green-600">
                                                {{ number_format($totalStats['income'], 2) }} лв.
                                            </span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">{{ __('Total Expenses') }}:</span>
                                            <span class="text-red-600">
                                                {{ number_format($totalStats['expenses'], 2) }} лв.
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
                                            <span class="text-gray-600">{{ __('Income') }}:</span>
                                            <span class="text-green-600">
                                                {{ number_format($yearStats['income'], 2) }} лв.
                                            </span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">{{ __('Expenses') }}:</span>
                                            <span class="text-red-600">
                                                {{ number_format($yearStats['expenses'], 2) }} лв.
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
                                            <span class="text-gray-600">{{ __('Income') }}:</span>
                                            <span class="text-green-600">
                                                {{ number_format($monthStats['income'], 2) }} лв.
                                            </span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">{{ __('Expenses') }}:</span>
                                            <span class="text-red-600">
                                                {{ number_format($monthStats['expenses'], 2) }} лв.
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Списък с транзакции -->
                    <div class="mt-6">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Transactions') }}</h3>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Date') }}</th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Type') }}</th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Category') }}</th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Bank Account') }}</th>
                                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Amount') }}</th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Description') }}</th>
                                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @forelse($transactions as $transaction)
                                                <tr>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                        {{ $transaction->executed_at->format('d.m.Y H:i') }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $transaction->type === 'income' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                            {{ __(ucfirst($transaction->type)) }}
                                                        </span>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                        {{ $transaction->transactionType->name }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                        {{ $transaction->bankAccount->name }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right {{ $transaction->type === 'income' ? 'text-green-600' : 'text-red-600' }}">
                                                        {{ number_format($transaction->amount, 2) }} {{ $transaction->currency }}
                                                    </td>
                                                    <td class="px-6 py-4 text-sm text-gray-900">
                                                        {{ str($transaction->description)->limit(50) }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-3">
                                                        <a href="{{ route('transactions.show', $transaction) }}" class="text-blue-600 hover:text-blue-900">{{ __('View') }}</a>
                                                        <a href="{{ route('transactions.edit', $transaction) }}" class="text-indigo-600 hover:text-indigo-900">{{ __('Edit') }}</a>
                                                        <form action="{{ route('transactions.destroy', $transaction) }}" method="POST" class="inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('{{ __('Are you sure you want to delete this transaction?') }}')">
                                                                {{ __('Delete') }}
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                                        {{ __('No transactions found.') }}
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                @if($transactions->hasPages())
                                    <div class="mt-4">
                                        {{ $transactions->links() }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Бутон за изтриване -->
                    <div class="mt-6 border-t pt-6">
                        <form method="POST" action="{{ route('counterparties.destroy', $counterparty) }}" class="inline" onsubmit="return confirm('{{ __('Are you sure you want to delete this counterparty? This action cannot be undone.') }}');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">
                                {{ __('Delete Counterparty') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 