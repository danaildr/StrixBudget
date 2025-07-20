<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Scheduled Payment Details') }}
            </h2>
            <div class="flex space-x-4">
                <a href="{{ route('scheduled-payments.edit', $payment) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                    {{ __('Edit Scheduled Payment') }}
                </a>
                <a href="{{ route('scheduled-payments.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    {{ __('Back to List') }}
                </a>
            </div>
        </div>
    </x-slot>
    <div class="container mx-auto py-6 max-w-lg">
        <div class="flex justify-between items-center mb-4">
            <a href="{{ route('scheduled-payments.edit', $payment) }}" class="btn btn-warning">Редакция</a>
        </div>
        <div class="bg-white shadow rounded-lg p-6">
            <dl class="divide-y divide-gray-200">
                <div class="py-2 flex justify-between">
                    <dt class="font-semibold">{{ __('Account') }}:</dt>
                    <dd>{{ $payment->bankAccount->name ?? '-' }}</dd>
                </div>
                <div class="py-2 flex justify-between">
                    <dt class="font-semibold">{{ __('Counterparty') }}:</dt>
                    <dd>{{ $payment->counterparty->name ?? '-' }}</dd>
                </div>
                <div class="py-2 flex justify-between">
                    <dt class="font-semibold">{{ __('Category') }}:</dt>
                    <dd>{{ $payment->transactionType->name ?? '-' }}</dd>
                </div>
                <div class="py-2 flex justify-between">
                    <dt class="font-semibold">{{ __('Amount') }}:</dt>
                    <dd>{{ number_format($payment->amount, 2) }} {{ $payment->currency }}</dd>
                </div>
                <div class="py-2 flex justify-between">
                    <dt class="font-semibold">{{ __('Description') }}:</dt>
                    <dd>{{ $payment->description }}</dd>
                </div>
                <div class="py-2 flex justify-between">
                    <dt class="font-semibold">{{ __('Scheduled Date') }}:</dt>
                    <dd>{{ $payment->scheduled_date }}</dd>
                </div>
                <div class="py-2 flex justify-between">
                    <dt class="font-semibold">{{ __('Period') }}:</dt>
                    <dd>
                        @if($payment->period_start_date && $payment->period_end_date)
                            {{ $payment->period_start_date }} - {{ $payment->period_end_date }}
                        @else
                            -
                        @endif
                    </dd>
                </div>
                <div class="py-2 flex justify-between">
                    <dt class="font-semibold">{{ __('Active') }}:</dt>
                    <dd>
                        @if($payment->is_active)
                            <span class="text-green-600 font-semibold">{{ __('Yes') }}</span>
                        @else
                            <span class="text-red-600 font-semibold">{{ __('No') }}</span>
                        @endif
                    </dd>
                </div>
            </dl>
            <div class="mt-6 flex gap-2 justify-end">
                <a href="{{ route('scheduled-payments.make-transaction', $payment) }}" class="btn btn-primary">{{ __('Make Transaction') }}</a>
                <form action="{{ route('scheduled-payments.destroy', $payment) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure?') }}');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">{{ __('Delete') }}</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout> 