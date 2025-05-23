<?php

namespace Database\Factories;

use App\Enums\Invite\ActionName;
use App\Models\Company;
use App\Models\Invite;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Invite>
 */
class InviteFactory extends Factory
{
    protected $model = Invite::class;

    public function definition(): array
    {
        return [
            'key' => Str::uuid(),
            'expire_at' => $this->faker->dateTimeBetween('now', '+1 month'),
            'company_id' => Company::factory(),
            'action' => $this->faker->randomElement(ActionName::cases())->value,
            'body' => [],
            'created_by' => User::factory(),
        ];
    }
}
