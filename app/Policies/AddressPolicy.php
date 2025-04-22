<?php

namespace App\Policies;

use App\Models\Address;
use App\Models\Customer;
use App\Models\User;

class AddressPolicy
{
    /**
     * Determine whether the user can update a User address.
     */
    public function updateUserAddress(User $user, Address $address): bool
    {
        if ($user->can('addresses.user.update')) {
            return true;
        }

        if ($user->can('addresses.user.update.self')) {
            return $address->addressable instanceof User && $address->addressable->is($user);
        }

        return false;
    }

    /**
     * Determine whether the user can update a User address.
     */
    public function updateCustomerAddress(User|Customer $user, Address $address): bool
    {
        if ($user->can('addresses.customer.update')) {
            return true;
        }

        if ($user->can('addresses.customer.update.self')) {
            return $address->addressable instanceof Customer && $address->addressable->is($user);
        }

        return false;
    }
}
