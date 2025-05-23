<?php

namespace Database\Factories;

use App\Models\Geo;
use App\Models\OnesignalTemplate;
use App\Models\OnesignalTemplateContents;
use Illuminate\Database\Eloquent\Factories\Factory;

class OnesignalTemplateContentsFactory extends Factory
{
    protected $model = OnesignalTemplateContents::class;

    public function definition(): array
    {
        return [
            'onesignal_template_id' => OnesignalTemplate::factory(),
            'geo_id' => Geo::factory(),
            'title' => $this->faker->word,
            'text' => $this->faker->text(),
            'image' => $this->faker->imageUrl(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
