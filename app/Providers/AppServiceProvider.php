<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\ClinicalCalculationService;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ClinicalCalculationService::class);
    }

    public function boot(): void
    {
    }
}
