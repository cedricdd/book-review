<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Review;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(1)->myself()->create();
        // User::factory(10)->create();

        Book::factory(20)->create()->each(function ($book) {
            $count = random_int(5, 20);

            Review::factory($count)->good()->for($book)->create();
        });
        Book::factory(20)->create()->each(function ($book) {
            $count = random_int(5, 20);

            Review::factory($count)->bad()->for($book)->create();
        });
        Book::factory(60)->create()->each(function ($book) {
            $count = random_int(5, 20);

            Review::factory($count)->for($book)->create();
        });
    }
}
