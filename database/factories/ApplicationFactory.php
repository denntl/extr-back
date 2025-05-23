<?php

namespace Database\Factories;

use App\Enums\Application\Geo;
use App\Enums\Application\PlatformType;
use App\Enums\Application\LandingType;
use App\Enums\Application\Status;
use App\Enums\Application\WhiteType;
use App\Models\Application;
use App\Models\Company;
use App\Models\Domain;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ApplicationFactory extends Factory
{
    protected $model = Application::class;

    public function definition()
    {
        return [
            'status' => $this->faker->randomElement(Status::cases())->value,
            'company_id' => Company::factory(),
            'created_by_id' => User::factory(),
            'owner_id' => User::factory(),
            'uuid' => $this->faker->uuid,
            'name' => Str::random(10),
            'domain_id' => Domain::factory(),
            'subdomain' => $this->faker->domainWord,
            'pixel_id' => $this->faker->uuid,
            'pixel_key' => $this->faker->sha256,
            'link' => $this->faker->url,
            'platform_type' => $this->faker->randomElement(PlatformType::cases())->value,
            'landing_type' => $this->faker->randomElement(LandingType::cases())->value,
            'white_type' => $this->faker->randomElement(WhiteType::cases())->value,
            'category' => $this->faker->numberBetween(1, 10),
            'app_name' => $this->faker->word,
            'developer_name' => $this->faker->name,
            'icon' => $this->faker->imageUrl,
            'description' => $this->faker->optional()->text,
            'downloads_count' => $this->faker->numberBetween(0, 1000000),
            'rating' => $this->faker->randomFloat(2, 0, 5),
            'onesignal_id' => null,
            'onesignal_name' => $this->faker->optional()->word,
            'onesignal_auth_key' => null,
            'display_top_bar' => $this->faker->boolean,
            'display_app_bar' => $this->faker->boolean,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
