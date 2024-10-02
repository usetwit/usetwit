<?php

namespace App\Composers;

use Illuminate\View\View;

class NavbarComposer
{
    /**
     * Bind data to the view.
     *
     * @param View $view
     *
     * @return void
     */
    public function compose(View $view): void
    {
        $route = route('auth.logout');

        $view->with(compact('route'));
    }
}
