<?php

use App\Http\Controllers\BookController;
use Illuminate\Support\Facades\Route;

Route::resource('books', BookController::class);

Route::fallback(function () {
    return redirect(route("books.index"), 301);
});
