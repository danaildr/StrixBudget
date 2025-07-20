<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-bold">{{ __('Recurring Payments') }}</h1>
    </x-slot>
    <div class="container mx-auto py-6">
        <div class="flex justify-between items-center mb-4">
            <a href="{{ route('recurring-payments.create') }}" class="btn btn-primary">+ {{ __('Create Recurring Payment') }}</a>
        </div>
        @if(session('success'))
            <div class="alert alert-success mb-4">{{ session('success') }}</div>
        @endif
        <div class="bg-white shadow rounded-lg overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-4 py-2">{{ __('Bank Account') }}</th>
                        <th class="px-4 py-2">{{ __('Counterparty') }}</th>
                        <th class="px-4 py-2">{{ __('Category') }}</th>
                        <th class="px-4 py-2">{{ __('Amount') }}</th>
                        <th class="px-4 py-2">{{ __('Period') }}</th>
                        <th class="px-4 py-2">{{ __('Active') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                        <tr class="hover:bg-gray-100 cursor-pointer" onclick="window.location='{{ route('recurring-payments.show', $payment) }}'">
                            <td class="px-4 py-2">{{ $payment->bankAccount->name ?? '-' }}</td>
                            <td class="px-4 py-2">{{ $payment->counterparty->name ?? '-' }}</td>
                            <td class="px-4 py-2">{{ $payment->transactionType->name ?? '-' }}</td>
                            <td class="px-4 py-2">{{ number_format($payment->amount, 2) }} {{ $payment->currency }}</td>
                            <td class="px-4 py-2">
                                {{ $payment->repeat_type }}
                                @if($payment->repeat_type === 'custom')
                                    ({{ $payment->repeat_interval }} {{ $payment->repeat_unit }})
                                @endif
                            </td>
                            <td class="px-4 py-2">
                                @if($payment->is_active)
                                    <span class="text-green-600 font-semibold">{{ __('Yes') }}</span>
                                @else
                                    <span class="text-red-600 font-semibold">{{ __('No') }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-2 text-center text-gray-500">{{ __('No recurring payments found.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $payments->links() }}</div>
    </div>
</x-app-layout> 