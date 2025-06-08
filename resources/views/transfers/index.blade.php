<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Transfers') }}
            </h2>
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
                            <a href="{{ route('transfers.export', array_merge(['format' => 'csv'], request()->only(['start_date', 'end_date', 'from_account_id', 'to_account_id', 'min_amount', 'max_amount']))) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">{{ __('CSV') }}</a>
                            <a href="{{ route('transfers.export', array_merge(['format' => 'xlsx'], request()->only(['start_date', 'end_date', 'from_account_id', 'to_account_id', 'min_amount', 'max_amount']))) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">{{ __('Excel (XLSX)') }}</a>
                            <a href="{{ route('transfers.export', array_merge(['format' => 'ods'], request()->only(['start_date', 'end_date', 'from_account_id', 'to_account_id', 'min_amount', 'max_amount']))) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">{{ __('OpenDocument (ODS)') }}</a>
                            <a href="{{ route('transfers.export', array_merge(['format' => 'pdf'], request()->only(['start_date', 'end_date', 'from_account_id', 'to_account_id', 'min_amount', 'max_amount']))) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">{{ __('PDF') }}</a>
                            <a href="{{ route('transfers.export', array_merge(['format' => 'json'], request()->only(['start_date', 'end_date', 'from_account_id', 'to_account_id', 'min_amount', 'max_amount']))) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">{{ __('JSON') }}</a>
                            <a href="{{ route('transfers.export', array_merge(['format' => 'html'], request()->only(['start_date', 'end_date', 'from_account_id', 'to_account_id', 'min_amount', 'max_amount']))) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">{{ __('HTML') }}</a>
                        </div>
                    </div>
                </div>
                <a href="{{ route('transfers.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                    {{ __('New Transfer') }}
                </a>
            </div>
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
                                @if(request()->anyFilled(['start_date', 'end_date', 'from_account_id', 'to_account_id', 'min_amount', 'max_amount']))
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
                            <form method="GET" action="{{ route('transfers.index') }}" class="space-y-4">
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

                                    <!-- Сметка-източник -->
                                    <div>
                                        <label for="from_account_id" class="block text-sm font-medium text-gray-700">{{ __('From Account') }}</label>
                                        <select name="from_account_id" id="from_account_id"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                            <option value="">{{ __('All Accounts') }}</option>
                                            @foreach($accounts as $account)
                                                <option value="{{ $account->id }}" {{ request('from_account_id') == $account->id ? 'selected' : '' }}>
                                                    {{ $account->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Сметка-получател -->
                                    <div>
                                        <label for="to_account_id" class="block text-sm font-medium text-gray-700">{{ __('To Account') }}</label>
                                        <select name="to_account_id" id="to_account_id"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                            <option value="">{{ __('All Accounts') }}</option>
                                            @foreach($accounts as $account)
                                                <option value="{{ $account->id }}" {{ request('to_account_id') == $account->id ? 'selected' : '' }}>
                                                    {{ $account->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Сума от -->
                                    <div>
                                        <label for="min_amount" class="block text-sm font-medium text-gray-700">{{ __('Min Amount') }}</label>
                                        <input type="number" step="0.01" min="0" name="min_amount" id="min_amount" value="{{ request('min_amount') }}"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    </div>

                                    <!-- Сума до -->
                                    <div>
                                        <label for="max_amount" class="block text-sm font-medium text-gray-700">{{ __('Max Amount') }}</label>
                                        <input type="number" step="0.01" min="0" name="max_amount" id="max_amount" value="{{ request('max_amount') }}"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    </div>
                                </div>

                                <div class="flex justify-end space-x-3">
                                    <a href="{{ route('transfers.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-200">
                                        {{ __('Reset') }}
                                    </a>
                                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                                        {{ __('Apply Filters') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    @if($transfers->isEmpty())
                        <p class="text-gray-500 text-center py-4">{{ __('No transfers found.') }}</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Date') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('From Account') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('To Account') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Amount') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Exchange Rate') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Description') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Details') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($transfers as $transfer)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $transfer->executed_at->format('Y-m-d H:i') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $transfer->fromAccount->name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $transfer->toAccount->name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ number_format($transfer->amount_from, 2) }} {{ $transfer->currency_from }}
                                                @if($transfer->currency_from !== $transfer->currency_to)
                                                    → {{ number_format($transfer->amount_to, 2) }} {{ $transfer->currency_to }}
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                @if($transfer->currency_from !== $transfer->currency_to)
                                                    {{ number_format($transfer->exchange_rate, 4) }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-500">
                                                {{ str($transfer->description)->limit(50) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                <a href="{{ route('transfers.show', $transfer) }}" class="text-indigo-600 hover:text-indigo-900">{{ __('View Details') }}</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $transfers->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 