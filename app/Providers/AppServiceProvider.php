<?php

namespace App\Providers;

use App\Services\ExcelService;
use App\Services\StatisticalActivityService;
use App\Services\StatisticalActivityExcelService;
use App\Services\UserService;
use App\Services\PmlAllocationService;
use App\Services\PmlAllocationExcelService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        /**
         * By passing only the class name to the singleton method, 
         * Laravel uses reflection to see what the constructor needs.
         * This fixes the "Too few arguments" error because Laravel 
         * will now automatically inject the missing dependencies.
         */

        // User Services
        $this->app->singleton(UserService::class);
        $this->app->singleton(ExcelService::class);

        // Statistical Activity Services
        $this->app->singleton(StatisticalActivityService::class);
        $this->app->singleton(StatisticalActivityExcelService::class);

        // PML Allocation Services
        $this->app->singleton(PmlAllocationService::class);
        $this->app->singleton(PmlAllocationExcelService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}