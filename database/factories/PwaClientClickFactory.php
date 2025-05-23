<?php

namespace Database\Factories;

use App\Models\Application;
use App\Models\PwaClient;
use App\Models\PwaClientClick;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class PwaClientClickFactory extends Factory
{
    protected $model = PwaClientClick::class;

    public function definition()
    {
        return [
            'pwa_client_id' => PwaClient::factory(),
            'external_id' => (string) Str::uuid(),
            'ip' => $this->faker->ipv4,
            'useragent' => $this->faker->userAgent,
            'sub_1' => Str::random(8),
            'sub_2' => Str::random(8),
            'sub_3' => Str::random(8),
            'sub_4' => Str::random(8),
            'sub_5' => Str::random(8),
            'sub_6' => Str::random(8),
            'sub_7' => Str::random(8),
            'sub_8' => Str::random(8),
            'fb_p' => Str::random(),
            'fb_c' => Str::random(32),
            'pixel_id' => Str::random(),
            'pixel_key' => Str::random(64),
            'link' => $this->faker->url,
            'request_url' => $this->faker->url,
            'country' => $this->faker->countryCode,
        ];
    }
}
