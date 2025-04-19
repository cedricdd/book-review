<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Review>
 */
class ReviewFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'review' => fake()->paragraph(),
            'rating' => round(fake()->randomFloat(2, 0, 5) * 2) / 2,
            'user_id' => User::factory(),
            'book_id' => Book::factory(),
            'created_at' => fake()->dateTimeBetween('-1 year', 'now'),
            'updated_at' => fake()->dateTimeBetween('created_at', 'now'),
        ];
    }

    public function goodBook(): static
    {
        return $this->state(fn (array $attributes) => [
            'rating' => round(fake()->randomFloat(2, 4, 5) * 2) / 2,
        ]);
    }

    public function badBook(): static
    {
        return $this->state(fn (array $attributes) => [
            'rating' => round(fake()->randomFloat(2, 0, 2) * 2) / 2,
        ]);
    }
}
