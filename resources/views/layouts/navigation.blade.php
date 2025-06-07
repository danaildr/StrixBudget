<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    <x-nav-link :href="route('transactions.index')" :active="request()->routeIs('transactions.*')">
                        {{ __('Transactions') }}
                    </x-nav-link>
                    <x-nav-link :href="route('transfers.index')" :active="request()->routeIs('transfers.index')">
                        {{ __('Transfers') }}
                    </x-nav-link>
                    <x-nav-link :href="route('counterparties.index')" :active="request()->routeIs('counterparties.*')">
                        {{ __('Counterparties') }}
                    </x-nav-link>
                    <x-nav-link :href="route('transaction-types.index')" :active="request()->routeIs('transaction-types.*')">
                        {{ __('Categories') }}
                    </x-nav-link>
                    <x-nav-link :href="route('bank-accounts.index')" :active="request()->routeIs('bank-accounts.*')">
                        {{ __('Accounts') }}
                    </x-nav-link>
                </div>
            </div>

            <!-- New Transaction/Transfer Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-auto me-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-green-700 inline-flex items-center">
                            NEW
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('transactions.create')" class="text-sm">
                            {{ __('New Transaction') }}
                        </x-dropdown-link>
                        <x-dropdown-link :href="route('transfers.create')" class="text-sm">
                            {{ __('New Transfer') }}
                        </x-dropdown-link>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Help Button -->
            <div class="hidden sm:flex sm:items-center me-6">
                <a href="{{ route('help.index') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 inline-flex items-center" title="{{ __('Help & User Guide') }}">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="ml-2">{{ __('Help') }}</span>
                </a>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('transactions.index')" :active="request()->routeIs('transactions.*')">
                {{ __('Transactions') }}
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('transfers.index')" :active="request()->routeIs('transfers.*')">
                {{ __('Transfers') }}
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('counterparties.index')" :active="request()->routeIs('counterparties.*')">
                {{ __('Counterparties') }}
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('transaction-types.index')" :active="request()->routeIs('transaction-types.*')">
                {{ __('Categories') }}
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('bank-accounts.index')" :active="request()->routeIs('bank-accounts.*')">
                {{ __('Accounts') }}
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('help.index')" :active="request()->routeIs('help.*')">
                {{ __('Help') }}
            </x-responsive-nav-link>

            <!-- Mobile New Transaction/Transfer Menu -->
            <div class="px-4 py-2 space-y-1">
                <div class="font-medium text-base text-gray-800 px-3 py-2">New:</div>
                <a href="{{ route('transactions.create') }}" class="block w-full px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">
                    {{ __('New Transaction') }}
                </a>
                <a href="{{ route('transfers.create') }}" class="block w-full px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">
                    {{ __('New Transfer') }}
                </a>
            </div>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
