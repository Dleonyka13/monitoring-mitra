<?php

use Illuminate\Support\Facades\Route;

// Redirect root to login
Route::get('/', function () {
    return redirect('/login');
});

// Auth routes
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

// Dashboard route
Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');