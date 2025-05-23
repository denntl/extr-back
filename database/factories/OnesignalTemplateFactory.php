<?php

namespace Database\Factories;

use App\Enums\PushNotification\Type;
use App\Enums\PushTemplate\Event;
use App\Models\OnesignalTemplate;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OnesignalTemplateFactory extends Factory
{
    protected $model = OnesignalTemplate::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word,
            'type' => $this->faker->randomElement(array_map(fn($case) => $case->value, Type::cases())),
            'is_active' => $this->faker->boolean,
            'segments' => $this->faker->randomElements(array_map(fn($case) => $case->value, Event::cases())),
            'created_by' => User::factory(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
