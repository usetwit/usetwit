<?php

namespace App\Policies;

use App\Models\User;

class BomPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
    }

    public function update(User $user): bool
    {
        return $user->can('boms.update');
    }
}
