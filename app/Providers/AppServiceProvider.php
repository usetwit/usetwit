<?php

namespace App\Providers;

use App\Composers\NavbarComposer;
use App\Composers\AdminSidebarComposer;
use App\Services\BomComparisonService;
use App\Services\BomUpversionService;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(BomComparisonService::class, function ($app) {
            return new BomComparisonService();
        });

        $this->app->singleton(BomUpversionService::class, function ($app) {
            return new BomUpversionService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Route::pattern('calendar', '[0-9]+');
        Route::pattern('year', '^\d{4}$');

        View::composer('app._sidebar', AdminSidebarComposer::class);
        View::composer('app._navbar', NavbarComposer::class);
    }
}
