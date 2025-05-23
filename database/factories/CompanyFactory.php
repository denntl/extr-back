<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\CompanyBalance;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompanyFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Company::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->text(15),
            'owner_id' => null,
            'public_id' => Str::uuid(),
            'tariff_id' => 1,
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Company $company) {
            CompanyBalance::factory()->create([
                'company_id' => $company->id,
            ]);
        });
    }
}
