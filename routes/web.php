<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::fallback(function () {
    return redirect(route("home"), 301);
});
