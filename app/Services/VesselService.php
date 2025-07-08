<?php

namespace App\Services;

use App\Models\User;
use App\Models\Vessel;
use Illuminate\Support\Facades\DB;

class VesselService
{
    protected $activityLogService;

    public function __construct(ActivityLogService $activityLogService)
    {
        $this->activityLogService = $activityLogService;
    }

    public function getVessels()
    {
        return Vessel::all();
    }

    public function getPaginatedVessels($search = null, $perPage = 10)
    {
        $query = Vessel::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('nationality', 'like', "%$search%");
            });
        }

        return $query->orderBy('id', 'asc')->paginate($perPage);
    }

    public function getVessel($id)
    {
        return Vessel::findOrFail($id);
    }

    public function createVessel(array $data, User $user)
    {
        return DB::transaction(function () use ($data, $user) {
            $vessel = Vessel::create($data);
            $this->activityLogService->logActivity(
                $user,
                'vessel.created',
                "Created vessel {$vessel->name}",
                $vessel,
                [],
                $vessel->getAttributes()
            );
            return $vessel;
        });
    }

    public function updateVessel($id, array $data, User $user)
    {
        return DB::transaction(function () use ($id, $data, $user) {
            $vessel = Vessel::findOrFail($id);
            $oldData = $vessel->getAttributes();
            $vessel->update($data);
            $this->activityLogService->logActivity(
                $user,
                'vessel.updated',
                "Updated vessel {$vessel->name}",
                $vessel,
                $oldData,
                $vessel->getAttributes()
            );
            return $vessel;
        });
    }

    public function deleteVessel($id, User $user)
    {
        return DB::transaction(function () use ($id, $user) {
            $vessel = Vessel::findOrFail($id);
            $oldData = $vessel->getAttributes();
            $vesselName = $vessel->name;
            $vessel->delete();
            $this->activityLogService->logActivity(
                $user,
                'vessel.deleted',
                "Deleted vessel {$vesselName}",
                $vessel,
                $oldData,
                []
            );
            return true;
        });
    }
}
