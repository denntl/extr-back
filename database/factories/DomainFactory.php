<?php

namespace Database\Factories;

use App\Enums\Domain\Status;
use App\Models\Domain;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class DomainFactory extends Factory
{
    protected $model = Domain::class;

    public function definition()
    {
        return [
            'domain' => $this->faker->domainName,
            'status' => $this->faker->randomElement(Status::cases())->value,
            'took_at' => $this->faker->optional()->dateTime,
            'banned_at' => $this->faker->optional()->dateTime,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
