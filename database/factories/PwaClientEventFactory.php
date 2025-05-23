<?php

namespace Database\Factories;

use App\Enums\Application\Geo;
use App\Enums\PwaEvents\Event;
use App\Enums\PwaEvents\Platform;
use App\Models\PwaClientClick;
use App\Models\PwaClientEvent;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class PwaClientEventFactory extends Factory
{
    protected $model = PwaClientEvent::class;

    public function definition()
    {
        return [
            'created_at' => now(),
            'event' => $this->faker->randomElement(Event::cases())->value,
            'details' => $this->faker->text(30),
            'is_handled' => $this->faker->boolean,
            'pwa_client_click_id' => PWAClientClick::factory(),
            'is_first' => $this->faker->boolean,
            'full_domain' => $this->faker->domainName,
            'geo' => $this->faker->randomElements(array_map(fn (Geo $item) => $item->value, Geo::cases()), 2),
            'platform' => $this->faker->randomElement(Platform::class::cases())->value,
        ];
    }
}
