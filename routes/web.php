<?php

use App\Http\Controllers\BookController;
use Illuminate\Support\Facades\Route;

Route::get('/', [BookController::class, 'index'])->name('books.index');
Route::get('/books/show/{book}', [BookController::class, 'show'])->name('books.show');
