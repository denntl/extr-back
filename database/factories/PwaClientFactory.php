<?php

namespace Database\Factories;

use App\Enums\User\Status;
use App\Models\Application;
use App\Models\Company;
use App\Models\PwaClient;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class PwaClientFactory extends Factory
{
    protected $model = PwaClient::class;

    public function definition()
    {
        return [
            'application_id' => Application::factory(),
            'external_id' => (string)Str::uuid(),
        ];
    }
}
