<?php

namespace Database\Seeders;

use Database\Factories\NewsFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NewsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 1; $i < 100; $i++) {
            DB::table('news')->insert([
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
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
