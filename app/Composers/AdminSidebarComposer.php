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
        $items = [
            [
                'label' => 'Calendars',
                'icon' => 'pi pi-calendar',
                'links' => [
                    [
                        'type' => 'link',
                        'label' => 'All Calendars',
                        'route' => 'calendars.index',
                    ],
                    [
                        'type' => 'link',
                        'label' => 'Create Calendar',
                        'route' => 'calendars.create',
                    ],
                ],
            ],
            [
                'label' => 'Users',
                'icon' => 'pi pi-users',
                'links' => [
                    [
                        'type' => 'heading',
                        'label' => 'Users',
                    ],
                    [
                        'type' => 'link',
                        'label' => 'All Users',
                        'route' => 'users.index',
                        'matches' => [
                            'users.edit',
                        ],
                    ],
                    [
                        'type' => 'link',
                        'label' => 'Create User',
                        'route' => 'users.create',
                    ],
                ],
            ],
        ];

        foreach ($items as &$item) {
            $item['expanded'] = false;

            foreach ($item['links'] as &$link) {
                if ($link['type'] === 'link') {
                    $link['link'] = route("admin.{$link['route']}");

                    if (! isset($link['matches'])) {
                        $link['matches'] = [$link['route']];
                    } else {
                        $link['matches'][] = $link['route'];
                    }

                    foreach ($link['matches'] as &$match) {
                        $match = "admin.{$match}";
                    }

                    if (! $item['expanded'] && in_array(Route::currentRouteName(), $link['matches'])) {
                        $item['expanded'] = true;
                    }

                    unset($link['matches'], $link['route']);
                }
            }
        }

        $view->with(compact('items'));
    }
}
