<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

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
            Route::apiResource('users', UserController::class);
        });
    });
});