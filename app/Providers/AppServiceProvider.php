<?php

namespace App\Providers;

use App\Composers\SidebarComposer;
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

        View::composer('app._sidebar', SidebarComposer::class);
    }
}
