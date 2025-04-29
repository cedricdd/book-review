<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Author>
 */
class AuthorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name,
            'biography' => fake()->text(500),
            'date_of_birth' => fake()->date(max: '-20 years'),
            'nationality' => fake()->country,
            'website' => fake()->url,
        ];
    }
}
