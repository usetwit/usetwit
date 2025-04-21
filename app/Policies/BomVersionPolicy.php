<?php

namespace App\Policies;

use App\Models\User;

class BomVersionPolicy
{
    public function update(User $user): bool
    {
        return $user->can('boms-versions.update');
    }
}
