<?php

namespace App\Policies;

use App\Models\Tank;
use App\Models\User;

class TankPolicy
{
    public function viewAny(User $user)
    {
        return $user->hasAnyRole(['super_admin', 'ceo', 'client']);
    }

    public function view(User $user, Tank $tank)
    {
        return $user->hasRole('super_admin') ||
            $user->hasRole('ceo') ||
            ($user->hasRole('client') && $tank->company_id === $user->company_id);
    }

    public function create(User $user)
    {
        return $user->hasRole('super_admin');
    }

    public function update(User $user)
    {
        return $user->hasRole('super_admin');
    }

    public function settings(User $user)
    {
        return $user->hasRole('super_admin');
    }

    public function delete(User $user)
    {
        return $user->hasRole('super_admin');
    }
}
