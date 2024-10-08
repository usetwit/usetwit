<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('users.view') || $user->can('users.update');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        if ($user->can('users.view')) {
            return true;
        }

        if ($user->can('users.view.self')) {
            return $user->id === $model->id;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('users.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        return $user->can('users.update') || $user->can('updatePersonalProfile', $model) || $user->can('updateAddress',
                $model) || $user->can('updateProfileImage', $model) || $user->can('updateCompanyProfile', $model);
    }

    /**
     * Determine whether the user can update the model profile.
     */
    public function updatePersonalProfile(User $user, User $model): bool
    {
        if ($user->can('users.update')) {
            return true;
        }

        if ($user->can('users.update.self.personal-profile')) {
            return $user->id === $model->id;
        }

        return false;
    }

    /**
     * Determine whether the user can update the model profile.
     */
    public function updateCompanyProfile(User $user, User $model): bool
    {
        if ($user->can('users.update')) {
            return true;
        }

        if ($user->can('users.update.self.company-profile')) {
            return $user->id === $model->id;
        }

        return false;
    }

    /**
     * Determine whether the user can update the model profile image.
     */
    public function updateProfileImage(User $user, User $model): bool
    {
        if ($user->can('users.update')) {
            return true;
        }

        if ($user->can('users.update.self.profile-image')) {
            return $user->id === $model->id;
        }

        return false;
    }

    /**
     * Determine whether the user can update the model profile image.
     */
    public function updateProtectedInfo(User $user): bool
    {
        return $user->can('users.update');
    }

    /**
     * Determine whether the user can update the model profile image.
     */
    public function updateAddress(User $user, User $model): bool
    {
        if ($user->can('users.update')) {
            return true;
        }

        if ($user->can('users.update.self.address')) {
            return $user->id === $model->id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user): bool
    {
        return $user->can('users.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user): bool
    {
        return $user->can('users.restore');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        return false;
    }
}
