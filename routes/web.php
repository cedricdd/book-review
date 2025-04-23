<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\UserController;

Route::get('/', [BookController::class, 'index'])->name('books.index');
Route::get('books/show/{book}', [BookController::class, 'show'])->name('books.show');

Route::post('sorting/{type}', [SiteController::class, 'sorting'])->name('sorting')->where('type', '[a-z]+');

Route::get('users/{user}', [UserController::class, 'profile'])->name('users.show');

Route::fallback(fn() => redirect()->route("books.index"));
