<?php

namespace App\Providers;

use App\Contracts\Services\SaleProcessingServiceInterface;
use App\Services\SaleProcessingService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(SaleProcessingServiceInterface::class, SaleProcessingService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
