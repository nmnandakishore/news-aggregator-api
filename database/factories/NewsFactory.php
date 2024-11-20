<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\News>
 */
class NewsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'image' => fake()->imageUrl(),
            'url' => fake()->url(),
            'content' => fake()->text(),
            'author' => fake()->name(),
            'provider' => fake()->company() . ' API',
            'language' => fake()->languageCode(),
            'tags' => fake()->words(3, true),
            'country' => fake()->country(),
            'published_at' => fake()->dateTimeBetween('-1 week', 'now'),
            'category' => fake()->word(),
            'source' => fake()->word(),
        ];
    }
}
