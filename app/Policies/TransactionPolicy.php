<?php

namespace App\Policies;

use App\Models\Transaction;
use App\Models\User;

class TransactionPolicy
{
    public function viewAny(User $user)
    {
        return $user->hasAnyRole(['super_admin', 'ceo', 'client']);
    }

    public function view(User $user, Transaction $transaction)
    {
        return $user->hasRole('super_admin') ||
            $user->hasRole('ceo') ||
            ($user->hasRole('client') && $transaction->tank->company_id === $user->company_id);
    }

    public function create(User $user)
    {
        return $user->hasRole('super_admin');
    }

    public function update(User $user, Transaction $transaction)
    {
        return $user->hasRole('super_admin');
    }

    public function delete(User $user, Transaction $transaction)
    {
        return $user->hasRole('super_admin');
    }
}
