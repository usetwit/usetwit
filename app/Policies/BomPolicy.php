<?php

namespace App\Policies;

use App\Models\User;

class BomPolicy
{
    public function update(User $user): bool
    {
        return $user->can('boms.update');
    }
}
