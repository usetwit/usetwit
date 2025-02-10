<?php

namespace App\Providers;

use App\Composers\NavbarComposer;
use App\Composers\AdminSidebarComposer;
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
        //
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
