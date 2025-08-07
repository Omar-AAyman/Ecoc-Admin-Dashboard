<?php

namespace App\Policies;

use App\Models\User;

class ProductPolicy
{
    /**
     * Determine whether the user can view any products.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'ceo', 'engineer']);
    }

    /**
     * Determine whether the user can view a specific product.
     */
    public function view(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'ceo', 'engineer']);
    }

    /**
     * Determine whether the user can create products.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'engineer']);
    }

    /**
     * Determine whether the user can update a specific product.
     */
    public function update(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'engineer']);
    }

    /**
     * Determine whether the user can delete a specific product.
     */
    public function delete(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'engineer']);
    }
}
