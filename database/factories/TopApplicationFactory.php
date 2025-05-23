<?php

namespace Database\Factories;

use App\Models\Application;
use App\Models\TopApplication;
use Illuminate\Database\Eloquent\Factories\Factory;

class TopApplicationFactory extends Factory
{
    protected $model = TopApplication::class;

    public function definition()
    {
        return [
            'parent_application_id' => Application::factory(),
            'child_application_id' => Application::factory(),
        ];
    }
}
