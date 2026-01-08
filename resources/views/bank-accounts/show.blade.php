<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Account Details') }}
            </h2>
            <div class="flex space-x-4">
                <a href="{{ route('bank-accounts.edit', $bankAccount) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                    {{ __('Edit Account') }}
                </a>
                @if(!$bankAccount->is_default)
                    <form action="{{ route('bank-accounts.destroy', $bankAccount) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700" onclick="return confirm('{{ __('Are you sure you want to delete this account?') }}')">
                            {{ __('Delete Account') }}
                        </button>
                    </form>
                @endif
                <a href="{{ route('bank-accounts.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    {{ __('Back to List') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if($bankAccount->is_default)
                <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 mb-4" role="alert">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm">
                                {{ __('This is your default account. To delete it or change its status, you must first set another account as default.') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Основна информация -->
                        <div class="space-y-4">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('Basic Information') }}</h3>
                                <div class="bg-gray-50 rounded-lg p-4 space-y-2">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">{{ __('Account Name') }}:</span>
                                        <span class="text-gray-900">{{ $bankAccount->name }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">{{ __('Currency') }}:</span>
                                        <span class="text-gray-900">{{ $bankAccount->currency }}</span>
                                    </div>
                                    @if($bankAccount->iban)
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">{{ __('IBAN') }}:</span>
                                        <span class="text-gray-900 font-mono text-sm">{{ $bankAccount->iban }}</span>
                                    </div>
                                    @endif
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">{{ __('Current Balance') }}:</span>
                                        <span class="font-medium {{ $bankAccount->balance >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                            {{ number_format($bankAccount->balance, 2) }} {{ $bankAccount->currency }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">{{ __('Status') }}:</span>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $bankAccount->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $bankAccount->is_active ? __('Active') : __('Inactive') }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">{{ __('Default Account') }}:</span>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $bankAccount->is_default ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                                            {{ $bankAccount->is_default ? __('Yes') : __('No') }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            @if($bankAccount->description)
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('Description') }}</h3>
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <p class="text-gray-900 whitespace-pre-wrap">{{ $bankAccount->description }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Системна информация -->
                        <div class="space-y-4">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('System Information') }}</h3>
                                <div class="bg-gray-50 rounded-lg p-4 space-y-2">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">{{ __('Created At') }}:</span>
                                        <span class="text-gray-900">{{ $bankAccount->created_at->format('Y-m-d H:i:s') }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">{{ __('Last Updated') }}:</span>
                                        <span class="text-gray-900">{{ $bankAccount->updated_at->format('Y-m-d H:i:s') }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Статистика -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">
                                    {{ __('Statistics') }}
                                    @if($bankAccount->is_default)
                                        <span class="ml-2 px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">{{ __('Default Account') }}</span>
                                    @endif
                                </h3>
                                <div class="bg-gray-50 rounded-lg p-4 space-y-2">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">{{ __('Total Transactions') }}:</span>
                                        <span class="text-gray-900">{{ $bankAccount->transactions_count }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">{{ __('Total Income') }}:</span>
                                        <span class="text-green-600">
                                            +{{ number_format($bankAccount->total_income, 2) }} {{ $bankAccount->currency }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">{{ __('Total Expenses') }}:</span>
                                        <span class="text-red-600">
                                            -{{ number_format($bankAccount->total_expenses, 2) }} {{ $bankAccount->currency }}
                                        </span>
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
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Counterparty') }}</th>
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
                                                        {{ $transaction->counterparty->name }}
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

                    <!-- Списък с трансфери -->
                    <div class="mt-6">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Transfers') }}</h3>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Date') }}</th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('From Account') }}</th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('To Account') }}</th>
                                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Amount') }}</th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Exchange Rate') }}</th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Description') }}</th>
                                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @forelse($transfers as $transfer)
                                                <tr>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                        {{ $transfer->executed_at->format('d.m.Y H:i') }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                        {{ $transfer->fromAccount->name }}
                                                        @if($transfer->from_account_id === $bankAccount->id)
                                                            <span class="ml-1 text-xs text-gray-500">({{ __('This account') }})</span>
                                                        @endif
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                        {{ $transfer->toAccount->name }}
                                                        @if($transfer->to_account_id === $bankAccount->id)
                                                            <span class="ml-1 text-xs text-gray-500">({{ __('This account') }})</span>
                                                        @endif
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right {{ $transfer->from_account_id === $bankAccount->id ? 'text-red-600' : 'text-green-600' }}">
                                                        @if($transfer->from_account_id === $bankAccount->id)
                                                            -{{ number_format($transfer->amount_from, 2) }} {{ $transfer->currency_from }}
                                                        @else
                                                            +{{ number_format($transfer->amount_to, 2) }} {{ $transfer->currency_to }}
                                                        @endif
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                        {{ $transfer->exchange_rate }}
                                                    </td>
                                                    <td class="px-6 py-4 text-sm text-gray-900">
                                                        {{ str($transfer->description)->limit(50) }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                        <a href="{{ route('transfers.show', $transfer) }}" class="text-blue-600 hover:text-blue-900">{{ __('View') }}</a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                                        {{ __('No transfers found.') }}
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                @if($transfers->hasPages())
                                    <div class="mt-4">
                                        {{ $transfers->links() }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 