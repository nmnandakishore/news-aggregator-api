<?php

namespace Database\Seeders;

use Database\Factories\UserPreferenceFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserPreferenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        UserPreferenceFactory::factory()->count(10)->create();
    }
}
