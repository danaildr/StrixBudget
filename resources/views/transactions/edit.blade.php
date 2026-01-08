<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Transaction') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('transactions.update', $transaction) }}" class="space-y-6" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div>
                            <x-input-label for="executed_at" :value="__('Transaction Date')" />
                            <x-text-input 
                                id="executed_at" 
                                name="executed_at" 
                                type="datetime-local" 
                                class="mt-1 block w-full" 
                                :value="old('executed_at', $transaction->executed_at->format('Y-m-d\TH:i'))" 
                                required 
                            />
                            <x-input-error class="mt-2" :messages="$errors->get('executed_at')" />
                        </div>

                        <div>
                            <x-input-label for="type" :value="__('Transaction Type')" />
                            <select id="type" name="type" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="">Select Type</option>
                                <option value="income" {{ old('type', $transaction->type) == 'income' ? 'selected' : '' }}>Income</option>
                                <option value="expense" {{ old('type', $transaction->type) == 'expense' ? 'selected' : '' }}>Expense</option>
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('type')" />
                        </div>

                        <div>
                            <x-input-label for="bank_account_id" :value="__('Bank Account')" />
                            <select id="bank_account_id" name="bank_account_id" class="search-select mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="">Select Account</option>
                                @foreach($accounts as $account)
                                    <option value="{{ $account->id }}" 
                                        data-currency="{{ $account->currency }}" 
                                        data-balance="{{ $account->balance }}"
                                        {{ old('bank_account_id', $transaction->bank_account_id) == $account->id ? 'selected' : '' }}>
                                        {{ $account->name }} ({{ $account->currency }}){{ $account->iban ? ' - ' . $account->iban : '' }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('bank_account_id')" />
                        </div>

                        <div>
                            <x-input-label for="amount" :value="__('Amount')" />
                            <div class="mt-1 flex rounded-md shadow-sm">
                                <x-text-input
                                    id="amount"
                                    name="amount"
                                    type="text"
                                    pattern="[0-9]+([.,][0-9]{1,2})?"
                                    inputmode="decimal"
                                    :value="old('amount', $transaction->amount)"
                                    class="block w-full rounded-none rounded-l-md"
                                    required
                                    placeholder="0.00"
                                />
                                <span id="currency" class="inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                                    {{ $transaction->currency }}
                                </span>
                            </div>
                            <div id="balance_info" class="mt-2"></div>
                            <x-input-error class="mt-2" :messages="$errors->get('amount')" />
                        </div>

                        <div>
                            <x-input-label for="counterparty_id" :value="__('Counterparty')" />
                            <div class="flex-grow">
                                <select id="counterparty_id" name="counterparty_id" class="search-select mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="">Select Counterparty</option>
                                    @foreach($counterparties as $counterparty)
                                        <option value="{{ $counterparty->id }}" {{ old('counterparty_id', $transaction->counterparty_id) == $counterparty->id ? 'selected' : '' }}>
                                            {{ $counterparty->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <x-input-error class="mt-2" :messages="$errors->get('counterparty_id')" />
                        </div>

                        <div>
                            <x-input-label for="transaction_type_id" :value="__('Category')" />
                            <div class="flex-grow">
                                <select id="transaction_type_id" name="transaction_type_id" class="search-select mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="">Select Category</option>
                                    @foreach($transactionTypes as $type)
                                        <option value="{{ $type->id }}" {{ old('transaction_type_id', $transaction->transaction_type_id) == $type->id ? 'selected' : '' }}>
                                            {{ $type->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <x-input-error class="mt-2" :messages="$errors->get('transaction_type_id')" />
                        </div>

                        <div>
                            <x-input-label for="description" :value="__('Description')" />
                            <textarea id="description" name="description" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="3">{{ old('description', $transaction->description) }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('description')" />
                        </div>

                        <div>
                            <x-input-label for="attachment" :value="__('Attachment')" />
                            <input 
                                type="file" 
                                id="attachment" 
                                name="attachment" 
                                class="mt-1 block w-full text-sm text-gray-500
                                file:mr-4 file:py-2 file:px-4
                                file:rounded-full file:border-0
                                file:text-sm file:font-semibold
                                file:bg-indigo-50 file:text-indigo-700
                                hover:file:bg-indigo-100"
                                accept=".jpg,.jpeg,.png,.pdf"
                            />
                            @if($transaction->attachment_path)
                                <div class="mt-2">
                                    <span class="text-sm text-gray-500">Current attachment: </span>
                                    <a href="{{ Storage::url($transaction->attachment_path) }}" target="_blank" class="text-sm text-blue-600 hover:text-blue-900">
                                        {{ __('View Current File') }}
                                    </a>
                                </div>
                            @endif
                            <x-input-error class="mt-2" :messages="$errors->get('attachment')" />
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Update Transaction') }}</x-primary-button>
                            <a href="{{ route('transactions.show', $transaction) }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                {{ __('Cancel') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Decimal input handling is done by the global DecimalInput component

                const bankAccountSelect = new TomSelect('#bank_account_id', {
                    sortField: {
                        field: "text",
                        direction: "asc"
                    }
                });

                const counterpartySelect = new TomSelect('#counterparty_id', {
                    sortField: {
                        field: "text",
                        direction: "asc"
                    }
                });

                const typeSelect = new TomSelect('#transaction_type_id', {
                    sortField: {
                        field: "text",
                        direction: "asc"
                    }
                });

                const amountInput = document.getElementById('amount');
                const currencySpan = document.getElementById('currency');
                const balanceInfo = document.getElementById('balance_info');
                const typeSelect = document.getElementById('type');

                // Decimal input normalization is handled by the global DecimalInput component

                function validateExpenseAmount() {
                    if (typeSelect.value !== 'expense') {
                        balanceInfo.innerHTML = '';
                        return true;
                    }

                    const selectedOption = bankAccountSelect.options[bankAccountSelect.getValue()];
                    if (!selectedOption) {
                        balanceInfo.innerHTML = '';
                        return true;
                    }

                    const balance = parseFloat(selectedOption.dataset.balance);
                    const amount = parseFloat(amountInput.value) || 0;

                    if (amount > balance) {
                        const formattedAmount = new Intl.NumberFormat('bg-BG', { 
                            minimumFractionDigits: 2, 
                            maximumFractionDigits: 2 
                        }).format(amount);
                        
                        const formattedBalance = new Intl.NumberFormat('bg-BG', { 
                            minimumFractionDigits: 2, 
                            maximumFractionDigits: 2 
                        }).format(balance);

                        balanceInfo.innerHTML = `<p class="text-red-600">Внимание: Наличната сума по сметката (${formattedBalance} ${selectedOption.dataset.currency}) е по-малка от сумата на разхода (${formattedAmount} ${selectedOption.dataset.currency})</p>`;
                        return false;
                    }

                    balanceInfo.innerHTML = `<p class="text-gray-600">Наличност: ${new Intl.NumberFormat('bg-BG', { 
                        minimumFractionDigits: 2, 
                        maximumFractionDigits: 2 
                    }).format(balance)} ${selectedOption.dataset.currency}</p>`;
                    return true;
                }

                // Add event listeners
                bankAccountSelect.on('change', function(value) {
                    if (!value) {
                        currencySpan.textContent = '---';
                        return;
                    }
                    const selectedOption = bankAccountSelect.options[value];
                    currencySpan.textContent = selectedOption.dataset.currency;
                    validateExpenseAmount();
                });

                amountInput.addEventListener('input', validateExpenseAmount);
                typeSelect.addEventListener('change', validateExpenseAmount);

                // Handle form submission
                document.querySelector('form').addEventListener('submit', function(e) {
                    if (!validateExpenseAmount()) {
                        e.preventDefault();
                        return false;
                    }
                });

                // Initial validation
                validateExpenseAmount();
            });
        </script>
    @endpush

    @push('styles')
        <link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
        <style>
            .ts-control {
                border-color: #d1d5db;
                border-radius: 0.375rem;
                min-height: 38px;
                background-color: #ffffff;
            }
            .ts-control:focus {
                border-color: #6366f1;
                box-shadow: 0 0 0 1px #6366f1;
            }
            .ts-dropdown {
                border-color: #d1d5db;
                border-radius: 0.375rem;
            }
        </style>
    @endpush
</x-app-layout> 