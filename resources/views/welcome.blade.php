<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Laravel') }}</title>
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased">
        <div class="relative min-h-screen bg-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div class="text-center">
                    <h1 class="text-4xl font-bold text-gray-900 mb-8">
                        {{ __('Personal Finance Management') }}
                    </h1>
                    <p class="text-xl text-gray-600 mb-12 max-w-3xl mx-auto">
                        {{ __('Take control of your financial life with our comprehensive personal finance management system. Track your income, expenses, and transfers across multiple accounts and currencies.') }}
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">{{ __('Track Expenses') }}</h2>
                        <p class="text-gray-600">{{ __('Easily monitor your daily expenses and income. Categorize transactions and understand your spending patterns.') }}</p>
                    </div>

                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">{{ __('Multiple Accounts') }}</h2>
                        <p class="text-gray-600">{{ __('Manage multiple bank accounts in different currencies. Keep track of your total wealth in one place.') }}</p>
                    </div>

                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">{{ __('Financial Overview') }}</h2>
                        <p class="text-gray-600">{{ __('Get detailed insights into your finances with monthly and yearly statistics and trends.') }}</p>
                    </div>
                </div>

                <div class="text-center space-x-4">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('Go to Dashboard') }}
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('Log in') }}
                            </a>

                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    {{ __('Create Account') }}
                                </a>
                            @endif
                        @endauth
                    @endif
                </div>

                <!-- Help Button -->
                <div class="text-center mt-8">
                    <a href="{{ route('help.index') }}" class="inline-flex items-center px-6 py-3 bg-blue-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ __('User Guide & Help') }}
                    </a>
                </div>
            </div>
        </div>
    </body>
</html>
