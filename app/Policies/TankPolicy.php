<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Tank;

class TankPolicy
{
    public function viewAny(User $user)
    {
        return $user->hasAnyRole(['super_admin', 'engineer', 'ceo', 'client']);
    }

    public function view(User $user, Tank $tank)
    {
        return $user->hasRole('super_admin') ||
            $user->hasRole('engineer') ||
            $user->hasRole('ceo') ||
            ($user->hasRole('client') && $user->company_id === $tank->company_id);
    }

    public function create(User $user)
    {
        return $user->hasAnyRole(['super_admin', 'engineer', 'ceo']);
    }

    public function update(User $user, Tank $tank = null)
    {
        return $user->hasAnyRole(['super_admin', 'engineer', 'ceo']);
    }

    public function delete(User $user, Tank $tank = null)
    {
        return $user->hasRole('super_admin');
    }
}
