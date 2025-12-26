<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\StatisticalActivityController;

$apiName = env('API_NAME', 'api');
$apiVersion = env('API_VERSION', 'v1');

// Prefix: /api/{api_name}/{api_version}
Route::prefix("{$apiName}/{$apiVersion}")->group(function () {
    // Public routes
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/logout-all', [AuthController::class, 'logoutAll']);

        Route::get('/me', [UserController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/logout-all', [AuthController::class, 'logoutAll']);

        // Admin only routes - User Management
        Route::middleware('role:admin')->group(function () {
            Route::get('users/template/download', [UserController::class, 'downloadTemplate']);
            Route::post('users/import', [UserController::class, 'import']);
            Route::get('users/export', [UserController::class, 'export']);
            Route::get('users', [UserController::class, 'findAll']);          
            Route::post('users', [UserController::class, 'create']);         
            Route::get('users/{id}', [UserController::class, 'findById']);
            Route::patch('users/{id}', [UserController::class, 'update']);
            Route::delete('users/{id}', [UserController::class, 'delete']);
    });

        # Statisitcal Activities Management
        //GET all & GET by ID = accessible by all authenticated users (with filtering logic in controller)
        Route::get('/kegiatan-statistik', [StatisticalActivityController::class, 'findAll']);
        Route::get('/kegiatan-statistik/{id}', [StatisticalActivityController::class, 'findById']);
        Route::middleware('role:admin')->group(function () {
            Route::post('/kegiatan-statistik', [StatisticalActivityController::class, 'create']);
            Route::patch('/kegiatan-statistik/{id}', [StatisticalActivityController::class, 'update']);
            Route::delete('/kegiatan-statistik/{id}', [StatisticalActivityController::class, 'delete']);
        });
    });
});