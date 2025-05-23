<?php

namespace Database\Factories;

use App\Enums\Application\Geo;
use App\Enums\PushTemplate\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PushTemplateFactory extends Factory
{
    protected $model = \App\Models\PushTemplate::class;
    public function definition()
    {
        return [
            'name' => $this->faker->word,
            'geo' => $this->faker->randomElements(array_map(fn($case) => $case->value, Geo::cases())),
            'events' => $this->faker->randomElements(array_map(fn($case) => $case->value, Event::cases())),
            'created_by' => User::factory(),
            'is_active' => $this->faker->boolean,
            'title' => $this->faker->word,
            'content' => $this->faker->text,
            'icon' => $this->faker->url,
            'image' => $this->faker->url,
            'link' => $this->faker->url,
        ];
    }
}
