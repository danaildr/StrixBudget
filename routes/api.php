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

// Public authentication routes
Route::prefix('auth')->group(function () {
    Route::post('login', [AuthApiController::class, 'login']);
});

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Authentication routes
    Route::prefix('auth')->group(function () {
        Route::post('logout', [AuthApiController::class, 'logout']);
        Route::get('user', [AuthApiController::class, 'user']);
        Route::post('refresh', [AuthApiController::class, 'refresh']);
        Route::put('profile', [AuthApiController::class, 'updateProfile']);
        Route::put('password', [AuthApiController::class, 'changePassword']);
        Route::get('statistics', [AuthApiController::class, 'statistics']);
    });

    // Bank Accounts
    Route::apiResource('bank-accounts', BankAccountApiController::class);
    Route::get('bank-accounts/{bankAccount}/statistics', [BankAccountApiController::class, 'statistics']);

    // Transactions
    Route::apiResource('transactions', TransactionApiController::class);

    // Transfers
    Route::apiResource('transfers', TransferApiController::class);

    // Counterparties
    Route::apiResource('counterparties', CounterpartyApiController::class);
    Route::get('counterparties/{counterparty}/statistics', [CounterpartyApiController::class, 'statistics']);
    Route::get('counterparties/{counterparty}/transactions', [CounterpartyApiController::class, 'transactions']);

    // Transaction Types
    Route::apiResource('transaction-types', TransactionTypeApiController::class);
    Route::get('transaction-types/{transactionType}/statistics', [TransactionTypeApiController::class, 'statistics']);
    Route::get('transaction-types/{transactionType}/transactions', [TransactionTypeApiController::class, 'transactions']);
});