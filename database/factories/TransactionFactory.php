<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\BankAccount;
use App\Models\Counterparty;
use App\Models\TransactionType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = $this->faker->randomElement(['income', 'expense']);

        return [
            'user_id' => User::factory(),
            'bank_account_id' => BankAccount::factory(),
            'counterparty_id' => Counterparty::factory(),
            'transaction_type_id' => TransactionType::factory(),
            'type' => $type,
            'amount' => $this->faker->randomFloat(2, 1, 1000),
            'currency' => 'BGN',
            'description' => $this->faker->optional()->sentence,
            'executed_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
