<?php

namespace App\Composers;

use Illuminate\Support\Facades\Route;
use Illuminate\View\View;

class SidebarComposer
{
    /**
     * Bind data to the view.
     *
     * @param View $view
     * @return void
     */
    public function compose(View $view): void
    {
        $routeMatches = [
            'calendars.index' => [
                'calendars.index',
                'calendars.show',
                'calendars.calendar-shifts.edit'
            ],
            'calendars.create' => ['calendars.create'],
            'sales-orders.index' => ['sales-orders.index'],
            'sales-orders.create' => ['sales-orders.create'],
            'company.index' => [
                'company.index',
                'company.edit',
            ],
            'application.index' => [
                'application.index',
                'application.edit',
            ],
            'users.index' => ['users.index'],
            'users.create' => ['users.create'],
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
