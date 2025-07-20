<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\BankAccountController;
use App\Http\Controllers\CounterpartyController;
use App\Http\Controllers\MasterDataController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TransferController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\TransactionTypeController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\HelpController;
use App\Http\Controllers\PowerUserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Help page - достъпна за всички
Route::get('/help', [HelpController::class, 'index'])->name('help.index');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])
        ->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::patch('/profile/locale', [LocaleController::class, 'update'])->name('profile.locale.update');

    Route::resource('bank-accounts', BankAccountController::class);
    Route::post('bank-accounts/set-default', [BankAccountController::class, 'setDefault'])->name('bank-accounts.set-default');
    Route::resource('counterparties', CounterpartyController::class);
    Route::resource('transfers', TransferController::class);
    Route::resource('transactions', TransactionController::class);
    Route::resource('transaction-types', TransactionTypeController::class);
    Route::resource('recurring-payments', App\Http\Controllers\RecurringPaymentController::class);
    Route::get('recurring-payments/{recurring_payment}/make-transaction', [App\Http\Controllers\RecurringPaymentController::class, 'makeTransaction'])->name('recurring-payments.make-transaction');

    Route::resource('scheduled-payments', App\Http\Controllers\ScheduledPaymentController::class);
    Route::get('scheduled-payments/{scheduled_payment}/make-transaction', [App\Http\Controllers\ScheduledPaymentController::class, 'makeTransaction'])->name('scheduled-payments.make-transaction');

    // Master Data route
    Route::get('/master-data', [MasterDataController::class, 'index'])->name('master-data.index');



    // Admin routes
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('index');
        Route::get('/users', [AdminController::class, 'users'])->name('users');
        Route::patch('/users/{user}/role', [AdminController::class, 'updateUserRole'])->name('users.update-role');
        Route::get('/registration-keys', [AdminController::class, 'registrationKeys'])->name('registration-keys');
        Route::post('/registration-keys/generate', [AdminController::class, 'generateKey'])->name('registration-keys.generate');
        Route::delete('/registration-keys/{key}', [AdminController::class, 'deleteKey'])->name('registration-keys.delete');
        Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
        Route::post('/settings', [AdminController::class, 'updateSettings'])->name('settings.update');
    });

    // Power User routes
    Route::prefix('power-user')->name('power-user.')->group(function () {
        Route::get('/registration-keys', [PowerUserController::class, 'registrationKeys'])->name('registration-keys');
        Route::post('/registration-keys/generate', [PowerUserController::class, 'generateKey'])->name('registration-keys.generate');
        Route::delete('/registration-keys/{key}', [PowerUserController::class, 'deleteKey'])->name('registration-keys.delete');
    });

    // Експортиране на транзакции
    Route::get('/transactions/export/{format}', [ExportController::class, 'exportTransactions'])
        ->name('transactions.export')
        ->where('format', 'csv|xlsx|ods|pdf|json|html');

    // Експортиране на трансфери
    Route::get('/transfers/export/{format}', [ExportController::class, 'exportTransfers'])
        ->name('transfers.export')
        ->where('format', 'csv|xlsx|ods|pdf|json|html');

    // Маршрути за импортиране на данни
    Route::get('/import', [ImportController::class, 'index'])->name('import.index');
    Route::get('/import/template/{type}/{format}', [ImportController::class, 'template'])->name('import.template');
    Route::post('/import/transaction-types', [ImportController::class, 'importTransactionTypes'])->name('import.transaction-types');
    Route::post('/import/counterparties', [ImportController::class, 'importCounterparties'])->name('import.counterparties');
    Route::post('/import/bank-accounts', [ImportController::class, 'importBankAccounts'])->name('import.bank-accounts');
    Route::post('/import/transactions', [ImportController::class, 'importTransactions'])->name('import.transactions');
    Route::post('/import/transfers', [ImportController::class, 'importTransfers'])->name('import.transfers');
});

require __DIR__.'/auth.php';
