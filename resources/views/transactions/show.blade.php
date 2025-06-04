<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Transaction Details') }}
            </h2>
            <div class="flex items-center space-x-4">
                <a href="{{ route('transactions.edit', $transaction) }}" 
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                    {{ __('Edit') }}
                </a>
                <form method="POST" action="{{ route('transactions.destroy', $transaction) }}" 
                      class="inline" 
                      onsubmit="return confirm('{{ __('Are you sure you want to delete this transaction? This will affect the account balance.') }}')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">
                        {{ __('Delete') }}
                    </button>
                </form>
                <a href="{{ route('transactions.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
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
                                        <span class="text-gray-600">{{ __('Type') }}:</span>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $transaction->type === 'income' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ ucfirst($transaction->type) }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">{{ __('Category') }}:</span>
                                        <span class="text-gray-900">{{ $transaction->transactionType->name }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">{{ __('Amount') }}:</span>
                                        <span class="font-medium {{ $transaction->type === 'income' ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $transaction->type === 'income' ? '+' : '-' }}{{ number_format($transaction->amount, 2) }} {{ $transaction->currency }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">{{ __('Date') }}:</span>
                                        <span class="text-gray-900">{{ $transaction->executed_at->format('Y-m-d H:i') }}</span>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('Account Information') }}</h3>
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <div class="flex justify-between mb-2">
                                        <span class="text-gray-600">{{ __('Account') }}:</span>
                                        <span class="text-gray-900">{{ $transaction->bankAccount->name }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">{{ __('Currency') }}:</span>
                                        <span class="text-gray-900">{{ $transaction->currency }}</span>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('Counterparty Information') }}</h3>
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <div class="flex justify-between mb-2">
                                        <span class="text-gray-600">{{ __('Name') }}:</span>
                                        <span class="text-gray-900">{{ $transaction->counterparty->name }}</span>
                                    </div>
                                    @if($transaction->counterparty->email)
                                        <div class="flex justify-between mb-2">
                                            <span class="text-gray-600">{{ __('Email') }}:</span>
                                            <span class="text-gray-900">{{ $transaction->counterparty->email }}</span>
                                        </div>
                                    @endif
                                    @if($transaction->counterparty->phone)
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">{{ __('Phone') }}:</span>
                                            <span class="text-gray-900">{{ $transaction->counterparty->phone }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Допълнителна информация -->
                        <div class="space-y-4">
                            @if($transaction->description)
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('Description') }}</h3>
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <p class="text-gray-900 whitespace-pre-wrap">{{ $transaction->description }}</p>
                                    </div>
                                </div>
                            @endif

                            @if($transaction->attachment_path)
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('Attachment') }}</h3>
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <a href="{{ Storage::url($transaction->attachment_path) }}" target="_blank" 
                                           class="inline-flex items-center text-blue-600 hover:text-blue-900">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                      d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                            </svg>
                                            {{ __('View Attachment') }}
                                        </a>
                                    </div>
                                </div>
                            @endif

                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('System Information') }}</h3>
                                <div class="bg-gray-50 rounded-lg p-4 space-y-2">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">{{ __('Created At') }}:</span>
                                        <span class="text-gray-900">{{ $transaction->created_at->format('Y-m-d H:i:s') }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">{{ __('Last Updated') }}:</span>
                                        <span class="text-gray-900">{{ $transaction->updated_at->format('Y-m-d H:i:s') }}</span>
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