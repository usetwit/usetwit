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
                'label' => 'Locations',
                'icon' => 'pi pi-map-marker',
                'links' => [
                    [
                        'type' => 'link',
                        'label' => 'All Locations',
                        'route' => 'locations.index',
                    ],
                    [
                        'type' => 'link',
                        'label' => 'Create Location',
                        'route' => 'locations.create',
                    ],
                ],
            ],
            [
                'label' => 'Settings',
                'icon' => 'pi pi-cog',
                'links' => [
                    [
                        'type' => 'heading',
                        'label' => 'Settings',
                    ],
                    [
                        'type' => 'link',
                        'label' => 'Company Settings',
                        'route' => 'company.edit',
                    ],
                    [
                        'type' => 'link',
                        'label' => 'Application Settings',
                        'route' => 'application.index',
                    ],
                ],
            ],
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
            [
                'label' => 'Sales Orders',
                'icon' => 'pi pi-dollar',
                'links' => [
                    [
                        'type' => 'link',
                        'label' => 'All Sales Orders',
                        'route' => 'sales-orders.index',
                        'matches' => [
                            'sales-orders.edit',
                        ],
                    ],
                    [
                        'type' => 'link',
                        'label' => 'Create Sales Order',
                        'route' => 'sales-orders.create',
                    ],
                ],
            ],
        ];

        $current = Route::currentRouteName();

        foreach ($items as $i => &$item) {
            $item['expanded'] = false;
            $item['id'] = $i;

            foreach ($item['links'] as $j => &$link) {
                $link['id'] = "{$i}-{$j}";

                if ($link['type'] !== 'link') {
                    continue;
                }

                $link['link'] = route("admin.{$link['route']}");

                $matches = $link['matches'] ?? [];
                $matches[] = $link['route'];
                $prefixedMatches = array_map(fn($m) => "admin.$m", $matches);

                $link['active'] = in_array($current, $prefixedMatches, true);

                if (! $item['expanded'] && $link['active']) {
                    $item['expanded'] = true;
                }

                unset($link['matches'], $link['route']);
            }
        }

        $view->with(compact('items'));
    }
}
