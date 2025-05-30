<?php

namespace App;

class Constants
{
    const BOOKS_PER_PAGE = 20;
    const REVIEW_PER_PAGE = 20;
    const AUTHOR_PER_PAGE = 10;
    const BOOK_SORTING = [
        'title' => 'Title',
        'newest' => 'Newest',
        'author' => 'Author',
        'popular_month' => 'Popular/1 Month',
        'popular_6_months' => 'Popular/6 Months',
        'popular_all' => 'Popular All Time',
        'ratings_month' => 'Highest Rated/1 Month',
        'ratings_6_months' => 'Highest Rated/6 Months',
        'ratings_all' => 'Highest Rated All Time',
    ];
    const BOOK_SORTING_DEFAULT = 'title';
    const REVIEW_SORTING = [
        'newest' => 'Newest',
        'oldest' => 'Oldest',
        'highest_rated' => 'Highest Rated',
        'lowest_rated' => 'Lowest Rated',
    ];
    const REVIEW_SORTING_DEFAULT = 'newest';
    const AUTHOR_SORTING = [
        'name' => 'Name',
        'newest' => 'Newest',
        'country' => 'Country',
        'book_count' => 'Book Count',
    ];
    const AUTHOR_SORTING_DEFAULT = 'name';
    const REVIEW_MIN_LENGTH = 10;
    const REVIEW_MAX_LENGTH = 2048;
    const REVIEW_MIN_RATING = 0;
    const REVIEW_MAX_RATING = 5;
    const STRING_MAX_LENGTH = 255;
    const BOOK_SUMMARY_MIN_LENGTH = 100;
    const BOOK_SUMMARY_MAX_LENGTH = 4096;
    const IMAGE_EXTENSIONS_ALLOWED = ["jpg", "png", "webp"];
    const BOOK_COVER_MAX_WEIGHT = 4096;
    const BOOK_COVER_MIN_RES = 250;
    const BOOK_COVER_MAX_RES = 1000;

    const CACHE_REVIEWS = 60*60*24; // 24 hours
    const CACHE_CATEGORIES = 60*60*24; // 24 hours

    const CATEGORIES = [
        'Romance',
        'Drama',
        'Science Fiction',
        'Fantasy',
        'Mystery',
        'Thriller',
        'Horror',
        'Historical',
        'Biography',
        'Self-Help',
        'Non-Fiction',
        'Young Adult',
        'Children',
        'Adventure',
        'Classic',
        'Poetry',
        'Graphic Novel',
        'Memoir',
        'Crime',
        'Dystopian'
    ];
    const MIN_REVIEWS_FOR_CATEGORY_COVER = 5;
    const MIN_CATEGORIES_FOR_BOOK = 1;
    const MAX_CATEGORIES_FOR_BOOK = 5;
}