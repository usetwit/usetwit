<?php

namespace App\Composers;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NavbarComposer
{
    /**
     * Bind data to the view.
     *
     * @param View $view
     * @return void
     */
    public function compose(View $view): void
    {
        $user = Auth::user();
        $name = $user->full_name;
        $image = $user->profileImages()->default()->first();
        $role = $user->getRoleNames()->first();

        $userProfileImage = null;
        if ($image) {
            $userProfileImage = asset("images/user/profile/{$user->id}/{$image->filename}");
        }

        $view->with(compact('userProfileImage', 'name', 'role'));
    }
}
