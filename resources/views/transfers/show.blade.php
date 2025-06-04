<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Transfer Details') }}
            </h2>
            <div>
                <a href="{{ route('transfers.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
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
                                        <span class="text-gray-600">{{ __('Date') }}:</span>
                                        <span class="text-gray-900">{{ $transfer->executed_at->format('Y-m-d H:i') }}</span>
                                    </div>
                                    
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">{{ __('Amount From') }}:</span>
                                        <span class="text-red-600">
                                            -{{ number_format($transfer->amount_from, 2) }} {{ $transfer->currency_from }}
                                        </span>
                                    </div>

                                    <div class="flex justify-between">
                                        <span class="text-gray-600">{{ __('Amount To') }}:</span>
                                        <span class="text-green-600">
                                            +{{ number_format($transfer->amount_to, 2) }} {{ $transfer->currency_to }}
                                        </span>
                                    </div>

                                    @if($transfer->currency_from !== $transfer->currency_to)
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">{{ __('Exchange Rate') }}:</span>
                                            <span class="text-gray-900">{{ number_format($transfer->exchange_rate, 4) }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('Source Account') }}</h3>
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <div class="flex justify-between mb-2">
                                        <span class="text-gray-600">{{ __('Account') }}:</span>
                                        <span class="text-gray-900">{{ $transfer->fromAccount->name }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">{{ __('Currency') }}:</span>
                                        <span class="text-gray-900">{{ $transfer->currency_from }}</span>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('Destination Account') }}</h3>
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <div class="flex justify-between mb-2">
                                        <span class="text-gray-600">{{ __('Account') }}:</span>
                                        <span class="text-gray-900">{{ $transfer->toAccount->name }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">{{ __('Currency') }}:</span>
                                        <span class="text-gray-900">{{ $transfer->currency_to }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Допълнителна информация -->
                        <div class="space-y-4">
                            @if($transfer->description)
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('Description') }}</h3>
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <p class="text-gray-900 whitespace-pre-wrap">{{ $transfer->description }}</p>
                                    </div>
                                </div>
                            @endif

                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('System Information') }}</h3>
                                <div class="bg-gray-50 rounded-lg p-4 space-y-2">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">{{ __('Created At') }}:</span>
                                        <span class="text-gray-900">{{ $transfer->created_at->format('Y-m-d H:i:s') }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">{{ __('Last Updated') }}:</span>
                                        <span class="text-gray-900">{{ $transfer->updated_at->format('Y-m-d H:i:s') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 