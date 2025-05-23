<?php

namespace Database\Factories;

use App\Models\OnesignalTemplate;
use App\Models\OnesignalTemplateSingleSettings;
use Illuminate\Database\Eloquent\Factories\Factory;

class OnesignalTemplateSingleSettingsFactory extends Factory
{
    protected $model = OnesignalTemplateSingleSettings::class;

    public function definition(): array
    {
        return [
            'onesignal_template_id' => OnesignalTemplate::factory(),
            'scheduled_at' => $this->faker->date('Y-m-d H:m:s'),
            'handled_at' => null
        ];
    }
}
