<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user)
    {
        return $user->hasRole('super_admin') || $user->hasRole('client');
    }

    public function view(User $user, User $model)
    {
        return $user->hasRole('super_admin') || ($user->hasRole('client') && $user->company_id === $model->company_id) || $user->id === $model->id;
    }

    public function create(User $user)
    {
        return $user->hasRole('super_admin');
    }

    public function update(User $user, User $model = null)
    {
        if (!$model) {
            // Class-based authorization (e.g., for accessing edit form)
            return $user->hasRole('super_admin') || $user->hasRole('client');
        }

        // Instance-based authorization
        return $user->hasRole('super_admin') || ($user->hasRole('client') && $user->company_id === $model->company_id) || $user->id === $model->id;
    }

    public function delete(User $user, User $model = null)
    {
        if (!$model) {
            // Class-based authorization (e.g., for accessing delete routes)
            return $user->hasRole('super_admin') || $user->hasRole('client');
        }

        // Instance-based authorization
        return ($user->hasRole('super_admin') && $user->id !== $model->id) || ($user->hasRole('client') && $user->company_id === $model->company_id && $user->id !== $model->id);
    }
}
