<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BankAccount>
 */
class BankAccountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => $this->faker->company . ' Account',
            'currency' => $this->faker->randomElement(['BGN', 'EUR', 'USD']),
            'balance' => $this->faker->randomFloat(2, 0, 10000),
            'is_active' => true,
            'is_default' => false,
        ];
    }
}
