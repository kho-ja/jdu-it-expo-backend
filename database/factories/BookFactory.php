<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Book>
 */
class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name_kr' => fake()->name(),
            'name_uz' => fake()->name(),
            'author' => fake()->name(),
            'publisher' => fake()->name(),
            'volume' => rand(50,300),
            'main_id' => rand(1,10),
            'mid_id' => 1,
            'language_id' => rand(1,3),
            'category_id' => 1,
            'quantity' => rand(1,30),
        ];
    }
}
