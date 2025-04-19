<?php

namespace Database\Seeders;

use App\Models\Book;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use App\Models\Review;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        //Generic books & reviews
        Book::factory()->count(100)->create()->each(function ($book) {
            $book->reviews()->saveMany(Review::factory()->count(random_int(2, 6))->make());
        });

        //Books with good reviews
        Book::factory()->count(10)->create()->each(function ($book) {
            $book->reviews()->saveMany(Review::factory()->goodBook()->count(random_int(2, 6))->make());
        });

        //Books with bad reviews
        Book::factory()->count(10)->create()->each(function ($book) {
            $book->reviews()->saveMany(Review::factory()->badBook()->count(random_int(2, 6))->make());
        });

        //Our user's books & reviews
        $johnDoe = User::factory()->johnDoe()->create();

        Book::factory()->count(10)->create()->each(function ($book) use ($johnDoe) {
            $book->reviews()->saveMany(Review::factory()->for($johnDoe, 'user')->count(random_int(2, 6))->make());
        });
    }
}
