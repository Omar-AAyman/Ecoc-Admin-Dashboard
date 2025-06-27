<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function getNonClientUsers()
    {
        return User::with('role')->whereIn('role_id', [1, 2])->whereNull('deleted_at')->get();
    }

    public function getClientUsers(User $authUser)
    {
        $query = User::with(['role', 'company'])->where('role_id', 3)->whereNull('deleted_at');
        if ($authUser->hasRole('client')) {
            $query->where('company_id', $authUser->company_id);
        }
        return $query->get();
    }

    public function getUser($id)
    {
        return User::whereNull('deleted_at')->findOrFail($id);
    }

    public function createNonClientUser(array $data, User $authUser)
    {
        return DB::transaction(function () use ($data, $authUser) {
            $existingUser = User::withTrashed()->where('email', $data['email'])->first();
            if ($existingUser && $existingUser->trashed()) {
                $existingUser->restore();
                $existingUser->update([
                    'reactivated_at' => now(),
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name'],
                    'password' => Hash::make($data['password']),
                    'role_id' => $data['role_id'],
                    'status' => $data['status'],
                ]);
                ActivityLog::create([
                    'user_id' => $authUser->id,
                    'action' => 'user.reactivated',
                    'description' => "Reactivated user {$existingUser->email}",
                    'model_type' => User::class,
                    'model_id' => $existingUser->id,
                ]);
                return $existingUser;
            }

            $data['password'] = Hash::make($data['password']);
            $user = User::create($data);
            ActivityLog::create([
                'user_id' => $authUser->id,
                'action' => 'user.created',
                'description' => "Created user {$user->email}",
                'model_type' => User::class,
                'model_id' => $user->id,
            ]);
            return $user;
        });
    }

    public function createClientUser(array $data, User $authUser)
    {
        return DB::transaction(function () use ($data, $authUser) {
            $existingUser = User::withTrashed()->where('email', $data['email'])->first();
            if ($existingUser && $existingUser->trashed()) {
                $existingUser->restore();
                $existingUser->update([
                    'reactivated_at' => now(),
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name'],
                    'password' => Hash::make($data['password']),
                    'role_id' => 3,
                    'company_id' => $data['company_id'],
                    'status' => $data['status'],
                ]);
                ActivityLog::create([
                    'user_id' => $authUser->id,
                    'action' => 'client.reactivated',
                    'description' => "Reactivated client {$existingUser->email}",
                    'model_type' => User::class,
                    'model_id' => $existingUser->id,
                ]);
                return $existingUser;
            }

            $data['password'] = Hash::make($data['password']);
            $data['role_id'] = 3;
            $user = User::create($data);
            ActivityLog::create([
                'user_id' => $authUser->id,
                'action' => 'client.created',
                'description' => "Created client {$user->email}",
                'model_type' => User::class,
                'model_id' => $user->id,
            ]);
            return $user;
        });
    }

    public function updateNonClientUser($id, array $data, User $authUser)
    {
        return DB::transaction(function () use ($id, $data, $authUser) {
            $user = User::whereNull('deleted_at')->findOrFail($id);
            if (isset($data['password']) && $data['password']) {
                $data['password'] = Hash::make($data['password']);
            } else {
                unset($data['password']);
            }
            $user->update($data);
            ActivityLog::create([
                'user_id' => $authUser->id,
                'action' => 'user.updated',
                'description' => "Updated user {$user->email}",
                'model_type' => User::class,
                'model_id' => $user->id,
            ]);
            return $user;
        });
    }

    public function updateClientUser($id, array $data, User $authUser)
    {
        return DB::transaction(function () use ($id, $data, $authUser) {
            $user = User::whereNull('deleted_at')->findOrFail($id);
            if (isset($data['password']) && $data['password']) {
                $data['password'] = Hash::make($data['password']);
            } else {
                unset($data['password']);
            }
            $user->update($data);
            ActivityLog::create([
                'user_id' => $authUser->id,
                'action' => 'client.updated',
                'description' => "Updated client {$user->email}",
                'model_type' => User::class,
                'model_id' => $user->id,
            ]);
            return $user;
        });
    }

    public function deleteUser($id, User $authUser)
    {
        if ((int) $id === $authUser->id) {
            throw new \Exception('You cannot delete your own account.');
        }

        return DB::transaction(function () use ($id, $authUser) {
            $user = User::whereNull('deleted_at')->findOrFail($id);
            if ($user->transactionsAsEngineer()->exists() || $user->transactionsAsTechnician()->exists()) {
                throw new \Exception('Cannot delete user with associated transactions');
            }
            $email = $user->email;
            $user->delete();
            ActivityLog::create([
                'user_id' => $authUser->id,
                'action' => $user->isClient() ? 'client.deleted' : 'user.deleted',
                'description' => "Soft deleted user {$email}",
                'model_type' => User::class,
                'model_id' => $id,
            ]);
        });
    }

    public function updateProfile(array $data, User $user)
    {
        return DB::transaction(function () use ($data, $user) {
            if (isset($data['password']) && $data['password']) {
                $data['password'] = Hash::make($data['password']);
            } else {
                unset($data['password']);
            }
            $user->update($data);
            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'profile.updated',
                'description' => "Updated profile for {$user->email}",
                'model_type' => User::class,
                'model_id' => $user->id,
            ]);
            return $user;
        });
    }
}
