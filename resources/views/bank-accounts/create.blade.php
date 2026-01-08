<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Bank Account') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('bank-accounts.store') }}" class="space-y-6">
                        @csrf

                        <div>
                            <x-input-label for="name" :value="__('Account Name')" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required autofocus />
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                        </div>

                        <div>
                            <x-input-label for="currency" :value="__('Currency')" />
                            <select id="currency" name="currency" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="">Select Currency</option>
                                <option value="USD" {{ old('currency') == 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
                                <option value="EUR" {{ old('currency') == 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                                <option value="BGN" {{ old('currency') == 'BGN' ? 'selected' : '' }}>BGN - Bulgarian Lev</option>
                                <option value="GBP" {{ old('currency') == 'GBP' ? 'selected' : '' }}>GBP - British Pound</option>
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('currency')" />
                        </div>

                        <div>
                            <x-input-label for="iban" :value="__('IBAN')" />
                            <x-text-input id="iban" name="iban" type="text" class="mt-1 block w-full" :value="old('iban')" placeholder="BG80 BNBG 9661 1020 3456 78" />
                            <x-input-error class="mt-2" :messages="$errors->get('iban')" />
                            <p class="mt-1 text-sm text-gray-500">{{ __('Optional: International Bank Account Number') }}</p>
                        </div>

                        <div>
                            <x-input-label for="initial_balance" :value="__('Initial Balance')" />
                            <div class="mt-1 flex rounded-md shadow-sm">
                                <x-text-input
                                    id="initial_balance"
                                    name="initial_balance"
                                    type="text"
                                    pattern="[0-9]+([.,][0-9]{1,2})?"
                                    inputmode="decimal"
                                    class="block w-full rounded-none rounded-l-md"
                                    :value="old('initial_balance', '0.00')"
                                    required
                                    placeholder="0.00"
                                />
                                <span id="selected_currency" class="inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                                    ---
                                </span>
                            </div>
                            <x-input-error class="mt-2" :messages="$errors->get('initial_balance')" />
                        </div>

                        <div class="flex items-center gap-4">
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ old('is_active', true) ? 'checked' : '' }}>
                                <span class="ml-2 text-sm text-gray-600">{{ __('Active') }}</span>
                            </label>

                            <label class="inline-flex items-center">
                                <input type="checkbox" id="is_default" name="is_default" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ old('is_default') ? 'checked' : '' }}>
                                <span class="ml-2 text-sm text-gray-600">{{ __('Set as Default Account') }}</span>
                            </label>
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Create Account') }}</x-primary-button>
                            <a href="{{ route('bank-accounts.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
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
            const currencySelect = document.getElementById('currency');
            const selectedCurrencySpan = document.getElementById('selected_currency');
            
            function updateSelectedCurrency() {
                selectedCurrencySpan.textContent = currencySelect.value || '---';
            }
            
            currencySelect.addEventListener('change', updateSelectedCurrency);
            updateSelectedCurrency();

            const defaultCheckbox = document.getElementById('is_default');
            
            defaultCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    if (!confirm('{{ __("Setting this account as default will remove default status from any current default account. Do you want to continue?") }}')) {
                        this.checked = false;
                    }
                }
            });

            // IBAN validation
            const ibanInput = document.getElementById('iban');
            if (ibanInput) {
                ibanInput.addEventListener('input', function() {
                    let value = this.value.toUpperCase().replace(/\s/g, '');
                    this.value = value;
                });

                ibanInput.addEventListener('blur', function() {
                    if (this.value && !validateIBAN(this.value)) {
                        this.setCustomValidity('{{ __("Invalid IBAN format") }}');
                    } else {
                        this.setCustomValidity('');
                    }
                });
            }

            function validateIBAN(iban) {
                // Basic IBAN validation regex
                const ibanRegex = /^[A-Z]{2}[0-9]{2}[A-Z0-9]{1,30}$/;
                return ibanRegex.test(iban);
            }
        });
    </script>
    @endpush
</x-app-layout> 