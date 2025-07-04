<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\BankAccountApiController;
use App\Http\Controllers\Api\TransactionApiController;
use App\Http\Controllers\Api\TransferApiController;
use App\Http\Controllers\Api\CounterpartyApiController;
use App\Http\Controllers\Api\TransactionTypeApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// API Health Check and Information
Route::get('/', function () {
    return response()->json([
        'success' => true,
        'message' => 'StrixBudget API is running',
        'version' => '1.0.0',
        'timestamp' => now()->toISOString(),
        'endpoints' => [
            'authentication' => [
                'login' => 'POST /api/auth/login',
                'user' => 'GET /api/auth/user',
                'logout' => 'POST /api/auth/logout',
                'refresh' => 'POST /api/auth/refresh',
                'profile' => 'PUT /api/auth/profile',
                'password' => 'PUT /api/auth/password',
                'statistics' => 'GET /api/auth/statistics'
            ],
            'resources' => [
                'bank_accounts' => '/api/bank-accounts',
                'transactions' => '/api/transactions',
                'transfers' => '/api/transfers',
                'counterparties' => '/api/counterparties',
                'transaction_types' => '/api/transaction-types'
            ]
        ],
        'documentation' => 'See API_DOCUMENTATION.md for detailed information',
        'total_endpoints' => 37
    ]);
});

// Public authentication routes
Route::prefix('auth')->group(function () {
    Route::post('login', [AuthApiController::class, 'login']);
});

// Protected routes
Route::middleware('auth:sanctum')->name('api.')->group(function () {
    // Authentication routes
    Route::prefix('auth')->name('auth.')->group(function () {
        Route::post('logout', [AuthApiController::class, 'logout'])->name('logout');
        Route::get('user', [AuthApiController::class, 'user'])->name('user');
        Route::post('refresh', [AuthApiController::class, 'refresh'])->name('refresh');
        Route::put('profile', [AuthApiController::class, 'updateProfile'])->name('profile');
        Route::put('password', [AuthApiController::class, 'changePassword'])->name('password');
        Route::get('statistics', [AuthApiController::class, 'statistics'])->name('statistics');
    });

    // Bank Accounts
    Route::apiResource('bank-accounts', BankAccountApiController::class);
    Route::get('bank-accounts/{bankAccount}/statistics', [BankAccountApiController::class, 'statistics'])->name('bank-accounts.statistics');

    // Transactions
    Route::apiResource('transactions', TransactionApiController::class);

    // Transfers
    Route::apiResource('transfers', TransferApiController::class);

    // Counterparties
    Route::apiResource('counterparties', CounterpartyApiController::class);
    Route::get('counterparties/{counterparty}/statistics', [CounterpartyApiController::class, 'statistics'])->name('counterparties.statistics');
    Route::get('counterparties/{counterparty}/transactions', [CounterpartyApiController::class, 'transactions'])->name('counterparties.transactions');

    // Transaction Types
    Route::apiResource('transaction-types', TransactionTypeApiController::class);
    Route::get('transaction-types/{transactionType}/statistics', [TransactionTypeApiController::class, 'statistics'])->name('transaction-types.statistics');
    Route::get('transaction-types/{transactionType}/transactions', [TransactionTypeApiController::class, 'transactions'])->name('transaction-types.transactions');
});