<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Import Data') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <!-- Етап 1: Импортиране на типове транзакции и контрагенти -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Step 1: Import Transaction Types and Counterparties') }}</h3>
                    
                    <div class="space-y-4">
                        <!-- Типове транзакции -->
                        <div class="border rounded-lg p-4">
                            <h4 class="font-medium text-gray-800 mb-2">{{ __('Transaction Types') }}</h4>
                            <div class="flex items-center space-x-4">
                                <div class="flex-grow">
                                    <form action="{{ route('import.transaction-types') }}" method="POST" enctype="multipart/form-data" class="flex items-center space-x-4">
                                        @csrf
                                        <input type="file" name="file" class="flex-grow" accept=".csv,.xlsx,.ods" required>
                                        <x-primary-button type="submit" class="bg-orange-600 hover:bg-orange-700">{{ __('Import') }}</x-primary-button>
                                    </form>
                                </div>
                                <div class="flex space-x-4">
                                    <a href="{{ route('import.template', ['type' => 'transaction-types', 'format' => 'csv']) }}" class="text-sm text-indigo-600 hover:text-indigo-900 border border-indigo-600 rounded px-3 py-1">CSV</a>
                                    <a href="{{ route('import.template', ['type' => 'transaction-types', 'format' => 'xlsx']) }}" class="text-sm text-indigo-600 hover:text-indigo-900 border border-indigo-600 rounded px-3 py-1">XLSX</a>
                                    <a href="{{ route('import.template', ['type' => 'transaction-types', 'format' => 'ods']) }}" class="text-sm text-indigo-600 hover:text-indigo-900 border border-indigo-600 rounded px-3 py-1">ODS</a>
                                </div>
                            </div>
                        </div>

                        <!-- Контрагенти -->
                        <div class="border rounded-lg p-4">
                            <h4 class="font-medium text-gray-800 mb-2">{{ __('Counterparties') }}</h4>
                            <div class="flex items-center space-x-4">
                                <div class="flex-grow">
                                    <form action="{{ route('import.counterparties') }}" method="POST" enctype="multipart/form-data" class="flex items-center space-x-4">
                                        @csrf
                                        <input type="file" name="file" class="flex-grow" accept=".csv,.xlsx,.ods" required>
                                        <x-primary-button type="submit" class="bg-orange-600 hover:bg-orange-700">{{ __('Import') }}</x-primary-button>
                                    </form>
                                </div>
                                <div class="flex space-x-4">
                                    <a href="{{ route('import.template', ['type' => 'counterparties', 'format' => 'csv']) }}" class="text-sm text-indigo-600 hover:text-indigo-900 border border-indigo-600 rounded px-3 py-1">CSV</a>
                                    <a href="{{ route('import.template', ['type' => 'counterparties', 'format' => 'xlsx']) }}" class="text-sm text-indigo-600 hover:text-indigo-900 border border-indigo-600 rounded px-3 py-1">XLSX</a>
                                    <a href="{{ route('import.template', ['type' => 'counterparties', 'format' => 'ods']) }}" class="text-sm text-indigo-600 hover:text-indigo-900 border border-indigo-600 rounded px-3 py-1">ODS</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Етап 2: Импортиране на банкови сметки -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Step 2: Import Bank Accounts') }}</h3>
                    
                    <div class="border rounded-lg p-4">
                        <div class="flex items-center space-x-4">
                            <div class="flex-grow">
                                <form action="{{ route('import.bank-accounts') }}" method="POST" enctype="multipart/form-data" class="flex items-center space-x-4">
                                    @csrf
                                    <input type="file" name="file" class="flex-grow" accept=".csv,.xlsx,.ods" required>
                                    <x-primary-button type="submit" class="bg-orange-600 hover:bg-orange-700">{{ __('Import') }}</x-primary-button>
                                </form>
                            </div>
                            <div class="flex space-x-4">
                                <a href="{{ route('import.template', ['type' => 'bank-accounts', 'format' => 'csv']) }}" class="text-sm text-indigo-600 hover:text-indigo-900 border border-indigo-600 rounded px-3 py-1">CSV</a>
                                <a href="{{ route('import.template', ['type' => 'bank-accounts', 'format' => 'xlsx']) }}" class="text-sm text-indigo-600 hover:text-indigo-900 border border-indigo-600 rounded px-3 py-1">XLSX</a>
                                <a href="{{ route('import.template', ['type' => 'bank-accounts', 'format' => 'ods']) }}" class="text-sm text-indigo-600 hover:text-indigo-900 border border-indigo-600 rounded px-3 py-1">ODS</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Етап 3: Импортиране на транзакции и трансфери -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Step 3: Import Transactions and Transfers') }}</h3>
                    
                    <div class="space-y-4">
                        <!-- Транзакции -->
                        <div class="border rounded-lg p-4">
                            <h4 class="font-medium text-gray-800 mb-2">{{ __('Transactions') }}</h4>
                            <p class="text-sm text-gray-600 mb-3">
                                {{ __('Import transactions with columns: Bank Account, Counterparty, Transaction Type, Type (income/expense), Amount, Description, Date. Templates include example data.') }}
                            </p>
                            <div class="flex items-center space-x-4">
                                <div class="flex-grow">
                                    <form action="{{ route('import.transactions') }}" method="POST" enctype="multipart/form-data" class="flex items-center space-x-4">
                                        @csrf
                                        <input type="file" name="file" class="flex-grow" accept=".csv,.xlsx,.ods" required>
                                        <x-primary-button type="submit" class="bg-orange-600 hover:bg-orange-700">{{ __('Import') }}</x-primary-button>
                                    </form>
                                </div>
                                <div class="flex space-x-4">
                                    <a href="{{ route('import.template', ['type' => 'transactions', 'format' => 'csv']) }}" class="text-sm text-indigo-600 hover:text-indigo-900 border border-indigo-600 rounded px-3 py-1">CSV</a>
                                    <a href="{{ route('import.template', ['type' => 'transactions', 'format' => 'xlsx']) }}" class="text-sm text-indigo-600 hover:text-indigo-900 border border-indigo-600 rounded px-3 py-1">XLSX</a>
                                    <a href="{{ route('import.template', ['type' => 'transactions', 'format' => 'ods']) }}" class="text-sm text-indigo-600 hover:text-indigo-900 border border-indigo-600 rounded px-3 py-1">ODS</a>
                                </div>
                            </div>
                        </div>

                        <!-- Трансфери -->
                        <div class="border rounded-lg p-4">
                            <h4 class="font-medium text-gray-800 mb-2">{{ __('Transfers') }}</h4>
                            <p class="text-sm text-gray-600 mb-3">
                                {{ __('Import transfers with columns: From Account, To Account, Amount, Description, Date, Exchange Rate (optional for different currencies). Templates include example data.') }}
                            </p>
                            <div class="flex items-center space-x-4">
                                <div class="flex-grow">
                                    <form action="{{ route('import.transfers') }}" method="POST" enctype="multipart/form-data" class="flex items-center space-x-4">
                                        @csrf
                                        <input type="file" name="file" class="flex-grow" accept=".csv,.xlsx,.ods" required>
                                        <x-primary-button type="submit" class="bg-orange-600 hover:bg-orange-700">{{ __('Import') }}</x-primary-button>
                                    </form>
                                </div>
                                <div class="flex space-x-4">
                                    <a href="{{ route('import.template', ['type' => 'transfers', 'format' => 'csv']) }}" class="text-sm text-indigo-600 hover:text-indigo-900 border border-indigo-600 rounded px-3 py-1">CSV</a>
                                    <a href="{{ route('import.template', ['type' => 'transfers', 'format' => 'xlsx']) }}" class="text-sm text-indigo-600 hover:text-indigo-900 border border-indigo-600 rounded px-3 py-1">XLSX</a>
                                    <a href="{{ route('import.template', ['type' => 'transfers', 'format' => 'ods']) }}" class="text-sm text-indigo-600 hover:text-indigo-900 border border-indigo-600 rounded px-3 py-1">ODS</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 