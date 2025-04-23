<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

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
            'title' => fake()->sentence(3),
            'author' => fake()->name(),
            'published_at' => fake()->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'summary' => fake()->text(200),
            'cover_image' => "https://picsum.photos/seed/" . rand(1, 1000000) . "/600/800",
        ];
    }
}
