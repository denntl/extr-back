<?php

namespace Database\Factories;

use App\Enums\Application\Geo;
use App\Enums\Application\Language;
use App\Models\Application;
use App\Models\ApplicationGeoLanguage;
use Illuminate\Database\Eloquent\Factories\Factory;

class ApplicationGeoLanguageFactory extends Factory
{
    protected $model = ApplicationGeoLanguage::class;

    public function definition()
    {
        return [
            'application_id' => Application::factory(),
            'geo' => $this->faker->randomElement(Geo::cases())->value,
            'language' => $this->faker->randomElement(Language::cases())->value,
        ];
    }
}
