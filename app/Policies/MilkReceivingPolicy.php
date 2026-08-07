<?php

namespace App\Policies;

use App\Models\MilkReceiving;
use App\Models\User;

class MilkReceivingPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'manager', 'center_staff']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, MilkReceiving $milkReceiving): bool
    {
        return $user->hasAnyRole(['super_admin', 'manager', 'center_staff']);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'manager', 'center_staff']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, MilkReceiving $milkReceiving): bool
    {
        return $user->hasAnyRole(['super_admin', 'manager', 'center_staff']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, MilkReceiving $milkReceiving): bool
    {
        return $user->hasAnyRole(['super_admin', 'manager', 'center_staff']);
    }
}
