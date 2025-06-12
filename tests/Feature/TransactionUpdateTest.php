<?php

namespace Tests\Feature;

use App\Models\BankAccount;
use App\Models\Counterparty;
use App\Models\Transaction;
use App\Models\TransactionType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionUpdateTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private BankAccount $bankAccount;
    private Counterparty $counterparty;
    private TransactionType $transactionType;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->bankAccount = BankAccount::factory()->create([
            'user_id' => $this->user->id,
            'balance' => 1000.00,
            'currency' => 'BGN'
        ]);
        $this->counterparty = Counterparty::factory()->create(['user_id' => $this->user->id]);
        $this->transactionType = TransactionType::factory()->create(['user_id' => $this->user->id]);
    }

    public function test_expense_transaction_update_calculates_balance_correctly()
    {
        // Създаваме първоначална транзакция - разход от 21.34
        $transaction = Transaction::create([
            'user_id' => $this->user->id,
            'bank_account_id' => $this->bankAccount->id,
            'counterparty_id' => $this->counterparty->id,
            'transaction_type_id' => $this->transactionType->id,
            'type' => 'expense',
            'amount' => 21.34,
            'currency' => 'BGN',
            'executed_at' => now(),
        ]);

        // Обновяваме баланса ръчно (симулираме създаването на транзакция)
        $this->bankAccount->withdraw(21.34);

        // Проверяваме началния баланс (1000 - 21.34 = 978.66)
        $this->bankAccount->refresh();
        $this->assertEquals(978.66, $this->bankAccount->balance);

        // Обновяваме транзакцията на 26.34
        $response = $this->actingAs($this->user)
            ->put(route('transactions.update', $transaction), [
                'bank_account_id' => $this->bankAccount->id,
                'counterparty_id' => $this->counterparty->id,
                'transaction_type_id' => $this->transactionType->id,
                'type' => 'expense',
                'amount' => '26,34', // Използваме запетая за тест
                'description' => 'Updated transaction',
                'executed_at' => now()->format('Y-m-d H:i:s'),
            ]);

        $response->assertRedirect(route('transactions.index'));

        // Проверяваме финалния баланс
        // Трябва да е: 1000 (начален) - 26.34 (нова сума) = 973.66
        // НЕ: 1000 - 21.34 - 26.34 = 952.32 (грешната логика)
        $this->bankAccount->refresh();
        $this->assertEquals(973.66, $this->bankAccount->balance);

        // Проверяваме че транзакцията е обновена
        $transaction->refresh();
        $this->assertEquals(26.34, $transaction->amount);
    }

    public function test_income_transaction_update_calculates_balance_correctly()
    {
        // Създаваме първоначална транзакция - приход от 50.00
        $transaction = Transaction::create([
            'user_id' => $this->user->id,
            'bank_account_id' => $this->bankAccount->id,
            'counterparty_id' => $this->counterparty->id,
            'transaction_type_id' => $this->transactionType->id,
            'type' => 'income',
            'amount' => 50.00,
            'currency' => 'BGN',
            'executed_at' => now(),
        ]);

        // Обновяваме баланса ръчно (симулираме създаването на транзакция)
        $this->bankAccount->deposit(50.00);

        // Проверяваме началния баланс (1000 + 50 = 1050)
        $this->bankAccount->refresh();
        $this->assertEquals(1050.00, $this->bankAccount->balance);

        // Обновяваме транзакцията на 75.00
        $response = $this->actingAs($this->user)
            ->put(route('transactions.update', $transaction), [
                'bank_account_id' => $this->bankAccount->id,
                'counterparty_id' => $this->counterparty->id,
                'transaction_type_id' => $this->transactionType->id,
                'type' => 'income',
                'amount' => '75.00',
                'description' => 'Updated income transaction',
                'executed_at' => now()->format('Y-m-d H:i:s'),
            ]);

        $response->assertRedirect(route('transactions.index'));

        // Проверяваме финалния баланс
        // Трябва да е: 1000 (начален) + 75.00 (нова сума) = 1075.00
        $this->bankAccount->refresh();
        $this->assertEquals(1075.00, $this->bankAccount->balance);

        // Проверяваме че транзакцията е обновена
        $transaction->refresh();
        $this->assertEquals(75.00, $transaction->amount);
    }

    public function test_transaction_type_change_calculates_balance_correctly()
    {
        // Създаваме първоначална транзакция - разход от 30.00
        $transaction = Transaction::create([
            'user_id' => $this->user->id,
            'bank_account_id' => $this->bankAccount->id,
            'counterparty_id' => $this->counterparty->id,
            'transaction_type_id' => $this->transactionType->id,
            'type' => 'expense',
            'amount' => 30.00,
            'currency' => 'BGN',
            'executed_at' => now(),
        ]);

        // Обновяваме баланса ръчно (симулираме създаването на транзакция)
        $this->bankAccount->withdraw(30.00);

        // Проверяваме началния баланс (1000 - 30 = 970)
        $this->bankAccount->refresh();
        $this->assertEquals(970.00, $this->bankAccount->balance);

        // Променяме транзакцията от разход на приход със същата сума
        $response = $this->actingAs($this->user)
            ->put(route('transactions.update', $transaction), [
                'bank_account_id' => $this->bankAccount->id,
                'counterparty_id' => $this->counterparty->id,
                'transaction_type_id' => $this->transactionType->id,
                'type' => 'income', // Променяме от expense на income
                'amount' => '30.00',
                'description' => 'Changed to income',
                'executed_at' => now()->format('Y-m-d H:i:s'),
            ]);

        $response->assertRedirect(route('transactions.index'));

        // Проверяваме финалния баланс
        // Трябва да е: 1000 (начален) + 30.00 (нова сума като приход) = 1030.00
        $this->bankAccount->refresh();
        $this->assertEquals(1030.00, $this->bankAccount->balance);

        // Проверяваме че транзакцията е обновена
        $transaction->refresh();
        $this->assertEquals('income', $transaction->type);
        $this->assertEquals(30.00, $transaction->amount);
    }
}
