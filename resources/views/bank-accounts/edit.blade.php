<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Bank Account') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('bank-accounts.update', $bankAccount) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div>
                            <x-input-label for="name" :value="__('Account Name')" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $bankAccount->name)" required autofocus />
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                        </div>

                        <div>
                            <x-input-label for="currency" :value="__('Currency')" />
                            <select id="currency" name="currency" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="">Select Currency</option>
                                <option value="USD" {{ old('currency', $bankAccount->currency) == 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
                                <option value="EUR" {{ old('currency', $bankAccount->currency) == 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                                <option value="BGN" {{ old('currency', $bankAccount->currency) == 'BGN' ? 'selected' : '' }}>BGN - Bulgarian Lev</option>
                                <option value="GBP" {{ old('currency', $bankAccount->currency) == 'GBP' ? 'selected' : '' }}>GBP - British Pound</option>
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('currency')" />
                        </div>

                        <div>
                            <x-input-label for="iban" :value="__('IBAN')" />
                            <x-text-input id="iban" name="iban" type="text" class="mt-1 block w-full" :value="old('iban', $bankAccount->iban)" placeholder="BG80 BNBG 9661 1020 3456 78" />
                            <x-input-error class="mt-2" :messages="$errors->get('iban')" />
                            <p class="mt-1 text-sm text-gray-500">{{ __('Optional: International Bank Account Number') }}</p>
                        </div>

                        <div class="flex items-center gap-4">
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ old('is_active', $bankAccount->is_active) ? 'checked' : '' }}>
                                <span class="ml-2 text-sm text-gray-600">{{ __('Active') }}</span>
                            </label>

                            <label class="inline-flex items-center">
                                <input type="checkbox" id="is_default" name="is_default" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ old('is_default', $bankAccount->is_default) ? 'checked' : '' }} {{ $bankAccount->is_default ? 'disabled' : '' }}>
                                <span class="ml-2 text-sm text-gray-600">{{ __('Set as Default Account') }}</span>
                            </label>

                            @if($bankAccount->is_default)
                                <div class="mt-2">
                                    <p class="text-sm text-blue-600">
                                        {{ __('This is your default account. To change it, you need to set another account as default first.') }}
                                    </p>
                                </div>
                            @endif
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Update Account') }}</x-primary-button>
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
            const defaultCheckbox = document.getElementById('is_default');
            const activeCheckbox = document.querySelector('input[name="is_active"]');
            const currentIsDefault = {{ $bankAccount->is_default ? 'true' : 'false' }};
            
            if (currentIsDefault) {
                activeCheckbox.addEventListener('change', function() {
                    if (!this.checked) {
                        alert('{{ __("You cannot deactivate the default account. Please set another account as default first.") }}');
                        this.checked = true;
                    }
                });
            }

            defaultCheckbox.addEventListener('change', function() {
                if (this.checked && !currentIsDefault) {
                    if (!confirm('{{ __("This will remove default status from the current default account. Are you sure you want to continue?") }}')) {
                        this.checked = false;
                    }
                } else if (!this.checked && currentIsDefault) {
                    alert('{{ __("You cannot unset the default status directly. Please set another account as default first.") }}');
                    this.checked = true;
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