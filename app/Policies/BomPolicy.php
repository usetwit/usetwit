<?php

namespace App\Policies;

use App\Models\User;

class BomPolicy
{
    public function view(User $user): bool
    {
        return $user->can('boms.view');
    }

    public function edit(User $user): bool
    {
        return $user->can('boms.edit');
    }
}
