<?php

namespace App\Composers;

use Illuminate\Support\Facades\Route;
use Illuminate\View\View;

class AdminSidebarComposer
{
    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        $routeMatches = [
            'admin.calendars.index' => [
                'admin.calendars.index',
                'admin.calendars.show',
                'admin.calendars.calendar-shifts.edit',
            ],
            'admin.calendars.create' => [
                'admin.calendars.create',
            ],
            'admin.sales-orders.index' => [
                'admin.sales-orders.index',
            ],
            'admin.sales-orders.create' => [
                'admin.sales-orders.create',
            ],
            'admin.company.edit' => [
                'admin.company.edit',
            ],
            'admin.application.index' => [
                'admin.application.index',
                'admin.application.edit',
            ],
            'admin.users.index' => [
                'admin.users.index',
                'admin.users.edit',
            ],
            'admin.users.create' => [
                'admin.users.create',
            ],
            'admin.locations.index' => [
                'admin.locations.index',
            ],
            'admin.locations.create' => [
                'admin.locations.create',
            ],
        ];

        $uris = [];
        $routes = [];

        foreach ($routeMatches as $route => $options) {
            foreach ($options as $value) {
                $uris[$route] = route($route);
                $routes[$value] = $route;
            }
        }

        $current = Route::currentRouteName();

        $view->with(compact('uris', 'routes', 'current'));
    }
}
