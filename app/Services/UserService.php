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
        return User::with('role')->whereIn('role_id', [1, 2])->get();
    }

    public function getPaginatedNonClientUsers($search = null, $perPage = 10)
    {
        $query = User::with('role')->whereIn('role_id', [1, 2]);

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
            ->findOrFail($id);
    }

    public function createNonClientUser(array $data, User $authUser)
    {
        return DB::transaction(function () use ($data, $authUser) {
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
            $imagePath = null;
            if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
                $filename = uniqid() . '.' . $data['image']->getClientOriginalExtension();
                $data['image']->move(public_path('storage/logos'), $filename);
                $imagePath = 'storage/logos/' . $filename;
                unset($data['image']);
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
            $user = User::findOrFail($id);
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
            $user = User::findOrFail($id);

            $oldData = $user->only(['first_name', 'last_name', 'email', 'phone', 'role_id', 'company_id', 'status', 'position', 'image']);

            if (isset($data['company_name'])) {
                $user->company->update(['name' => $data['company_name']]);
                unset($data['company_name']);
            }

            if (isset($data['remove_image']) && $data['remove_image'] == '1') {
                if ($user->image && file_exists(public_path($user->image))) {
                    unlink(public_path($user->image));
                }
                $data['image'] = null;
            } elseif (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
                $filename = uniqid() . '.' . $data['image']->getClientOriginalExtension();
                $data['image']->move(public_path('storage/logos'), $filename);
                if ($user->image && file_exists(public_path($user->image))) {
                    unlink(public_path($user->image));
                }
                $data['image'] = 'storage/logos/' . $filename;
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
            $user = User::findOrFail($id);
            if ($user->transactionsAsEngineer()->exists() || $user->transactionsAsTechnician()->exists()) {
                throw new \Exception('Cannot delete user with associated transactions');
            }
            // Check if user is associated with a company and if that company has tanks
            if ($user->company_id && $user->company && $user->company->tanks()->exists()) {
                throw new \Exception('Cannot delete user associated with a company that has tanks');
            }
            $oldData = $user->only(['first_name', 'last_name', 'email', 'role_id', 'company_id', 'status', 'position', 'image']);
            if ($user->image) {
                Storage::disk('public')->delete($user->image);
            }
            // Handle company: delete if no tanks and no other users are associated, otherwise dissociate
            if ($user->company_id && $user->company) {
                $company = $user->company;
                $otherUsersCount = $company->users()->where('id', '!=', $user->id)->count();
                if ($otherUsersCount === 0) {
                    // No other users are associated with the company, safe to delete
                    $company->delete();
                    $this->activityLogService->logActivity(
                        $authUser,
                        'company.deleted',
                        "Permanently deleted company {$company->name} associated with user {$user->email}",
                        $user,
                        ['company_id' => $company->id, 'company_name' => $company->name],
                        []
                    );
                } else {
                    // Other users are associated, only dissociate the current user
                    $user->company()->dissociate();
                    $user->save(); // Ensure dissociation is saved before deletion
                }
            }
            $email = $user->email;
            $user->forceDelete();
            $this->activityLogService->logActivity(
                $authUser,
                $user->isClient() ? 'client.deleted' : 'user.deleted',
                "Permanently deleted user {$email}",
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

            if ($authUser->isClient()) {
                if (isset($data['remove_image']) && $data['remove_image'] == '1') {
                    if ($authUser->image && file_exists(public_path($authUser->image))) {
                        unlink(public_path($authUser->image));
                    }
                    $data['image'] = null;
                } elseif (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
                    $filename = uniqid() . '.' . $data['image']->getClientOriginalExtension();
                    $data['image']->move(public_path('storage/logos'), $filename);
                    if ($authUser->image && file_exists(public_path($authUser->image))) {
                        unlink(public_path($authUser->image));
                    }
                    $data['image'] = 'storage/logos/' . $filename;
                } else {
                    unset($data['image']);
                }
                unset($data['remove_image']);
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
