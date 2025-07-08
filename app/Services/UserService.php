<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserService
{
    protected $activityLogService;

    public function __construct(ActivityLogService $activityLogService)
    {
        $this->activityLogService = $activityLogService;
    }

    public function getNonClientUsers()
    {
        return User::with('role')->whereIn('role_id', [1, 2])->whereNull('deleted_at')->get();
    }

    public function getPaginatedNonClientUsers($search = null, $perPage = 10)
    {
        $query = User::with('role')->whereIn('role_id', [1, 2])->whereNull('deleted_at');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%$search%"])
                    ->orWhere('email', 'like', "%$search%")
                    ->orWhereHas('role', function ($q) use ($search) {
                        $q->where('display_name', 'like', "%$search%");
                    });
            });
        }

        return $query->orderBy('id', 'asc')->paginate($perPage);
    }

    public function getUser($id)
    {
        return User::with(['role', 'company.tankRentals.tank.product'])
            ->whereNull('deleted_at')
            ->findOrFail($id);
    }

    public function createNonClientUser(array $data, User $authUser)
    {
        return DB::transaction(function () use ($data, $authUser) {
            $existingUser = User::withTrashed()->where('email', $data['email'])->first();
            if ($existingUser && $existingUser->trashed()) {
                $oldData = $existingUser->only(['first_name', 'last_name', 'email', 'role_id', 'status', 'position']);
                $existingUser->restore();
                $updateData = [
                    'reactivated_at' => now(),
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name'],
                    'password' => Hash::make($data['password']),
                    'role_id' => $data['role_id'],
                    'status' => $data['status'],
                    'position' => $data['position'] ?? 'None',
                ];
                $existingUser->update($updateData);
                $newData = $existingUser->only(['first_name', 'last_name', 'email', 'role_id', 'status', 'position']);
                $newData['password_changed'] = true;
                $this->activityLogService->logActivity(
                    $authUser,
                    'user.reactivated',
                    "Reactivated user {$existingUser->email}",
                    $existingUser,
                    $oldData,
                    $newData
                );
                return $existingUser;
            }

            $data['password'] = Hash::make($data['password']);
            $data['position'] = $data['position'] ?? 'None';
            $user = User::create($data);
            $newData = $user->only(['first_name', 'last_name', 'email', 'role_id', 'status', 'position']);
            $newData['password_changed'] = true;
            $this->activityLogService->logActivity(
                $authUser,
                'user.created',
                "Created user {$user->email}",
                $user,
                [],
                $newData
            );
            return $user;
        });
    }

    public function createClientUser(array $data, User $authUser)
    {
        return DB::transaction(function () use ($data, $authUser) {
            $existingUser = User::withTrashed()->where('email', $data['email'])->first();
            $imagePath = null;
            if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
                $imagePath = $data['image']->store('logos', 'public');
                unset($data['image']);
            }

            if ($existingUser && $existingUser->trashed()) {
                $oldData = $existingUser->only(['first_name', 'last_name', 'email', 'role_id', 'company_id', 'status', 'position', 'image']);
                $existingUser->restore();
                $updateData = [
                    'reactivated_at' => now(),
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name'],
                    'password' => Hash::make($data['password']),
                    'role_id' => 3,
                    'company_id' => $data['company_id'],
                    'status' => $data['status'],
                    'position' => 'None',
                    'image' => $imagePath,
                ];
                if ($imagePath && $existingUser->image) {
                    Storage::disk('public')->delete($existingUser->image);
                }
                $existingUser->update($updateData);
                $newData = $existingUser->only(['first_name', 'last_name', 'email', 'role_id', 'company_id', 'status', 'position', 'image']);
                $newData['password_changed'] = true;
                $this->activityLogService->logActivity(
                    $authUser,
                    'client.reactivated',
                    "Reactivated client {$existingUser->email}",
                    $existingUser,
                    $oldData,
                    $newData
                );
                return $existingUser;
            }

            $data['password'] = Hash::make($data['password']);
            $data['role_id'] = 3;
            $data['position'] = 'None';
            $data['image'] = $imagePath;
            $user = User::create($data);
            $newData = $user->only(['first_name', 'last_name', 'email', 'role_id', 'company_id', 'status', 'position', 'image']);
            $newData['password_changed'] = true;
            $this->activityLogService->logActivity(
                $authUser,
                'client.created',
                "Created client {$user->email}",
                $user,
                [],
                $newData
            );
            return $user;
        });
    }

    public function updateNonClientUser($id, array $data, User $authUser)
    {
        return DB::transaction(function () use ($id, $data, $authUser) {
            $user = User::whereNull('deleted_at')->findOrFail($id);
            $oldData = $user->only(['first_name', 'last_name', 'email', 'role_id', 'status', 'position']);
            if (isset($data['password']) && $data['password']) {
                $data['password'] = Hash::make($data['password']);
                $oldData['password_changed'] = false;
            } else {
                unset($data['password']);
            }
            $data['position'] = $data['position'] ?? 'None';
            $user->update($data);
            $newData = $user->only(['first_name', 'last_name', 'email', 'role_id', 'status', 'position']);
            if (isset($data['password'])) {
                $newData['password_changed'] = true;
            }
            $this->activityLogService->logActivity(
                $authUser,
                'user.updated',
                "Updated user {$user->email}",
                $user,
                $oldData,
                $newData
            );
            return $user;
        });
    }

    public function updateClientUser($id, array $data, User $authUser)
    {
        return DB::transaction(function () use ($id, $data, $authUser) {
            $user = User::whereNull('deleted_at')->findOrFail($id);
            $oldData = $user->only(['first_name', 'last_name', 'email', 'phone', 'role_id', 'company_id', 'status', 'position', 'image']);

            if (isset($data['company_name'])) {
                $user->company->update(['name' => $data['company_name']]);
                unset($data['company_name']);
            }

            if (isset($data['remove_image']) && $data['remove_image'] == '1') {
                if ($user->image) {
                    Storage::disk('public')->delete($user->image);
                }
                $data['image'] = null;
            } elseif (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
                $data['image'] = $data['image']->store('logos', 'public');
                if ($user->image) {
                    Storage::disk('public')->delete($user->image);
                }
            } else {
                unset($data['image']);
            }

            unset($data['remove_image']);

            if (isset($data['password']) && $data['password']) {
                $data['password'] = Hash::make($data['password']);
                $oldData['password_changed'] = false;
            } else {
                unset($data['password']);
            }
            $data['position'] = 'None';
            $data['phone'] = $data['phone'] ?? null;
            $user->update($data);
            $newData = $user->only(['first_name', 'last_name', 'email', 'phone', 'role_id', 'company_id', 'status', 'position', 'image']);
            if (isset($data['password'])) {
                $newData['password_changed'] = true;
            }
            $this->activityLogService->logActivity(
                $authUser,
                'client.updated',
                "Updated client {$user->email}",
                $user,
                $oldData,
                $newData
            );
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
            $oldData = $user->only(['first_name', 'last_name', 'email', 'role_id', 'company_id', 'status', 'position', 'image']);
            if ($user->image) {
                Storage::disk('public')->delete($user->image);
            }
            $email = $user->email;
            $user->delete();
            $this->activityLogService->logActivity(
                $authUser,
                $user->isClient() ? 'client.deleted' : 'user.deleted',
                "Soft deleted user {$email}",
                $user,
                $oldData,
                []
            );
            return true;
        });
    }

    public function updateProfile(array $data, User $authUser)
    {
        return DB::transaction(function () use ($data, $authUser) {
            $oldData = $authUser->only(['first_name', 'last_name', 'phone', 'image']);

            if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile && $authUser->isClient()) {
                $data['image'] = $data['image']->store('logos', 'public');
                if ($authUser->image) {
                    Storage::disk('public')->delete($authUser->image);
                }
            } elseif (isset($data['image']) && $data['image'] === null && $authUser->isClient()) {
                if ($authUser->image) {
                    Storage::disk('public')->delete($authUser->image);
                }
                $data['image'] = null;
            } elseif (!$authUser->isClient()) {
                unset($data['image']);
            }

            if (isset($data['password']) && $data['password']) {
                $data['password'] = Hash::make($data['password']);
                $oldData['password_changed'] = false;
            } else {
                unset($data['password']);
            }
            unset($data['email']);
            $authUser->update($data);
            $newData = $authUser->only(['first_name', 'last_name', 'phone', 'image']);
            if (isset($data['password'])) {
                $newData['password_changed'] = true;
            }
            $this->activityLogService->logActivity(
                $authUser,
                'profile.updated',
                "Updated profile for {$authUser->email}",
                $authUser,
                $oldData,
                $newData
            );
            return $authUser;
        });
    }
}
