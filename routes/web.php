<?php

use App\Http\Controllers\AuthorController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReviewController;

Route::get('/', [BookController::class, 'index'])->name('books.index');
Route::get('books/create', [BookController::class, 'create'])->name('books.create')->middleware('auth');
Route::post('books', [BookController::class, 'store'])->name('books.store')->middleware('auth');
Route::get('books/owner', [BookController::class, 'owner'])->name('books.owner')->middleware('auth');
Route::get('books/edit/{book}', [BookController::class, 'edit'])->name('books.edit')->middleware('auth')->can('update', 'book');
Route::put('books/{book}', [BookController::class, 'update'])->name('books.update')->middleware('auth')->can('update', 'book');
Route::get('books/{book}', [BookController::class, 'show'])->name('books.show');
Route::delete('books/{book}', [BookController::class, 'destroy'])->name('books.destroy')->middleware('auth')->can('destroy', 'book');

Route::get('books/{book}/reviews/create', [ReviewController::class, 'create'])->name('reviews.create')->middleware('auth');
Route::post('books/{book}/reviews', [ReviewController::class, 'store'])->name('reviews.store')->middleware('auth');
Route::get('books/{book}/reviews/{review}/edit', [ReviewController::class, 'edit'])->name('reviews.edit')->middleware('auth')->can('update', 'review');
Route::put('books/{book}/reviews/{review}', [ReviewController::class, 'update'])->name('reviews.update')->middleware('auth')->can('update', 'review');
Route::delete('reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy')->middleware('auth')->can('destroy', 'review');

Route::get('authors', [AuthorController::class, 'index'])->name('authors.index');
Route::get('authors/{author}', [AuthorController::class, 'show'])->name('authors.show');

Route::post('sorting/{type}', [SiteController::class, 'sorting'])->name('sorting')->where('type', '[a-z]+');

Route::get('users/{user}', [UserController::class, 'profile'])->name('users.profile');

Route::get('login', [UserController::class, 'login'])->name(('login'))->middleware('guest');
Route::post('login', [UserController::class, 'loginPost'])->name(('loginPost'))->middleware('guest');
Route::delete('logout', [UserController::class, 'logout'])->name(('logout'))->middleware('auth');

Route::fallback(fn() => redirect()->route("books.index"));
