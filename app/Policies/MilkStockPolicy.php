<?php

namespace App\Policies;

use App\Models\MilkStock;
use App\Models\User;

class MilkStockPolicy
{
    /**
     * Determine whether the user can view any stock records.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'manager', 'center_staff']);
    }

    /**
     * Determine whether the user can view the specific stock record.
     */
    public function view(User $user, MilkStock $milkStock): bool
    {
        return $user->hasAnyRole(['super_admin', 'manager', 'center_staff']);
    }

    /**
     * Determine whether the user can record a Stock OUT transaction.
     */
    public function createOut(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'manager', 'center_staff']);
    }
}
