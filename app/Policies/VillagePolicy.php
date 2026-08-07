<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Village;

class VillagePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'manager', 'collection_staff']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Village $village): bool
    {
        return $user->hasAnyRole(['super_admin', 'manager', 'collection_staff']);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'manager']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Village $village): bool
    {
        return $user->hasAnyRole(['super_admin', 'manager']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Village $village): bool
    {
        return $user->hasAnyRole(['super_admin', 'manager']);
    }
}
