<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Counterparties</h2>
            <a href="{{ route('counterparties.create') }}" class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-green-700">NEW COUNTERPARTY</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    <!-- Search/Filter Form -->
                    <form method="GET" action="{{ route('counterparties.index') }}" class="mb-6">
                        <div class="flex flex-col md:flex-row gap-4">
                            <div class="flex-1">
                                <x-input-label for="search" :value="__('Search')" />
                                <x-text-input id="search" name="search" type="text" class="mt-1 block w-full" 
                                    value="{{ request('search') }}" placeholder="{{ __('Search by name, email, phone...') }}" />
                            </div>
                            <div class="flex items-end space-x-2">
                                <x-primary-button>
                                    {{ __('Filter') }}
                                </x-primary-button>
                                @if(request()->has('search'))
                                    <a href="{{ route('counterparties.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                        {{ __('Clear') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($counterparties as $counterparty)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $counterparty->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $counterparty->email }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $counterparty->phone }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <div class="flex space-x-3">
                                                <a href="{{ route('counterparties.show', $counterparty) }}" class="text-blue-600 hover:text-blue-900">{{ __('View') }}</a>
                                                <a href="{{ route('counterparties.edit', $counterparty) }}" class="text-indigo-600 hover:text-indigo-900">{{ __('Edit') }}</a>
                                                <form method="POST" action="{{ route('counterparties.destroy', $counterparty) }}" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('{{ __('Are you sure you want to delete this counterparty?') }}')">
                                                        {{ __('Delete') }}
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                            {{ __('No counterparties found.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $counterparties->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 