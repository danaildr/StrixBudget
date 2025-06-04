<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Transaction Type Details') }}
            </h2>
            <div class="flex space-x-4">
                <a href="{{ route('transaction-types.edit', $transactionType) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                    {{ __('Edit Type') }}
                </a>
                <a href="{{ route('transaction-types.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    {{ __('Back to List') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Основна информация -->
                        <div class="space-y-4">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('Basic Information') }}</h3>
                                <div class="bg-gray-50 rounded-lg p-4 space-y-2">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">{{ __('Name') }}:</span>
                                        <span class="text-gray-900">{{ $transactionType->name }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">{{ __('Category') }}:</span>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $transactionType->category === 'income' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ __(ucfirst($transactionType->category)) }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            @if($transactionType->description)
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('Description') }}</h3>
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <p class="text-gray-900 whitespace-pre-wrap">{{ $transactionType->description }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Системна информация и статистика -->
                        <div class="space-y-4">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('System Information') }}</h3>
                                <div class="bg-gray-50 rounded-lg p-4 space-y-2">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">{{ __('Created At') }}:</span>
                                        <span class="text-gray-900">{{ $transactionType->created_at->format('Y-m-d H:i:s') }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">{{ __('Last Updated') }}:</span>
                                        <span class="text-gray-900">{{ $transactionType->updated_at->format('Y-m-d H:i:s') }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Статистика -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('Usage Statistics') }}</h3>
                                <div class="bg-gray-50 rounded-lg p-4 space-y-2">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">{{ __('Total Transactions') }}:</span>
                                        <span class="text-gray-900">{{ $transactionType->transactions_count }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">{{ __('Total Amount') }}:</span>
                                        <span class="{{ $transactionType->category === 'income' ? 'text-green-600' : 'text-red-600' }}">
                                            {{ number_format($transactionType->total_amount, 2) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Бутон за изтриване -->
                    <div class="mt-6 border-t pt-6">
                        <form method="POST" action="{{ route('transaction-types.destroy', $transactionType) }}" class="inline" onsubmit="return confirm('{{ __('Are you sure you want to delete this transaction type? This action cannot be undone.') }}');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">
                                {{ __('Delete Transaction Type') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 