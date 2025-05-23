<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\TelegraphBot;
use Illuminate\Database\Eloquent\Factories\Factory;

class TelegraphBotFactory extends Factory
{
    protected $model = TelegraphBot::class;

    public function definition()
    {
        return [
            'token' => $this->faker->uuid,
            'name' => $this->faker->word,
            'company_id' => Company::factory(),
            'is_active' => $this->faker->randomElement([false, true]),
        ];
    }
}
