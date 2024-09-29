<?php

namespace App\Composers;

use Illuminate\View\View;

class StorageComposer
{
    /**
     * Bind data to the view.
     *
     * @param View $view
     * @return void
     */
    public function compose(View $view): void
    {
        $version = config('app.version');

        $view->with(compact('version'));
    }
}
