<?php

return [
    /*
    |--------------------------------------------------------------------------
    | StrixBudget Application Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration specific to the StrixBudget application.
    | All hardcoded values should be moved here for better maintainability.
    |
    */

    'pagination' => [
        'default_per_page' => env('PAGINATION_DEFAULT_PER_PAGE', 25),
        'admin_per_page' => env('PAGINATION_ADMIN_PER_PAGE', 20),
        'details_per_page' => env('PAGINATION_DETAILS_PER_PAGE', 10),
        'search_per_page' => env('PAGINATION_SEARCH_PER_PAGE', 25),
    ],

    'file_uploads' => [
        'max_size_kb' => env('FILE_UPLOAD_MAX_SIZE_KB', 5120),
        'allowed_mimes' => explode(',', env('FILE_UPLOAD_ALLOWED_MIMES', 'jpg,jpeg,png,pdf')),
        'storage_disk' => env('FILE_UPLOAD_STORAGE_DISK', 'public'),
        'storage_path' => env('FILE_UPLOAD_STORAGE_PATH', 'attachments'),
    ],

    'cache_ttl' => [
        'exchange_rates' => env('CACHE_TTL_EXCHANGE_RATES', 3600), // 1 hour
        'user_stats' => env('CACHE_TTL_USER_STATS', 1800), // 30 minutes
        'dashboard_data' => env('CACHE_TTL_DASHBOARD_DATA', 900), // 15 minutes
    ],

    'business' => [
        'supported_currencies' => ['EUR', 'USD', 'BGN', 'GBP', 'CHF', 'JPY'],
        'default_currency' => env('DEFAULT_CURRENCY', 'EUR'),
        'transaction_types' => ['income', 'expense'],
        'user_roles' => ['user', 'power_user', 'admin'],
        'registration_key_length' => env('REGISTRATION_KEY_LENGTH', 32),
        'max_decimal_places' => env('MAX_DECIMAL_PLACES', 2),
        'max_amount' => env('MAX_AMOUNT', 999999999.99),
    ],

    'api' => [
        'exchange_rate_url' => env('EXCHANGE_RATE_API_URL', 'https://open.er-api.com/v6/latest/EUR'),
        'exchange_rate_timeout' => env('EXCHANGE_RATE_API_TIMEOUT', 10),
        'exchange_rate_retries' => env('EXCHANGE_RATE_API_RETRIES', 3),
    ],

    'security' => [
        'rate_limit_attempts' => env('RATE_LIMIT_ATTEMPTS', 5),
        'rate_limit_decay_minutes' => env('RATE_LIMIT_DECAY_MINUTES', 1),
    ],

    'features' => [
        'enable_file_attachments' => env('ENABLE_FILE_ATTACHMENTS', true),
        'enable_multi_currency' => env('ENABLE_MULTI_CURRENCY', true),
        'enable_registration_keys' => env('ENABLE_REGISTRATION_KEYS', true),
        'enable_user_roles' => env('ENABLE_USER_ROLES', true),
    ],
];
