<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    public function viewAny(User $user)
    {
        return $user->hasRole('super_admin');
    }

    public function view(User $user, Product $product)
    {
        return $user->hasRole('super_admin');
    }

    public function create(User $user)
    {
        return $user->hasRole('super_admin');
    }

    public function update(User $user, Product $product)
    {
        return $user->hasRole('super_admin');
    }

    public function delete(User $user, Product $product)
    {
        return $user->hasRole('super_admin');
    }
}
