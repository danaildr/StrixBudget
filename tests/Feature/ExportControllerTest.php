<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\BankAccount;
use App\Models\Counterparty;
use App\Models\TransactionType;
use App\Models\Transaction;
use App\Models\Transfer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExportControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected BankAccount $account1;
    protected BankAccount $account2;
    protected Counterparty $counterparty;
    protected TransactionType $transactionType;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $this->account1 = BankAccount::create([
            'user_id' => $this->user->id,
            'name' => 'Test Account 1',
            'currency' => 'BGN',
            'balance' => 1000.00,
            'is_active' => true,
            'is_default' => true,
        ]);

        $this->account2 = BankAccount::create([
            'user_id' => $this->user->id,
            'name' => 'Test Account 2',
            'currency' => 'EUR',
            'balance' => 500.00,
            'is_active' => true,
            'is_default' => false,
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

    public function test_export_transactions_without_filters_returns_all_transactions()
    {
        // Създаваме тестови транзакции
        Transaction::create([
            'user_id' => $this->user->id,
            'bank_account_id' => $this->account1->id,
            'counterparty_id' => $this->counterparty->id,
            'transaction_type_id' => $this->transactionType->id,
            'type' => 'income',
            'amount' => 100.00,
            'currency' => 'BGN',
            'description' => 'Test income',
            'executed_at' => '2024-01-15 10:00:00',
        ]);

        Transaction::create([
            'user_id' => $this->user->id,
            'bank_account_id' => $this->account1->id,
            'counterparty_id' => $this->counterparty->id,
            'transaction_type_id' => $this->transactionType->id,
            'type' => 'expense',
            'amount' => 50.00,
            'currency' => 'BGN',
            'description' => 'Test expense',
            'executed_at' => '2024-02-15 10:00:00',
        ]);

        $response = $this->actingAs($this->user)
            ->get('/transactions/export/json');

        $response->assertStatus(200);
        $data = $response->json();
        $this->assertCount(2, $data);
    }

    public function test_export_transactions_with_type_filter_returns_filtered_transactions()
    {
        // Създаваме тестови транзакции
        Transaction::create([
            'user_id' => $this->user->id,
            'bank_account_id' => $this->account1->id,
            'counterparty_id' => $this->counterparty->id,
            'transaction_type_id' => $this->transactionType->id,
            'type' => 'income',
            'amount' => 100.00,
            'currency' => 'BGN',
            'description' => 'Test income',
            'executed_at' => '2024-01-15 10:00:00',
        ]);

        Transaction::create([
            'user_id' => $this->user->id,
            'bank_account_id' => $this->account1->id,
            'counterparty_id' => $this->counterparty->id,
            'transaction_type_id' => $this->transactionType->id,
            'type' => 'expense',
            'amount' => 50.00,
            'currency' => 'BGN',
            'description' => 'Test expense',
            'executed_at' => '2024-02-15 10:00:00',
        ]);

        // Тестваме филтър за входящи транзакции
        $response = $this->actingAs($this->user)
            ->get('/transactions/export/json?type=income');

        $response->assertStatus(200);
        $data = $response->json();
        $this->assertCount(1, $data);
        $this->assertEquals('income', $data[0]['type']);
    }

    public function test_export_transactions_with_date_filter_returns_filtered_transactions()
    {
        // Създаваме тестови транзакции
        Transaction::create([
            'user_id' => $this->user->id,
            'bank_account_id' => $this->account1->id,
            'counterparty_id' => $this->counterparty->id,
            'transaction_type_id' => $this->transactionType->id,
            'type' => 'income',
            'amount' => 100.00,
            'currency' => 'BGN',
            'description' => 'Test income',
            'executed_at' => '2024-01-15 10:00:00',
        ]);

        Transaction::create([
            'user_id' => $this->user->id,
            'bank_account_id' => $this->account1->id,
            'counterparty_id' => $this->counterparty->id,
            'transaction_type_id' => $this->transactionType->id,
            'type' => 'expense',
            'amount' => 50.00,
            'currency' => 'BGN',
            'description' => 'Test expense',
            'executed_at' => '2024-03-15 10:00:00',
        ]);

        // Тестваме филтър за период
        $response = $this->actingAs($this->user)
            ->get('/transactions/export/json?start_date=2024-01-01&end_date=2024-02-28');

        $response->assertStatus(200);
        $data = $response->json();
        $this->assertCount(1, $data);
        $this->assertEquals('2024-01-15T10:00:00.000000Z', $data[0]['executed_at']);
    }

    public function test_export_transfers_without_filters_returns_all_transfers()
    {
        // Създаваме тестови трансфери
        Transfer::create([
            'user_id' => $this->user->id,
            'from_account_id' => $this->account1->id,
            'to_account_id' => $this->account2->id,
            'amount_from' => 100.00,
            'amount_to' => 85.00,
            'currency_from' => 'BGN',
            'currency_to' => 'EUR',
            'exchange_rate' => 0.85,
            'description' => 'Test transfer 1',
            'executed_at' => '2024-01-15 10:00:00',
        ]);

        Transfer::create([
            'user_id' => $this->user->id,
            'from_account_id' => $this->account2->id,
            'to_account_id' => $this->account1->id,
            'amount_from' => 50.00,
            'amount_to' => 50.00,
            'currency_from' => 'EUR',
            'currency_to' => 'EUR',
            'exchange_rate' => 1.00,
            'description' => 'Test transfer 2',
            'executed_at' => '2024-02-15 10:00:00',
        ]);

        $response = $this->actingAs($this->user)
            ->get('/transfers/export/json');

        $response->assertStatus(200);
        $data = $response->json();
        $this->assertCount(2, $data);
    }

    public function test_export_transfers_with_amount_filter_returns_filtered_transfers()
    {
        // Създаваме тестови трансфери
        Transfer::create([
            'user_id' => $this->user->id,
            'from_account_id' => $this->account1->id,
            'to_account_id' => $this->account2->id,
            'amount_from' => 100.00,
            'amount_to' => 85.00,
            'currency_from' => 'BGN',
            'currency_to' => 'EUR',
            'exchange_rate' => 0.85,
            'description' => 'Test transfer 1',
            'executed_at' => '2024-01-15 10:00:00',
        ]);

        Transfer::create([
            'user_id' => $this->user->id,
            'from_account_id' => $this->account2->id,
            'to_account_id' => $this->account1->id,
            'amount_from' => 25.00,
            'amount_to' => 25.00,
            'currency_from' => 'EUR',
            'currency_to' => 'EUR',
            'exchange_rate' => 1.00,
            'description' => 'Test transfer 2',
            'executed_at' => '2024-02-15 10:00:00',
        ]);

        // Тестваме филтър за минимална сума
        $response = $this->actingAs($this->user)
            ->get('/transfers/export/json?min_amount=50');

        $response->assertStatus(200);
        $data = $response->json();
        $this->assertCount(1, $data);
        $this->assertEquals(100.00, $data[0]['amount_from']);
    }
}
