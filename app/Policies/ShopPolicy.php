<?php

namespace App\Policies;

use App\Models\Shop;
use App\Models\User;

class ShopPolicy
{
    /**
     * Determine whether the user can view any shops.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'manager', 'center_staff']);
    }

    /**
     * Determine whether the user can view the specific shop.
     */
    public function view(User $user, Shop $shop): bool
    {
        return $user->hasAnyRole(['super_admin', 'manager', 'center_staff']);
    }

    /**
     * Determine whether the user can create a shop.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'manager']);
    }

    /**
     * Determine whether the user can update the shop.
     */
    public function update(User $user, Shop $shop): bool
    {
        return $user->hasAnyRole(['super_admin', 'manager']);
    }

    /**
     * Determine whether the user can delete the shop.
     */
    public function delete(User $user, Shop $shop): bool
    {
        return $user->hasAnyRole(['super_admin', 'manager']);
    }
}
