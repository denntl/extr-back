<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;


class PermissionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Permission::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => Str::camel($this->faker->name(15)),
            'guard_name' => 'web',
        ];
    }
}
