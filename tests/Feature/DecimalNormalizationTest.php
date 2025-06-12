<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\BankAccount;
use App\Models\Counterparty;
use App\Models\TransactionType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DecimalNormalizationTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected BankAccount $account;
    protected Counterparty $counterparty;
    protected TransactionType $transactionType;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        
        $this->account = BankAccount::create([
            'user_id' => $this->user->id,
            'name' => 'Test Account',
            'currency' => 'BGN',
            'balance' => 1000.00,
            'is_active' => true,
            'is_default' => true,
        ]);

        $this->counterparty = Counterparty::create([
            'user_id' => $this->user->id,
            'name' => 'Test Counterparty',
        ]);

        $this->transactionType = TransactionType::create([
            'user_id' => $this->user->id,
            'name' => 'Test Category',
        ]);
    }

    public function test_transaction_amount_with_comma_is_normalized()
    {
        $response = $this->actingAs($this->user)
            ->post('/transactions', [
                'bank_account_id' => $this->account->id,
                'counterparty_id' => $this->counterparty->id,
                'transaction_type_id' => $this->transactionType->id,
                'type' => 'income',
                'amount' => '123,45', // Използваме запетая
                'description' => 'Test transaction',
                'executed_at' => now()->format('Y-m-d H:i:s'),
            ]);

        $response->assertRedirect('/transactions');
        
        // Проверяваме дали транзакцията е създадена с правилната сума
        $this->assertDatabaseHas('transactions', [
            'user_id' => $this->user->id,
            'amount' => 123.45, // Трябва да е с точка
        ]);
    }

    public function test_transfer_amount_with_comma_is_normalized()
    {
        $toAccount = BankAccount::create([
            'user_id' => $this->user->id,
            'name' => 'Test Account 2',
            'currency' => 'EUR',
            'balance' => 0.00,
            'is_active' => true,
            'is_default' => false,
        ]);

        $response = $this->actingAs($this->user)
            ->post('/transfers', [
                'from_account_id' => $this->account->id,
                'to_account_id' => $toAccount->id,
                'amount_from' => '100,50', // Използваме запетая
                'exchange_rate' => '1,95', // Използваме запетая
                'description' => 'Test transfer',
                'executed_at' => now()->format('Y-m-d H:i:s'),
            ]);

        $response->assertRedirect('/transfers');
        
        // Проверяваме дали трансферът е създаден с правилните суми
        $this->assertDatabaseHas('transfers', [
            'user_id' => $this->user->id,
            'amount_from' => 100.50, // Трябва да е с точка
            'exchange_rate' => 1.95, // Трябва да е с точка
        ]);
    }

    public function test_bank_account_initial_balance_with_comma_is_normalized()
    {
        $response = $this->actingAs($this->user)
            ->post('/bank-accounts', [
                'name' => 'New Account',
                'currency' => 'USD',
                'initial_balance' => '500,75', // Използваме запетая
                'is_active' => true,
                'is_default' => false,
            ]);

        $response->assertRedirect('/bank-accounts');
        
        // Проверяваме дали сметката е създадена с правилния баланс
        $this->assertDatabaseHas('bank_accounts', [
            'user_id' => $this->user->id,
            'name' => 'New Account',
            'balance' => 500.75, // Трябва да е с точка
        ]);
    }
}
