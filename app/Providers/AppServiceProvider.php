<?php

namespace App\Providers;

use App\Composers\AdminNavbarComposer;
use App\Composers\AdminSidebarComposer;
use App\Services\BomComparisonService;
use App\Services\BomUpversionService;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Illuminate\Database\Eloquent\Model;
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
        $this->app->singleton(BomComparisonService::class, function () {
            return new BomComparisonService();
        });

        $this->app->singleton(BomUpversionService::class, function () {
            return new BomUpversionService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::automaticallyEagerLoadRelationships();

        Route::pattern('calendar', '[0-9]+');
        Route::pattern('year', '^\d{4}$');

        View::composer('admin.layout', AdminSidebarComposer::class);
        View::composer('admin.layout', AdminNavbarComposer::class);
        View::composer('admin.layout', function ($view) {
            $view->with('breadcrumbs', Breadcrumbs::generate());
        });
    }
}
