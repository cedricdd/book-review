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
        define('BOOK_COUNT', 500);
        define('USER_COUNT', 100);

        $books = Book::factory()->count(BOOK_COUNT)->create();

        //Generic books & reviews
        for ($i = 0; $i < USER_COUNT; $i++) {
            $user = User::factory()->create();

            foreach ($books->shuffle()->slice(0, random_int(1, 25)) as $book) {
                Review::factory()->for($book, 'book')->for($user, 'user')->create();
            }
        }

        $users = User::select('id')->get();

        //Books with good reviews
        Book::factory()->count(20)->create()->each(function ($book) use ($users) {
            foreach ($users->shuffle()->slice(0, random_int(6, 12)) as $user) {
                Review::factory()->goodBook()->for($book, 'book')->for($user, 'user')->create();
            }
        });

        //Books with bad reviews
        Book::factory()->count(20)->create()->each(function ($book) use ($users) {
            foreach ($users->shuffle()->slice(0, random_int(6, 12)) as $user) {
                Review::factory()->badBook()->for($book, 'book')->for($user, 'user')->create();
            }
        });

        //Our user's books & reviews
        $johnDoe = User::factory()->johnDoe()->create();

        foreach ($books->shuffle()->slice(0, random_int(10, 20)) as $book) {
            Review::factory()->for($book, 'book')->for($johnDoe, 'user')->create();
        }
    }
}
