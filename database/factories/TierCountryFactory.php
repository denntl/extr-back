<?php

namespace Database\Factories;

use App\Enums\Application\Geo;
use App\Models\TierCountry;
use Illuminate\Database\Eloquent\Factories\Factory;

class TierCountryFactory extends Factory
{
    protected $model = TierCountry::class;

    public function definition()
    {
        return [
            'country' => $this->faker->optional()->randomElements(array_map(fn (Geo $item) => $item->value, Geo::cases())),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
