<?php

namespace Database\Factories;

use App\Enums\Application\Geo;
use App\Enums\PushNotification\Status;
use App\Enums\PushNotification\Type;
use App\Enums\PushTemplate\Event;
use App\Models\Application;
use App\Models\PushNotification;
use App\Models\PushTemplate;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PushNotificationFactory extends Factory
{
    protected $model = PushNotification::class;

    public function definition()
    {
        return [
            'application_id' => Application::factory(),
            'push_template_id' => PushTemplate::factory(),
            'created_by' => User::factory(),
            'type' => $this->faker->randomElement(array_map(fn($case) => $case->value, Type::cases())),
            'status' => $this->faker->randomElement(array_map(fn($case) => $case->value, Status::cases())),
            'is_active' => $this->faker->boolean,
            'is_delayed' => $this->faker->boolean,
            'name' => $this->faker->word,
            'date' => $this->faker->date,
            'time' => $this->faker->time,
            'geo' => $this->faker->randomElements(array_map(fn($case) => $case->value, Geo::cases())),
            'events' => $this->faker->randomElements(array_map(fn($case) => $case->value, Event::cases())),
            'title' => $this->faker->word,
            'content' => $this->faker->text,
            'icon' => $this->faker->url,
            'image' => $this->faker->url,
            'link' => $this->faker->url,
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null,
        ];
    }
}
