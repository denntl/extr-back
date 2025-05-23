<?php

namespace Database\Factories;

use App\Models\Tier;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TierFactory extends Factory
{
    protected $model = Tier::class;

    public function definition()
    {
        return [
            'name' => Str::random(10),
            'tariff_id' => 1,
            'price' => 0.4,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
