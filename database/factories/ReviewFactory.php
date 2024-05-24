<?php

namespace Database\Factories;

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
            'book_id' => null,
            'review' => $this->faker->paragraphs(3, true),
            'rating' => $this->faker->numberBetween(1, 5),
            'ip_address' => $this->faker->ipv4(),
            'created_at' => $this->faker->dateTimeBetween('-2 years'),
            'updated_at'=> function(array $attributes) {
                return $this->faker->dateTimeBetween($attributes['created_at']);
            },
        ];
    }

    public function good(): static {
        return $this->state(function(array $attributes) {
            return [
                'rating' => $this->faker->numberBetween(4, 5),
            ];
        });
    }

    public function bad(): static {
        return $this->state(function(array $attributes) {
            return [
                'rating' => $this->faker->numberBetween(1, 2),
            ];
        });
    }
}
