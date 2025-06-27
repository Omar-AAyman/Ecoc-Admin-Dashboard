<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Tank;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class TankService
{
    public function getTanks(User $user)
    {
        $query = Tank::with(['product', 'company'])->orderBy('id', 'asc');
        if ($user->isClient()) {
            $query->where('company_id', $user->company_id);
        }
        return $query->get();
    }
    public function getTank($id)
    {
        return Tank::with(['product', 'company'])->findOrFail($id);
    }
    public function createTank(array $data, User $user)
    {
        return DB::transaction(function () use ($data, $user) {
            $tank = Tank::create($data);
            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'tank.created',
                'description' => "Created tank {$tank->number}",
                'model_type' => Tank::class,
                'model_id' => $tank->id,
            ]);
            return $tank;
        });
    }

    public function updateTankSettings($id, array $data, User $user)
    {
        return DB::transaction(function () use ($id, $data, $user) {
            $tank = Tank::findOrFail($id);
            if ($tank->current_level > 0 && $tank->product_id !== $data['product_id']) {
                throw new \Exception('Cannot change product in a non-empty tank');
            }
            $tank->update($data);
            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'tank.updated',
                'description' => "Updated settings for tank {$tank->number}",
                'model_type' => Tank::class,
                'model_id' => $tank->id,
            ]);
            return $tank;
        });
    }

    public function deleteTank($id, User $user)
    {
        return DB::transaction(function () use ($id, $user) {
            $tank = Tank::findOrFail($id);
            if ($tank->current_level > 0) {
                throw new \Exception('Cannot delete a non-empty tank');
            }
            $tankNumber = $tank->number;
            $tank->delete();
            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'tank.deleted',
                'description' => "Deleted tank {$tankNumber}",
                'model_type' => Tank::class,
                'model_id' => $id,
            ]);
            return true;
        });
    }
}
