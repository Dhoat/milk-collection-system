<?php

namespace App\Policies;

use App\Models\Farmer;
use App\Models\User;

class FarmerPolicy
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
    public function view(User $user, Farmer $farmer): bool
    {
        return $user->hasAnyRole(['super_admin', 'manager', 'collection_staff']);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'manager', 'collection_staff']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Farmer $farmer): bool
    {
        return $user->hasAnyRole(['super_admin', 'manager', 'collection_staff']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Farmer $farmer): bool
    {
        return $user->hasAnyRole(['super_admin', 'manager', 'collection_staff']);
    }
}
