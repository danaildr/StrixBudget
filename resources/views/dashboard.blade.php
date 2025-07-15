<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Dashboard') }}
            </h2>
            <div class="flex space-x-3">
                <a href="{{ route('transactions.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    {{ __('New Transaction') }}
                </a>
                <a href="{{ route('transfers.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                    </svg>
                    {{ __('New Transfer') }}
                </a>
            </div>
        </div>
    </x-slot>

    @push('styles')
    @vite(['resources/css/chart.css'])
    @endpush

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const chartData = @json($chartData);
            initBalanceChart(chartData);
        });
    </script>
    @endpush

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(Auth::user()->bankAccounts->isEmpty())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('No Bank Accounts') }}</h3>
                        <p class="text-gray-600 mb-4">{{ __('You have not added any bank accounts yet. Add your first account to start tracking your finances.') }}</p>
                        <a href="{{ route('bank-accounts.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Add Bank Account') }}
                        </a>
                    </div>
                </div>
            @else
                <!-- Bank Accounts Overview -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">{{ __('Bank Accounts Overview') }}</h3>
                            <a href="{{ route('bank-accounts.create') }}" class="inline-flex items-center px-3 py-1 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('Add Account') }}
                            </a>
                        </div>
                        
                        <div class="mb-6 bg-gray-50 p-4 rounded-lg">
                            <h4 class="text-sm font-medium text-gray-700 mb-3">{{ __('Total Available by Currency') }}</h4>
                            <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
                                <div class="lg:col-span-1">
                                    <div class="grid grid-cols-1 gap-4">
                                        @foreach($balanceByCurrency as $currency => $total)
                                            <div class="bg-white p-3 rounded-lg shadow-sm">
                                                <div class="text-sm text-gray-500">{{ $currency }}</div>
                                                <div class="text-xl font-bold {{ $total < 0 ? 'text-red-600' : 'text-gray-900' }}">
                                                    {{ number_format($total, 2) }}
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="lg:col-span-3">
                                    <div class="bg-white p-4 rounded-lg shadow-sm chart-container">
                                        <canvas id="balanceChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Individual Accounts -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach(Auth::user()->bankAccounts as $account)
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h4 class="font-semibold text-gray-900">{{ $account->name }}</h4>
                                            <p class="text-sm text-gray-500">{{ $account->currency }}</p>
                                        </div>
                                        <span class="px-2 py-1 text-xs rounded-full {{ $account->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $account->is_active ? __('Active') : __('Inactive') }}
                                        </span>
                                    </div>
                                    <div class="mt-2">
                                        <span class="text-xl font-bold {{ $account->balance < 0 ? 'text-red-600' : 'text-gray-900' }}">
                                            {{ number_format($account->balance, 2) }} {{ $account->currency }}
                                            @if($account->currency !== 'EUR')
                                                <span class="text-sm text-gray-500">
                                                    / {{ number_format($exchangeRateService->convertToEuro($account->balance, $account->currency), 2) }} EUR
                                                </span>
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Current Month Statistics -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Current Month Overview') }}</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            @php
                                $currentMonth = now()->month;
                                $currentYear = now()->year;
                                
                                $monthlyIncome = Auth::user()->transactions()
                                    ->where('type', 'income')
                                    ->whereMonth('executed_at', $currentMonth)
                                    ->whereYear('executed_at', $currentYear)
                                    ->sum('amount');
                                    
                                $monthlyExpense = Auth::user()->transactions()
                                    ->where('type', 'expense')
                                    ->whereMonth('executed_at', $currentMonth)
                                    ->whereYear('executed_at', $currentYear)
                                    ->sum('amount');
                                    
                                $monthlyBalance = $monthlyIncome - $monthlyExpense;
                            @endphp
                            
                            <div class="bg-green-50 p-4 rounded-lg">
                                <h4 class="text-sm font-medium text-green-800">{{ __('Income') }}</h4>
                                <p class="text-2xl font-bold text-green-600">{{ number_format($monthlyIncome, 2) }}</p>
                            </div>
                            
                            <div class="bg-red-50 p-4 rounded-lg">
                                <h4 class="text-sm font-medium text-red-800">{{ __('Expenses') }}</h4>
                                <p class="text-2xl font-bold text-red-600">{{ number_format($monthlyExpense, 2) }}</p>
                            </div>
                            
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h4 class="text-sm font-medium text-gray-800">{{ __('Balance') }}</h4>
                                <p class="text-2xl font-bold {{ $monthlyBalance < 0 ? 'text-red-600' : 'text-gray-900' }}">
                                    {{ number_format($monthlyBalance, 2) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Year to Date Statistics -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Year to Date Overview') }}</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            @php
                                $yearlyIncome = Auth::user()->transactions()
                                    ->where('type', 'income')
                                    ->whereYear('executed_at', $currentYear)
                                    ->sum('amount');
                                    
                                $yearlyExpense = Auth::user()->transactions()
                                    ->where('type', 'expense')
                                    ->whereYear('executed_at', $currentYear)
                                    ->sum('amount');
                                    
                                $yearlyBalance = $yearlyIncome - $yearlyExpense;
                            @endphp
                            
                            <div class="bg-green-50 p-4 rounded-lg">
                                <h4 class="text-sm font-medium text-green-800">{{ __('Income') }}</h4>
                                <p class="text-2xl font-bold text-green-600">{{ number_format($yearlyIncome, 2) }}</p>
                            </div>
                            
                            <div class="bg-red-50 p-4 rounded-lg">
                                <h4 class="text-sm font-medium text-red-800">{{ __('Expenses') }}</h4>
                                <p class="text-2xl font-bold text-red-600">{{ number_format($yearlyExpense, 2) }}</p>
                            </div>
                            
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h4 class="text-sm font-medium text-gray-800">{{ __('Balance') }}</h4>
                                <p class="text-2xl font-bold {{ $yearlyBalance < 0 ? 'text-red-600' : 'text-gray-900' }}">
                                    {{ number_format($yearlyBalance, 2) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
