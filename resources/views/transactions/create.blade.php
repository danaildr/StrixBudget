<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('New Transaction') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('transactions.store') }}" class="space-y-6" enctype="multipart/form-data">
                        @csrf

                        <div>
                            <x-input-label for="executed_at" :value="__('Transaction Date')" />
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
                            <x-input-label for="type" :value="__('Transaction Type')" />
                            <select id="type" name="type" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="">Select Type</option>
                                <option value="income" {{ old('type') == 'income' ? 'selected' : '' }}>Income</option>
                                <option value="expense" {{ old('type') == 'expense' ? 'selected' : '' }}>Expense</option>
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
                                        {{ old('bank_account_id') == $account->id ? 'selected' : '' }}>
                                        {{ $account->name }} ({{ $account->currency }})
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('bank_account_id')" />
                        </div>

                        <div>
                            <x-input-label for="amount" :value="__('Amount')" />
                            <div class="mt-1 flex rounded-md shadow-sm">
                                <x-text-input id="amount" name="amount" type="number" step="0.01" min="0.01" class="block w-full rounded-none rounded-l-md" :value="old('amount')" required />
                                <span id="currency" class="inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                                    ---
                                </span>
                            </div>
                            <x-input-error class="mt-2" :messages="$errors->get('amount')" />
                        </div>

                        <div>
                            <x-input-label for="counterparty_id" :value="__('Counterparty')" />
                            <div class="flex-grow">
                                <select id="counterparty_id" name="counterparty_id" class="search-select mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="">Select Counterparty</option>
                                    @foreach($counterparties as $counterparty)
                                        <option value="{{ $counterparty->id }}" {{ old('counterparty_id') == $counterparty->id ? 'selected' : '' }}>
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
                                        <option value="{{ $type->id }}" {{ old('transaction_type_id') == $type->id ? 'selected' : '' }}>
                                            {{ $type->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <x-input-error class="mt-2" :messages="$errors->get('transaction_type_id')" />
                        </div>

                        <div>
                            <x-input-label for="description" :value="__('Description')" />
                            <textarea id="description" name="description" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="3">{{ old('description') }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('description')" />
                        </div>

                        <div>
                            <x-input-label for="attachment" :value="__('Attachment')" />
                            <input type="file" id="attachment" name="attachment" class="mt-1 block w-full" accept=".jpg,.jpeg,.png,.pdf">
                            <p class="mt-1 text-sm text-gray-500">Accepted file types: JPG, JPEG, PNG, PDF (max 5MB)</p>
                            <x-input-error class="mt-2" :messages="$errors->get('attachment')" />
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Create Transaction') }}</x-primary-button>
                            <a href="{{ route('transactions.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                {{ __('Cancel') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const commonConfig = {
                    create: false,
                    sortField: {
                        field: "text",
                        direction: "asc"
                    },
                    maxItems: 1
                };

                const typeSelect = document.getElementById('type');
                const amountInput = document.getElementById('amount');
                let lastValidAmount = amountInput.value;

                // Initialize Tom Select for bank account
                const bankAccountSelect = new TomSelect('#bank_account_id', {
                    ...commonConfig,
                    onChange: function(value) {
                        const selectedOption = this.options[value];
                        const currencySpan = document.getElementById('currency');
                        if (selectedOption) {
                            currencySpan.textContent = selectedOption.dataset.currency;
                            validateExpenseAmount();
                        } else {
                            currencySpan.textContent = '---';
                        }
                    }
                });

                // Initialize Tom Select for counterparty
                new TomSelect('#counterparty_id', commonConfig);

                // Initialize Tom Select for transaction type
                new TomSelect('#transaction_type_id', commonConfig);

                // Set initial currency value
                const currencySpan = document.getElementById('currency');
                const selectedOption = bankAccountSelect.options[bankAccountSelect.getValue()];
                if (selectedOption) {
                    currencySpan.textContent = selectedOption.dataset.currency;
                }

                // Add event listeners for validation
                typeSelect.addEventListener('change', validateExpenseAmount);
                amountInput.addEventListener('input', validateExpenseAmount);
                bankAccountSelect.on('change', validateExpenseAmount);

                // Store form validity state
                let isFormValid = true;

                function validateExpenseAmount() {
                    const selectedOption = bankAccountSelect.options[bankAccountSelect.getValue()];
                    const submitButton = document.querySelector('button[type="submit"]');
                    const amountContainer = document.querySelector('#amount').parentElement;
                    
                    if (!selectedOption || typeSelect.value !== 'expense') {
                        submitButton.disabled = false;
                        amountContainer.classList.remove('ring-2', 'ring-red-500');
                        isFormValid = true;
                        return true;
                    }

                    const balance = parseFloat(selectedOption.dataset.balance);
                    const amount = parseFloat(amountInput.value) || 0;

                    if (amount > balance) {
                        const formattedBalance = new Intl.NumberFormat('bg-BG', { 
                            minimumFractionDigits: 2, 
                            maximumFractionDigits: 2 
                        }).format(balance);
                        
                        alert(`Внимание: Наличната сума по сметката (${formattedBalance} ${selectedOption.dataset.currency}) е по-малка от сумата на транзакцията (${amount} ${selectedOption.dataset.currency})`);
                        
                        submitButton.disabled = true;
                        amountContainer.classList.add('ring-2', 'ring-red-500');
                        isFormValid = false;
                        return false;
                    }
                    
                    submitButton.disabled = false;
                    amountContainer.classList.remove('ring-2', 'ring-red-500');
                    isFormValid = true;
                    return true;
                }

                // Handle form submission
                document.querySelector('form').addEventListener('submit', function(e) {
                    // Validate again before submitting
                    validateExpenseAmount();
                    
                    if (!isFormValid) {
                        e.preventDefault();
                        e.stopPropagation();
                        return false;
                    }
                });
            });
        </script>
    @endpush
</x-app-layout> 