<?php

namespace Database\Factories;

use App\Models\News;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserPreference>
 */
class UserPreferenceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $news = News::factory()->create();
        $preferenceName = fake()->randomElement(['category', 'source', 'author']);

        $value = [
            fake()->randomElement($news->$preferenceName),
            fake()->randomElement($news->$preferenceName)
        ];

        return [
            'user_id' => User::factory(),
            'preference_name' => $preferenceName,
            'value' => $value,
        ];
    }
}
