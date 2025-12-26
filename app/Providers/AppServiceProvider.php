<?php

namespace App\Providers;

use App\Services\ExcelService;
use App\Services\StatisticalActivityExcelService;
use App\Services\StatisticalActivityService;
use App\Services\UserService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register User Services
        $this->app->singleton(UserService::class, function ($app) {
            return new UserService();
        });

        $this->app->singleton(ExcelService::class, function ($app) {
            return new ExcelService();
        });

        // Register Statistical Activity Services
        $this->app->singleton(StatisticalActivityService::class, function ($app) {
            return new StatisticalActivityService();
        });

        $this->app->singleton(StatisticalActivityExcelService::class, function ($app) {
            return new StatisticalActivityExcelService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}