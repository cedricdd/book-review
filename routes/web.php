<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReviewController;

Route::get('/', [BookController::class, 'index'])->name('books.index');
Route::get('books/show/{book}', [BookController::class, 'show'])->name('books.show');

Route::get('reviews/{review}/edit', [ReviewController::class, 'edit'])->name('reviews.edit')->middleware('auth')->can('update', 'review');
Route::delete('reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy')->middleware('auth')->can('destroy', 'review');

Route::post('sorting/{type}', [SiteController::class, 'sorting'])->name('sorting')->where('type', '[a-z]+');

Route::get('users/{user}', [UserController::class, 'profile'])->name('users.profile');

Route::get('register', [UserController::class, 'create'])->name('register')->middleware('guest');

Route::get('login', [UserController::class, 'login'])->name(('login'))->middleware('guest');
Route::post('login', [UserController::class, 'loginPost'])->name(('loginPost'))->middleware('guest');
Route::delete('logout', [UserController::class, 'logout'])->name(('logout'))->middleware('auth');

Route::fallback(fn() => redirect()->route("books.index"));
