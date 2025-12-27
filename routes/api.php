<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\StatisticalActivityController;
use App\Http\Controllers\Api\PmlAllocationController;

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

        # Statistical Activities Management
        // GET all & GET by ID = accessible by all authenticated users
        Route::get('/kegiatan-statistik', [StatisticalActivityController::class, 'findAll']);
        Route::get('/kegiatan-statistik/statistics/summary', [StatisticalActivityController::class, 'summary']);
        Route::get('/kegiatan-statistik/{id}', [StatisticalActivityController::class, 'findById']);
        
        // Admin only routes
        Route::middleware('role:admin')->group(function () {
            Route::post('/kegiatan-statistik', [StatisticalActivityController::class, 'create']);
            Route::patch('/kegiatan-statistik/{id}', [StatisticalActivityController::class, 'update']);
            Route::delete('/kegiatan-statistik/{id}', [StatisticalActivityController::class, 'delete']);
            Route::get('/kegiatan-statistik/export/excel', [StatisticalActivityController::class, 'export']);
            Route::get('/kegiatan-statistik/template/download', [StatisticalActivityController::class, 'downloadTemplate']);
            Route::post('/kegiatan-statistik/import/excel', [StatisticalActivityController::class, 'import']);
        });

        # PML Allocations Management
        Route::get('/pml-allocations', [PmlAllocationController::class, 'findAll']);
        Route::get('/pml-allocations/statistics/summary', [PmlAllocationController::class, 'summary']);
        Route::get('/pml-allocations/check', [PmlAllocationController::class, 'checkAllocation']);
        Route::get('/pml-allocations/user/{userId}', [PmlAllocationController::class, 'findByUser']);
        Route::get('/pml-allocations/activity/{activityId}', [PmlAllocationController::class, 'findByActivity']);
        Route::get('/pml-allocations/{id}', [PmlAllocationController::class, 'findById']);
        
        // Admin only routes
        Route::middleware('role:admin')->group(function () {
            Route::post('/pml-allocations', [PmlAllocationController::class, 'create']);
            Route::post('/pml-allocations/bulk', [PmlAllocationController::class, 'bulkCreate']);
            Route::patch('/pml-allocations/{id}', [PmlAllocationController::class, 'update']);
            Route::delete('/pml-allocations/{id}', [PmlAllocationController::class, 'delete']);
            Route::delete('/pml-allocations/activity/{activityId}', [PmlAllocationController::class, 'bulkDeleteByActivity']);
            Route::delete('/pml-allocations/user/{userId}', [PmlAllocationController::class, 'bulkDeleteByUser']);
            
            // PML Export & Import
            Route::get('/pml-allocations/export/excel', [PmlAllocationController::class, 'export']);
            Route::get('/pml-allocations/template/download', [PmlAllocationController::class, 'downloadTemplate']);
            Route::post('/pml-allocations/import/excel', [PmlAllocationController::class, 'import']);           
        });
    });
});