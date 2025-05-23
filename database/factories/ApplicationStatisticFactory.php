<?php

namespace Database\Factories;

use App\Models\Application;
use App\Models\ApplicationStatistic;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ApplicationStatistic>
 */
class ApplicationStatisticFactory extends Factory
{
    protected $model = ApplicationStatistic::class;

    public function definition()
    {
        return [
            'date' => $this->faker->date(),
            'application_id' => Application::factory(),
            'clicks' => $this->faker->numberBetween(0, 1000),
            'push_subscriptions' => $this->faker->numberBetween(0, 1000),
            'unique_clicks' => $this->faker->numberBetween(0, 1000),
            'installs' => $this->faker->numberBetween(0, 1000),
            'deposits' => $this->faker->numberBetween(0, 1000),
            'registrations' => $this->faker->numberBetween(0, 1000),
            'ins_to_uc' => $this->faker->randomFloat(2, 0, 1),
            'reg_to_ins' => $this->faker->randomFloat(2, 0, 1),
            'dep_to_ins' => $this->faker->randomFloat(2, 0, 1),
            'dep_to_reg' => $this->faker->randomFloat(2, 0, 1),
            'first_installs' => $this->faker->numberBetween(0, 1000),
            'repeated_installs' => $this->faker->numberBetween(0, 1000),
            'opens' => $this->faker->numberBetween(0, 1000),
            'repeated_opens' => $this->faker->numberBetween(0, 1000),
            'first_opens' => $this->faker->numberBetween(0, 1000),
        ];
    }
}
