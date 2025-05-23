<?php

namespace Database\Factories;

use App\Models\Application;
use App\Models\ApplicationComment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ApplicationComment>
 */
class ApplicationCommentFactory extends Factory
{
    protected $model = ApplicationComment::class;

    public function definition()
    {
        return [
            'author_name' => $this->faker->name,
            'text' => $this->faker->paragraph,
            'stars' => $this->faker->numberBetween(1, 5),
            'lang' => $this->faker->languageCode,
            'icon' => $this->faker->imageUrl,
            'created_by' => User::factory(),
            'answer' => $this->faker->optional()->paragraph,
            'date' => $this->faker->optional()->date,
            'application_id' => null,
            'origin_id' => $this->faker->optional()->randomDigitNotNull,
            'likes' => $this->faker->numberBetween(0, 100),
            'answer_author' => $this->faker->optional()->name,
        ];
    }
}
