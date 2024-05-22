<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;

Route::resource('books', BookController::class)->only(['index','show']);
Route::resource('books.reviews', ReviewController::class)->except(['index', 'show']);

Route::fallback(function () {
    return redirect(route("books.index"), 301);
});
