<?php

namespace Database\Factories;

use App\Enums\Balance\Type;
use App\Enums\BalanceTransaction\Status;
use App\Enums\BalanceTransaction\Type as BalanceTransactionType;
use App\Models\BalanceTransaction;
use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BalanceTransactionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = BalanceTransaction::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $amount = $this->faker->randomFloat(2, 0, 1000);
        return [
            'company_id' => Company::factory(),
            'user_id' => User::factory(),
            'amount' => $amount,
            'balance_before' => 0,
            'balance_after' => $amount,
            'balance_type' => Type::Balance->value,
            'type' => BalanceTransactionType::Deposit->value,
            'status' => Status::Approved->value,
        ];
    }
}
