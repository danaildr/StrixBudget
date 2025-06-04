<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Transactions') }}
            </h2>
            @if($hasCounterparties && $hasTransactionTypes)
                <div class="flex space-x-4">
                    <div x-data="{ showExportMenu: false }" class="relative">
                        <button @click="showExportMenu = !showExportMenu" type="button" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50">
                            {{ __('Export') }}
                            <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <div x-show="showExportMenu" @click.away="showExportMenu = false" class="absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 divide-y divide-gray-100">
                            <div class="py-1">
                                <a href="{{ route('transactions.export', ['format' => 'csv']) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">{{ __('CSV') }}</a>
                                <a href="{{ route('transactions.export', ['format' => 'xlsx']) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">{{ __('Excel (XLSX)') }}</a>
                                <a href="{{ route('transactions.export', ['format' => 'ods']) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">{{ __('OpenDocument (ODS)') }}</a>
                                <a href="{{ route('transactions.export', ['format' => 'pdf']) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">{{ __('PDF') }}</a>
                                <a href="{{ route('transactions.export', ['format' => 'json']) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">{{ __('JSON') }}</a>
                                <a href="{{ route('transactions.export', ['format' => 'html']) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">{{ __('HTML') }}</a>
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('transactions.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                        {{ __('New Transaction') }}
                    </a>
                </div>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Бутон за филтри и индикатор за активни филтри -->
                    <div x-data="{ showFilters: false }" class="mb-6">
                        <div class="flex items-center justify-between">
                            <button @click="showFilters = !showFilters" type="button" 
                                class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50">
                                <span x-text="showFilters ? '{{ __('Hide Filters') }}' : '{{ __('Show Filters') }}'"></span>
                                <!-- Индикатор за активни филтри -->
                                @if(request()->anyFilled(['start_date', 'end_date', 'type', 'bank_account_id', 'counterparty_id', 'transaction_type_id']))
                                    <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                        {{ __('Filters active') }}
                                    </span>
                                @endif
                            </button>
                        </div>

                        <!-- Филтри -->
                        <div x-show="showFilters" x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 transform -translate-y-2"
                             x-transition:enter-end="opacity-100 transform translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 transform translate-y-0"
                             x-transition:leave-end="opacity-0 transform -translate-y-2"
                             class="mt-4 bg-gray-50 p-4 rounded-lg">
                            <form method="GET" action="{{ route('transactions.index') }}" class="space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    <!-- Период -->
                                    <div>
                                        <label for="start_date" class="block text-sm font-medium text-gray-700">{{ __('Start Date') }}</label>
                                        <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    </div>
                                    <div>
                                        <label for="end_date" class="block text-sm font-medium text-gray-700">{{ __('End Date') }}</label>
                                        <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    </div>

                                    <!-- Тип транзакция -->
                                    <div>
                                        <label for="type" class="block text-sm font-medium text-gray-700">{{ __('Type') }}</label>
                                        <select name="type" id="type"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                            <option value="">{{ __('All') }}</option>
                                            <option value="income" {{ request('type') === 'income' ? 'selected' : '' }}>{{ __('Income') }}</option>
                                            <option value="expense" {{ request('type') === 'expense' ? 'selected' : '' }}>{{ __('Expense') }}</option>
                                        </select>
                                    </div>

                                    <!-- Банкова сметка -->
                                    <div>
                                        <label for="bank_account_id" class="block text-sm font-medium text-gray-700">{{ __('Bank Account') }}</label>
                                        <select name="bank_account_id" id="bank_account_id"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                            <option value="">{{ __('All Accounts') }}</option>
                                            @foreach($bankAccounts as $account)
                                                <option value="{{ $account->id }}" {{ request('bank_account_id') == $account->id ? 'selected' : '' }}>
                                                    {{ $account->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Контрагент -->
                                    <div>
                                        <label for="counterparty_id" class="block text-sm font-medium text-gray-700">{{ __('Counterparty') }}</label>
                                        <select name="counterparty_id" id="counterparty_id"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                            <option value="">{{ __('All Counterparties') }}</option>
                                            @foreach($counterparties as $counterparty)
                                                <option value="{{ $counterparty->id }}" {{ request('counterparty_id') == $counterparty->id ? 'selected' : '' }}>
                                                    {{ $counterparty->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Категория -->
                                    <div>
                                        <label for="transaction_type_id" class="block text-sm font-medium text-gray-700">{{ __('Category') }}</label>
                                        <select name="transaction_type_id" id="transaction_type_id"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                            <option value="">{{ __('All Categories') }}</option>
                                            @foreach($transactionTypes as $type)
                                                <option value="{{ $type->id }}" {{ request('transaction_type_id') == $type->id ? 'selected' : '' }}>
                                                    {{ $type->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="flex justify-end space-x-3">
                                    <a href="{{ route('transactions.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-200">
                                        {{ __('Reset') }}
                                    </a>
                                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                                        {{ __('Apply Filters') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    @if(!$hasCounterparties || !$hasTransactionTypes)
                        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4">
                            <div class="flex flex-col items-center text-center">
                                <div class="flex items-center mb-4">
                                    <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <p class="text-sm text-yellow-700 mb-6">
                                    @if(!$hasCounterparties && !$hasTransactionTypes)
                                        {{ __('To create transactions, you need to set up both counterparties and transaction types first.') }}
                                    @elseif(!$hasCounterparties)
                                        {{ __('To create transactions, you need to set up counterparties first.') }}
                                    @else
                                        {{ __('To create transactions, you need to set up transaction types first.') }}
                                    @endif
                                </p>
                                <div class="flex flex-wrap justify-center gap-4">
                                    @if(!$hasCounterparties)
                                        <a href="{{ route('counterparties.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                                            {{ __('Create Counterparty') }}
                                        </a>
                                    @endif
                                    @if(!$hasTransactionTypes)
                                        <a href="{{ route('transaction-types.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                                            {{ __('Create Transaction Type') }}
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($transactions->isEmpty())
                        <p class="text-gray-500 text-center py-4">{{ __('No transactions found.') }}</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Date') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Type') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Category') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Account') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Counterparty') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Amount') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Description') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Details') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($transactions as $transaction)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $transaction->executed_at->format('Y-m-d H:i') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $transaction->type === 'income' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                    {{ ucfirst($transaction->type) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $transaction->transactionType->name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $transaction->bankAccount->name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $transaction->counterparty->name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm {{ $transaction->type === 'income' ? 'text-green-600' : 'text-red-600' }}">
                                                {{ $transaction->type === 'income' ? '+' : '-' }}{{ number_format($transaction->amount, 2) }} {{ $transaction->currency }}
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-500">
                                                {{ Str::limit($transaction->description, 50) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                <a href="{{ route('transactions.show', $transaction) }}" class="text-indigo-600 hover:text-indigo-900">{{ __('View Details') }}</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $transactions->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 