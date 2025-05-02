<?php

namespace Database\Seeders;

use App\Constants;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Book;
use App\Models\User;
use App\Models\Author;
use App\Models\Review;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        define('USER_COUNT', 250);
        define('AUTHOR_COUNT', 250);

        $authors = Author::factory()->count(AUTHOR_COUNT)->create()->each(function ($author) {
            Book::factory()->count(random_int(1, 10))->for($author, 'author')->create();
        });

        //Create a user with the name "John Doe" and assign them 10 books
        $johnDoe = User::factory()->johnDoe()->create();

        Book::factory()->count(10)->for($johnDoe, 'user')->create();

        $books = Book::select('id')->get();

        //Our user's reviews
        foreach ($books->shuffle()->slice(0, 20) as $book) {
            Review::factory()->for($book, 'book')->for($johnDoe, 'user')->create();
        }

        //Generic reviews
        for ($i = 0; $i < USER_COUNT; $i++) {
            $user = User::factory()->create();

            foreach ($books->shuffle()->slice(0, random_int(5, 30)) as $book) {
                Review::factory()->for($book, 'book')->for($user, 'user')->create();
            }
        }

        $users = User::select('id')->get();

        //Books with good reviews
        Book::factory()->count(20)->create()->each(function ($book) use ($users) {
            foreach ($users->shuffle()->slice(0, random_int(10, 20)) as $user) {
                Review::factory()->goodBook()->for($book, 'book')->for($user, 'user')->create();
            }
        });

        //Books with bad reviews
        Book::factory()->count(20)->create()->each(function ($book) use ($users) {
            foreach ($users->shuffle()->slice(0, random_int(10, 20)) as $user) {
                Review::factory()->badBook()->for($book, 'book')->for($user, 'user')->create();
            }
        });

        foreach (Constants::CATEGORIES as $name) {
            $category = new Category();
            $category->name = $name;
            $category->slug = Str::slug($name);
            $category->save();
        }

        $categoryIDs = range(1, count(Constants::CATEGORIES));

        foreach (Book::select('id')->get() as $book) {
            shuffle($categoryIDs);

            $book->categories()->attach(array_slice($categoryIDs, 0, random_int(1, 5)));
        }
    }
}
