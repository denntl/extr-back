<?php

namespace Database\Factories;

use App\Models\Geo;
use App\Enums\Application\Geo as GeoEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

class GeoFactory extends Factory
{
    protected $model = Geo::class;

    public function definition(): array
    {
        return [
            'code' => $this->faker->unique()->randomElement(GeoEnum::cases())->value,
        ];
    }
}
