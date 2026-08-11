<?php

namespace App\Policies;

use App\Models\ShopOrder;
use App\Models\User;

class ShopOrderPolicy
{
    /**
     * Determine whether the user can view any shop orders.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'manager', 'center_staff']);
    }

    /**
     * Determine whether the user can view the specific shop order.
     */
    public function view(User $user, ShopOrder $shopOrder): bool
    {
        return $user->hasAnyRole(['super_admin', 'manager', 'center_staff']);
    }

    /**
     * Determine whether the user can create a shop order.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'manager', 'center_staff']);
    }

    /**
     * Determine whether the user can update the shop order.
     */
    public function update(User $user, ShopOrder $shopOrder): bool
    {
        return $user->hasAnyRole(['super_admin', 'manager', 'center_staff']);
    }

    /**
     * Determine whether the user can update order status.
     */
    public function updateStatus(User $user, ShopOrder $shopOrder): bool
    {
        return $user->hasAnyRole(['super_admin', 'manager', 'center_staff']);
    }

    /**
     * Determine whether the user can delete the shop order.
     */
    public function delete(User $user, ShopOrder $shopOrder): bool
    {
        return $user->hasAnyRole(['super_admin', 'manager']);
    }
}
