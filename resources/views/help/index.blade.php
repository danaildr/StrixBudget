<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            <!-- Navigation -->
            <nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="shrink-0 flex items-center">
                                <a href="{{ url('/') }}">
                                    <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                                </a>
                            </div>

                            <!-- Navigation Links -->
                            <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                                <a href="{{ url('/') }}" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                                    {{ __('Home') }}
                                </a>
                                <a href="{{ route('help.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 border-indigo-400 text-gray-900 text-sm font-medium leading-5 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                                    {{ __('Help') }}
                                </a>
                                @if($user)
                                    <a href="{{ route('dashboard') }}" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                                        {{ __('Dashboard') }}
                                    </a>
                                @endif
                            </div>
                        </div>

                        <!-- Authentication Links -->
                        <div class="hidden sm:flex sm:items-center sm:ms-6">
                            @if($user)
                                <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    {{ __('Dashboard') }}
                                </a>
                            @else
                                <div class="space-x-4">
                                    <a href="{{ route('login') }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        {{ __('Log in') }}
                                    </a>
                                    @if (Route::has('register'))
                                        <a href="{{ route('register') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                            {{ __('Register') }}
                                        </a>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        {{ __('User Guide & Help') }}
                    </h2>
                </div>
            </header>

            <!-- Page Content -->
            <main>
                <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <!-- Въведение -->
                    <div class="mb-8">
                        <h3 class="text-2xl font-bold text-gray-900 mb-4">{{ __('Welcome to StrixBudget') }}</h3>
                        <p class="text-gray-700 leading-relaxed">
                            {{ __('StrixBudget is a comprehensive personal finance management system that helps you track your income, expenses, transfers between accounts, and manage your financial relationships with counterparties.') }}
                        </p>
                    </div>

                    @if(!$user)
                    <!-- Регистрация -->
                    <div class="mb-8 bg-blue-50 rounded-lg p-6">
                        <h3 class="text-xl font-semibold text-blue-900 mb-4">{{ __('Getting Started - Registration') }}</h3>
                        <p class="text-blue-800 mb-4">
                            {{ __('To start using StrixBudget, you need to create an account. The registration process requires a special registration key for security purposes.') }}
                        </p>
                        <div class="space-y-3">
                            <div class="flex items-start">
                                <span class="text-blue-600 mr-2 font-bold">1.</span>
                                <div>
                                    <p class="text-blue-800"><strong>{{ __('Obtain a Registration Key') }}</strong></p>
                                    <p class="text-blue-700 text-sm">{{ __('Contact the system administrator to get a valid registration key. This key is required to create your account.') }}</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <span class="text-blue-600 mr-2 font-bold">2.</span>
                                <div>
                                    <p class="text-blue-800"><strong>{{ __('Complete Registration') }}</strong></p>
                                    <p class="text-blue-700 text-sm">{{ __('Click the "Create Account" button, fill in your details (name, email, password), and enter the registration key when prompted.') }}</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <span class="text-blue-600 mr-2 font-bold">3.</span>
                                <div>
                                    <p class="text-blue-800"><strong>{{ __('Start Using the System') }}</strong></p>
                                    <p class="text-blue-700 text-sm">{{ __('Once registered, you can log in and start setting up your financial data following the steps below.') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 text-center">
                            <a href="{{ route('register') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('Create Account') }}
                            </a>
                        </div>
                    </div>
                    @endif

                    <!-- Основни функции -->
                    <div class="mb-8">
                        <h3 class="text-xl font-semibold text-gray-900 mb-4">{{ __('Main Features') }}</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="bg-blue-50 rounded-lg p-4">
                                <h4 class="font-semibold text-blue-900 mb-2">{{ __('Bank Accounts Management') }}</h4>
                                <p class="text-blue-800 text-sm">{{ __('Create and manage multiple bank accounts in different currencies (BGN, EUR, USD). Track balances and set default accounts.') }}</p>
                            </div>
                            <div class="bg-green-50 rounded-lg p-4">
                                <h4 class="font-semibold text-green-900 mb-2">{{ __('Transaction Tracking') }}</h4>
                                <p class="text-green-800 text-sm">{{ __('Record income and expenses with detailed categorization, counterparty information, and descriptions.') }}</p>
                            </div>
                            <div class="bg-purple-50 rounded-lg p-4">
                                <h4 class="font-semibold text-purple-900 mb-2">{{ __('Money Transfers') }}</h4>
                                <p class="text-purple-800 text-sm">{{ __('Transfer money between your accounts with automatic currency conversion and exchange rate tracking.') }}</p>
                            </div>
                            <div class="bg-yellow-50 rounded-lg p-4">
                                <h4 class="font-semibold text-yellow-900 mb-2">{{ __('Counterparties & Categories') }}</h4>
                                <p class="text-yellow-800 text-sm">{{ __('Manage people and organizations you transact with, and categorize your transactions for better organization.') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Стъпка по стъпка ръководство -->
                    <div class="mb-8">
                        <h3 class="text-xl font-semibold text-gray-900 mb-4">{{ __('Getting Started - Step by Step') }}</h3>
                        
                        <!-- Стъпка 1: Банкови сметки -->
                        <div class="mb-6 border-l-4 border-blue-500 pl-4">
                            <h4 class="text-lg font-semibold text-gray-900 mb-2">{{ __('Step 1: Create Bank Accounts') }}</h4>
                            <p class="text-gray-700 mb-2">{{ __('Start by creating your bank accounts:') }}</p>
                            <ul class="list-disc list-inside text-gray-600 space-y-1 ml-4">
                                <li>{{ __('Go to "Accounts" in the navigation menu') }}</li>
                                <li>{{ __('Click "New Account" button') }}</li>
                                <li>{{ __('Enter account name (e.g., "Main Checking Account")') }}</li>
                                <li>{{ __('Select currency (BGN, EUR, or USD)') }}</li>
                                <li>{{ __('Enter initial balance') }}</li>
                                <li>{{ __('Mark as default if this is your primary account') }}</li>
                            </ul>
                        </div>

                        <!-- Стъпка 2: Контрагенти -->
                        <div class="mb-6 border-l-4 border-green-500 pl-4">
                            <h4 class="text-lg font-semibold text-gray-900 mb-2">{{ __('Step 2: Add Counterparties') }}</h4>
                            <p class="text-gray-700 mb-2">{{ __('Create records for people and organizations you transact with:') }}</p>
                            <ul class="list-disc list-inside text-gray-600 space-y-1 ml-4">
                                <li>{{ __('Go to "Counterparties" in the navigation menu') }}</li>
                                <li>{{ __('Click "New Counterparty" button') }}</li>
                                <li>{{ __('Enter name (e.g., "Grocery Store", "John Doe", "Salary - Company XYZ")') }}</li>
                                <li>{{ __('Optionally add email, phone, and description') }}</li>
                            </ul>
                        </div>

                        <!-- Стъпка 3: Категории -->
                        <div class="mb-6 border-l-4 border-purple-500 pl-4">
                            <h4 class="text-lg font-semibold text-gray-900 mb-2">{{ __('Step 3: Create Transaction Categories') }}</h4>
                            <p class="text-gray-700 mb-2">{{ __('Organize your transactions with categories:') }}</p>
                            <ul class="list-disc list-inside text-gray-600 space-y-1 ml-4">
                                <li>{{ __('Go to "Categories" in the navigation menu') }}</li>
                                <li>{{ __('Click "New Category" button') }}</li>
                                <li>{{ __('Enter category name (e.g., "Groceries", "Salary", "Utilities", "Entertainment")') }}</li>
                                <li>{{ __('Add description if needed') }}</li>
                            </ul>
                        </div>

                        <!-- Стъпка 4: Транзакции -->
                        <div class="mb-6 border-l-4 border-red-500 pl-4">
                            <h4 class="text-lg font-semibold text-gray-900 mb-2">{{ __('Step 4: Record Transactions') }}</h4>
                            <p class="text-gray-700 mb-2">{{ __('Start tracking your income and expenses:') }}</p>
                            <ul class="list-disc list-inside text-gray-600 space-y-1 ml-4">
                                <li>{{ __('Use the "NEW" dropdown button in the top navigation') }}</li>
                                <li>{{ __('Select "New Transaction"') }}</li>
                                <li>{{ __('Choose transaction type: Income or Expense') }}</li>
                                <li>{{ __('Select bank account, counterparty, and category') }}</li>
                                <li>{{ __('Enter amount and description') }}</li>
                                <li>{{ __('Set the transaction date') }}</li>
                            </ul>
                        </div>

                        <!-- Стъпка 5: Трансфери -->
                        <div class="mb-6 border-l-4 border-yellow-500 pl-4">
                            <h4 class="text-lg font-semibold text-gray-900 mb-2">{{ __('Step 5: Transfer Money Between Accounts') }}</h4>
                            <p class="text-gray-700 mb-2">{{ __('Move money between your accounts:') }}</p>
                            <ul class="list-disc list-inside text-gray-600 space-y-1 ml-4">
                                <li>{{ __('Use the "NEW" dropdown button and select "New Transfer"') }}</li>
                                <li>{{ __('Select source account (money will be deducted from here)') }}</li>
                                <li>{{ __('Select destination account (money will be added here)') }}</li>
                                <li>{{ __('Enter amount in source currency') }}</li>
                                <li>{{ __('System will automatically calculate exchange rate for different currencies') }}</li>
                                <li>{{ __('Add description and set transfer date') }}</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Полезни съвети -->
                    <div class="mb-8">
                        <h3 class="text-xl font-semibold text-gray-900 mb-4">{{ __('Useful Tips') }}</h3>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <ul class="space-y-2 text-gray-700">
                                <li class="flex items-start">
                                    <span class="text-green-500 mr-2">✓</span>
                                    {{ __('Use descriptive names for accounts, counterparties, and categories to make tracking easier') }}
                                </li>
                                <li class="flex items-start">
                                    <span class="text-green-500 mr-2">✓</span>
                                    {{ __('Set one account as default to speed up transaction entry') }}
                                </li>
                                <li class="flex items-start">
                                    <span class="text-green-500 mr-2">✓</span>
                                    {{ __('Use the search functionality to quickly find transactions, counterparties, or categories') }}
                                </li>
                                <li class="flex items-start">
                                    <span class="text-green-500 mr-2">✓</span>
                                    {{ __('Check the Dashboard regularly to see your account balances and financial overview') }}
                                </li>
                                <li class="flex items-start">
                                    <span class="text-green-500 mr-2">✓</span>
                                    {{ __('Use the export functionality to backup your data or analyze it in external tools') }}
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Навигация и функции -->
                    <div class="mb-8">
                        <h3 class="text-xl font-semibold text-gray-900 mb-4">{{ __('Navigation & Features') }}</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <h4 class="font-semibold text-gray-900 mb-2">{{ __('Main Menu Items') }}</h4>
                                <ul class="text-sm text-gray-600 space-y-1">
                                    <li><strong>{{ __('Dashboard') }}:</strong> {{ __('Overview of all accounts and balances') }}</li>
                                    <li><strong>{{ __('Transactions') }}:</strong> {{ __('View and manage all income/expense records') }}</li>
                                    <li><strong>{{ __('Transfers') }}:</strong> {{ __('View and manage money transfers between accounts') }}</li>
                                    <li><strong>{{ __('Counterparties') }}:</strong> {{ __('Manage people and organizations') }}</li>
                                    <li><strong>{{ __('Categories') }}:</strong> {{ __('Manage transaction categories') }}</li>
                                    <li><strong>{{ __('Accounts') }}:</strong> {{ __('Manage your bank accounts') }}</li>
                                </ul>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900 mb-2">{{ __('Quick Actions') }}</h4>
                                <ul class="text-sm text-gray-600 space-y-1">
                                    <li><strong>{{ __('NEW Button') }}:</strong> {{ __('Quick access to create transactions and transfers') }}</li>
                                    <li><strong>{{ __('Search') }}:</strong> {{ __('Available on most listing pages') }}</li>
                                    <li><strong>{{ __('Export') }}:</strong> {{ __('Download data in various formats (CSV, Excel, PDF)') }}</li>
                                    <li><strong>{{ __('Import') }}:</strong> {{ __('Bulk import data from files') }}</li>
                                    <li><strong>{{ __('Filters') }}:</strong> {{ __('Filter transactions by date, account, type, etc.') }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    @if($isAdmin)
                    <!-- Админ панел -->
                    <div class="mb-8 bg-red-50 rounded-lg p-6">
                        <h3 class="text-xl font-semibold text-red-900 mb-4">{{ __('Administrator Panel') }}</h3>
                        <p class="text-red-800 mb-4">
                            {{ __('As an administrator, you have access to additional features for managing the system and users.') }}
                        </p>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                            <div class="bg-white rounded-lg p-4 border border-red-200">
                                <h4 class="font-semibold text-red-900 mb-2">{{ __('User Management') }}</h4>
                                <p class="text-red-800 text-sm mb-2">{{ __('Manage system users and their roles:') }}</p>
                                <ul class="text-red-700 text-sm space-y-1">
                                    <li>• {{ __('View all registered users') }}</li>
                                    <li>• {{ __('Change user roles (user/admin)') }}</li>
                                    <li>• {{ __('Monitor user activity') }}</li>
                                </ul>
                            </div>

                            <div class="bg-white rounded-lg p-4 border border-red-200">
                                <h4 class="font-semibold text-red-900 mb-2">{{ __('Registration Keys') }}</h4>
                                <p class="text-red-800 text-sm mb-2">{{ __('Control access to the system:') }}</p>
                                <ul class="text-red-700 text-sm space-y-1">
                                    <li>• {{ __('Generate new registration keys') }}</li>
                                    <li>• {{ __('View existing keys and their usage') }}</li>
                                    <li>• {{ __('Delete unused or expired keys') }}</li>
                                </ul>
                            </div>
                        </div>

                        <div class="bg-white rounded-lg p-4 border border-red-200">
                            <h4 class="font-semibold text-red-900 mb-2">{{ __('How to Access Admin Panel') }}</h4>
                            <ol class="text-red-700 text-sm space-y-1">
                                <li><strong>1.</strong> {{ __('Look for "Admin" in the main navigation menu') }}</li>
                                <li><strong>2.</strong> {{ __('Click on "Admin" to access the admin dashboard') }}</li>
                                <li><strong>3.</strong> {{ __('Use the sub-menu to navigate between Users and Registration Keys') }}</li>
                            </ol>
                        </div>

                        <div class="mt-4 text-center">
                            <a href="{{ route('admin.index') }}" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('Go to Admin Panel') }}
                            </a>
                        </div>
                    </div>
                    @endif

                    <!-- Поддръжка -->
                    <div class="bg-blue-50 rounded-lg p-6">
                        <h3 class="text-xl font-semibold text-blue-900 mb-4">{{ __('Need More Help?') }}</h3>
                        <p class="text-blue-800 mb-4">
                            {{ __('If you need additional assistance or have questions about using StrixBudget, here are some resources:') }}
                        </p>
                        <ul class="text-blue-700 space-y-2">
                            <li>• {{ __('Explore each section of the application to familiarize yourself with the interface') }}</li>
                            <li>• {{ __('Start with small test transactions to understand the workflow') }}</li>
                            <li>• {{ __('Use the view/edit functions to see how data is organized') }}</li>
                            <li>• {{ __('Check the statistics and reports to understand your financial patterns') }}</li>
                        </ul>
                    </div>

                </div>
            </div>
        </div>
    </div>
            </main>
        </div>
    </body>
</html>
