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

// Dashboard route (accessible to all authenticated users)
Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');


// Admin only routes
Route::prefix('admin')->name('admin.')->group(function () {
    // User Management
    Route::get('/users', function () {
        return view('admin.users');
    })->name('users');
    
    // Statistical Activities Management
    Route::get('/kegiatan-statistik', function () {
        return view('admin.statistical-activities');
    })->name('kegiatan-statistik');
});