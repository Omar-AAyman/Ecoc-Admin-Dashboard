<?php

namespace App\Policies;

use App\Models\User;

class DriverPolicy
{
    public function viewAny(User $user)
    {
        return $user->hasAnyRole(['super_admin', 'engineer']);
    }

    public function view(User $user)
    {
        return $user->hasAnyRole(['super_admin', 'engineer']);
    }

    public function create(User $user)
    {
        return $user->hasAnyRole(['super_admin', 'engineer']);
    }

    public function update(User $user)
    {
        return $user->hasAnyRole(['super_admin', 'engineer']);
    }

    public function delete(User $user)
    {
        return $user->hasAnyRole(['super_admin', 'engineer']);
    }
}
