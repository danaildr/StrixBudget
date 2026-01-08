<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('New Transfer') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Предупредително съобщение -->
                    <div class="mb-6 bg-yellow-50 border-l-4 border-yellow-400 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-yellow-700">
                                    <strong class="font-medium">{{ __('Important') }}:</strong>
                                    {{ __('Please verify all information carefully before submitting. Transfer records cannot be edited or deleted after creation.') }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('transfers.store') }}" class="space-y-6">
                        @csrf

                        <div>
                            <x-input-label for="executed_at" :value="__('Transfer Date')" />
                            <x-text-input 
                                id="executed_at" 
                                name="executed_at" 
                                type="datetime-local" 
                                class="mt-1 block w-full" 
                                :value="old('executed_at', now()->format('Y-m-d\TH:i'))" 
                                required 
                            />
                            <x-input-error class="mt-2" :messages="$errors->get('executed_at')" />
                        </div>

                        <div>
                            <x-input-label for="from_account_id" :value="__('From Account')" />
                            <select id="from_account_id" name="from_account_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="">Select Account</option>
                                @foreach($accounts as $account)
                                    <option value="{{ $account->id }}" data-currency="{{ $account->currency }}" {{ old('from_account_id') == $account->id ? 'selected' : '' }}>
                                        {{ $account->name }} ({{ $account->currency }}){{ $account->iban ? ' - ' . $account->iban : '' }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('from_account_id')" />
                        </div>

                        <div>
                            <x-input-label for="to_account_id" :value="__('To Account')" />
                            <select id="to_account_id" name="to_account_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="">Select Account</option>
                                @foreach($accounts as $account)
                                    <option value="{{ $account->id }}" data-currency="{{ $account->currency }}" {{ old('to_account_id') == $account->id ? 'selected' : '' }}>
                                        {{ $account->name }} ({{ $account->currency }}){{ $account->iban ? ' - ' . $account->iban : '' }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('to_account_id')" />
                        </div>

                        <div>
                            <x-input-label for="amount_from" :value="__('Amount')" />
                            <div class="mt-1 flex rounded-md shadow-sm">
                                <x-text-input
                                    id="amount_from"
                                    name="amount_from"
                                    type="text"
                                    pattern="[0-9]+([.,][0-9]{1,2})?"
                                    inputmode="decimal"
                                    :value="old('amount_from')"
                                    class="block w-full rounded-none rounded-l-md"
                                    required
                                    placeholder="0.00"
                                />
                                <span id="from_currency" class="inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                                    {{ $accounts->first()?->currency ?? '---' }}
                                </span>
                            </div>
                            <div class="mt-2" id="balance_info"></div>
                            <x-input-error class="mt-2" :messages="$errors->get('amount_from')" />
                        </div>

                        <div>
                            <x-input-label for="exchange_rate" :value="__('Exchange Rate')" />
                            <x-text-input
                                id="exchange_rate"
                                name="exchange_rate"
                                type="text"
                                pattern="[0-9]+([.,][0-9]{1,6})?"
                                inputmode="decimal"
                                :value="old('exchange_rate', 1)"
                                class="mt-1 block w-full"
                                required
                                placeholder="1.000000"
                            />
                            <div id="exchange_rate_info" class="mt-2 text-sm text-gray-600"></div>
                            <x-input-error class="mt-2" :messages="$errors->get('exchange_rate')" />
                        </div>

                        <div>
                            <x-input-label for="description" :value="__('Description')" />
                            <textarea id="description" name="description" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="3">{{ old('description') }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('description')" />
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Create Transfer') }}</x-primary-button>
                            <a href="{{ route('transfers.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                {{ __('Cancel') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Decimal input handling is done by the global DecimalInput component

                const fromAccountSelect = document.getElementById('from_account_id');
                const toAccountSelect = document.getElementById('to_account_id');
                const amountInput = document.getElementById('amount_from');
                const exchangeRateInput = document.getElementById('exchange_rate');

                // Decimal input normalization is handled by the global DecimalInput component
                const fromCurrencySpan = document.getElementById('from_currency');
                const balanceInfo = document.getElementById('balance_info');
                const exchangeRateInfo = document.getElementById('exchange_rate_info');
                const submitButton = document.querySelector('button[type="submit"]');

                let isValid = true;

                // Store bank account balances and currencies
                const accounts = {
                    @foreach($accounts as $account)
                        {{ $account->id }}: {
                            balance: {{ $account->balance }},
                            currency: '{{ $account->currency }}'
                        },
                    @endforeach
                };

                // Store exchange rates
                const exchangeRates = {
                    @foreach($accounts->pluck('currency')->unique() as $fromCurrency)
                        @foreach($accounts->pluck('currency')->unique() as $toCurrency)
                            @if($fromCurrency !== $toCurrency)
                                '{{ $fromCurrency }}_{{ $toCurrency }}': {{ $exchangeRateService->getEuroRate($toCurrency) / $exchangeRateService->getEuroRate($fromCurrency) }},
                            @endif
                        @endforeach
                    @endforeach
                };

                function updateExchangeRate() {
                    const fromAccount = accounts[fromAccountSelect.value];
                    const toAccount = accounts[toAccountSelect.value];

                    if (!fromAccount || !toAccount) {
                        exchangeRateInfo.innerHTML = '';
                        return;
                    }

                    if (fromAccount.currency === toAccount.currency) {
                        exchangeRateInput.value = '1.000000';
                        exchangeRateInfo.innerHTML = '';
                        return;
                    }

                    const rateKey = `${fromAccount.currency}_${toAccount.currency}`;
                    const rate = exchangeRates[rateKey];
                    
                    if (rate) {
                        exchangeRateInput.value = rate.toFixed(6);
                        exchangeRateInfo.innerHTML = `Препоръчителен курс: 1 ${fromAccount.currency} = ${rate.toFixed(6)} ${toAccount.currency}`;
                    }
                }

                function validateTransfer() {
                    const selectedFromAccount = fromAccountSelect.value;
                    const amount = parseFloat(amountInput.value) || 0;
                    
                    if (!selectedFromAccount || !accounts[selectedFromAccount]) {
                        balanceInfo.innerHTML = '';
                        fromCurrencySpan.textContent = '---';
                        isValid = true;
                        submitButton.disabled = false;
                        return true;
                    }

                    const accountData = accounts[selectedFromAccount];
                    fromCurrencySpan.textContent = accountData.currency;

                    if (amount > accountData.balance) {
                        const formattedAmount = new Intl.NumberFormat('bg-BG', { 
                            minimumFractionDigits: 2, 
                            maximumFractionDigits: 2 
                        }).format(amount);
                        
                        const formattedBalance = new Intl.NumberFormat('bg-BG', { 
                            minimumFractionDigits: 2, 
                            maximumFractionDigits: 2 
                        }).format(accountData.balance);

                        balanceInfo.innerHTML = `<p class="text-red-600">Внимание: Наличната сума по сметката (${formattedBalance} ${accountData.currency}) е по-малка от сумата на трансфера (${formattedAmount} ${accountData.currency})</p>`;
                        submitButton.disabled = true;
                        isValid = false;
                        return false;
                    }

                    balanceInfo.innerHTML = `<p class="text-gray-600">Наличност: ${new Intl.NumberFormat('bg-BG', { 
                        minimumFractionDigits: 2, 
                        maximumFractionDigits: 2 
                    }).format(accountData.balance)} ${accountData.currency}</p>`;
                    submitButton.disabled = false;
                    isValid = true;
                    return true;
                }

                // Add event listeners
                fromAccountSelect.addEventListener('change', function() {
                    validateTransfer();
                    updateExchangeRate();
                });
                toAccountSelect.addEventListener('change', updateExchangeRate);
                amountInput.addEventListener('input', validateTransfer);

                // Handle form submission
                document.querySelector('form').addEventListener('submit', function(e) {
                    if (!isValid) {
                        e.preventDefault();
                        e.stopPropagation();
                        return false;
                    }
                });

                // Initial validation
                validateTransfer();
                updateExchangeRate();
            });
        </script>
    @endpush
</x-app-layout> 