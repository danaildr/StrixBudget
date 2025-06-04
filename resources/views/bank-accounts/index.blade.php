<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Bank Accounts') }}</h2>
            <a href="{{ route('bank-accounts.create') }}" class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-green-700">{{ __('NEW BANK ACCOUNT') }}</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('warning'))
                <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('warning') }}</span>
                </div>
            @endif

            @if(session('select_default_account'))
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900">
                        <div class="flex items-start mb-4">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-yellow-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-lg font-medium text-gray-900">{{ __('Select New Default Account') }}</h3>
                                <p class="mt-1 text-sm text-gray-500">
                                    {{ __('You are about to delete your default account. Please select a new default account before proceeding.') }}
                                </p>
                            </div>
                        </div>
                        <form method="POST" action="{{ route('bank-accounts.set-default') }}" class="space-y-4">
                            @csrf
                            <input type="hidden" name="deleted_account_id" value="{{ session('deleted_account_id') }}">
                            
                            <div>
                                <select name="new_default_account_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">{{ __('Select Account') }}</option>
                                    @foreach(session('other_accounts') as $account)
                                        <option value="{{ $account->id }}">
                                            {{ $account->name }} ({{ $account->currency }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="flex items-center gap-4">
                                <x-primary-button>{{ __('Set as Default and Delete Old Account') }}</x-primary-button>
                                <a href="{{ route('bank-accounts.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                    {{ __('Cancel') }}
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <th class="px-6 py-3">Name</th>
                                    <th class="px-6 py-3">Currency</th>
                                    <th class="px-6 py-3 text-right">Balance</th>
                                    <th class="px-6 py-3">Status</th>
                                    <th class="px-6 py-3">Default</th>
                                    <th class="px-6 py-3 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse($accounts as $account)
                                    <tr class="text-sm">
                                        <td class="px-6 py-4">{{ $account->name }}</td>
                                        <td class="px-6 py-4">{{ $account->currency }}</td>
                                        <td class="px-6 py-4 text-right {{ $account->balance < 0 ? 'text-red-600' : 'text-gray-900' }}">
                                            {{ number_format($account->balance, 2) }} {{ $account->currency }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="px-2 py-1 text-xs rounded-full {{ $account->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $account->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($account->is_default)
                                                <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800 flex items-center w-fit">
                                                    <svg class="h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                    </svg>
                                                    {{ __('Default') }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-right space-x-3">
                                            <a href="{{ route('bank-accounts.show', $account) }}" class="text-blue-600 hover:text-blue-900">{{ __('View') }}</a>
                                            <a href="{{ route('bank-accounts.edit', $account) }}" class="text-indigo-600 hover:text-indigo-900">{{ __('Edit') }}</a>
                                            @if(!$account->is_default)
                                                <form action="{{ route('bank-accounts.destroy', $account) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('{{ __('Are you sure you want to delete this account?') }}')">{{ __('Delete') }}</button>
                                                </form>
                                            @else
                                                <span class="text-gray-400 cursor-not-allowed" title="{{ __('Default account cannot be deleted. Set another account as default first.') }}">{{ __('Delete') }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                            No bank accounts found
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 