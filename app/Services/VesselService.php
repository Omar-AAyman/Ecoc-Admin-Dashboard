<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\User;
use App\Models\Vessel;
use Illuminate\Support\Facades\DB;

class VesselService
{
    public function getVessels()
    {
        return Vessel::all();
    }

    public function getVessel($id)
    {
        return Vessel::findOrFail($id);
    }

    public function createVessel(array $data, User $user)
    {
        return DB::transaction(function () use ($data, $user) {
            $vessel = Vessel::create($data);
            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'vessel.created',
                'description' => "Created vessel {$vessel->name}",
                'model_type' => Vessel::class,
                'model_id' => $vessel->id,
            ]);
            return $vessel;
        });
    }

    public function updateVessel($id, array $data, User $user)
    {
        return DB::transaction(function () use ($id, $data, $user) {
            $vessel = Vessel::findOrFail($id);
            $vessel->update($data);
            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'vessel.updated',
                'description' => "Updated vessel {$vessel->name}",
                'model_type' => Vessel::class,
                'model_id' => $vessel->id,
            ]);
            return $vessel;
        });
    }

    public function deleteVessel($id, User $user)
    {
        return DB::transaction(function () use ($id, $user) {
            $vessel = Vessel::findOrFail($id);
            $vessel->delete();
            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'vessel.deleted',
                'description' => "Deleted vessel {$vessel->name}",
                'model_type' => Vessel::class,
                'model_id' => $id,
            ]);
            return true;
        });
    }
}
