<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\CompanyBalance;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompanyBalanceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CompanyBalance::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'balance' => 0,
            'balance_bonus' => 0,
        ];
    }
}
