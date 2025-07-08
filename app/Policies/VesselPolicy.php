<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vessel;

class VesselPolicy
{
    public function viewAny(User $user)
    {
        return $user->hasRole('super_admin');
    }

    public function view(User $user)
    {
        return $user->hasRole('super_admin');
    }

    public function create(User $user)
    {
        return $user->hasRole('super_admin');
    }

    public function update(User $user)
    {
        return $user->hasRole('super_admin');
    }

    public function delete(User $user)
    {
        return $user->hasRole('super_admin');
    }
}
