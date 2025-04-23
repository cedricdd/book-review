<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\SiteController;
use Illuminate\Support\Facades\Route;

Route::get('/', [BookController::class, 'index'])->name('books.index');
Route::get('books/show/{book}', [BookController::class, 'show'])->name('books.show');

Route::post('sorting/{type}', [SiteController::class, 'sorting'])->name('sorting')->where('type', '[a-z]+');

Route::fallback(fn() => redirect()->route("books.index"));
