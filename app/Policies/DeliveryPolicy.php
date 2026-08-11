<?php

namespace App\Policies;

use App\Models\Delivery;
use App\Models\User;

class DeliveryPolicy
{
    /**
     * Determine whether the user can view any deliveries.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'manager', 'center_staff']);
    }

    /**
     * Determine whether the user can view the specific delivery.
     */
    public function view(User $user, Delivery $delivery): bool
    {
        return $user->hasAnyRole(['super_admin', 'manager', 'center_staff']);
    }

    /**
     * Determine whether the user can create a delivery.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'manager', 'center_staff']);
    }

    /**
     * Determine whether the user can update the delivery details.
     */
    public function update(User $user, Delivery $delivery): bool
    {
        return $user->hasAnyRole(['super_admin', 'manager', 'center_staff']);
    }

    /**
     * Determine whether the user can change delivery status or assign staff.
     */
    public function updateStatus(User $user, Delivery $delivery): bool
    {
        return $user->hasAnyRole(['super_admin', 'manager', 'center_staff']);
    }

    /**
     * Determine whether the user can delete the delivery record.
     */
    public function delete(User $user, Delivery $delivery): bool
    {
        return $user->hasAnyRole(['super_admin', 'manager']);
    }
}
