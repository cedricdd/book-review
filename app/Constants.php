<?php

namespace App;

class Constants
{
    const BOOKS_PER_PAGE = 20;
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
}