<?php

namespace App\Services;

use App\Models\User;
use App\Models\Truck;
use Illuminate\Support\Facades\DB;

class TruckService
{
    protected $activityLogService;

    public function __construct(ActivityLogService $activityLogService)
    {
        $this->activityLogService = $activityLogService;
    }

    public function getTrucks()
    {
        return Truck::all();
    }

    public function getPaginatedTrucks($search = null, $perPage = 10)
    {
        $query = Truck::query();

        if ($search) {
            $query->where('truck_number', 'like', "%$search%");
        }

        return $query->orderBy('id', 'asc')->paginate($perPage);
    }

    public function getTruck($id)
    {
        return Truck::findOrFail($id);
    }

    public function createTruck(array $data, User $user)
    {
        return DB::transaction(function () use ($data, $user) {
            $truck = Truck::create($data);
            $this->activityLogService->logActivity(
                $user,
                'truck.created',
                "Created truck {$truck->truck_number}",
                $truck,
                [],
                $truck->getAttributes()
            );
            return $truck;
        });
    }

    public function updateTruck($id, array $data, User $user)
    {
        return DB::transaction(function () use ($id, $data, $user) {
            $truck = Truck::findOrFail($id);
            $oldData = $truck->getAttributes();
            $truck->update($data);
            $this->activityLogService->logActivity(
                $user,
                'truck.updated',
                "Updated truck {$truck->truck_number}",
                $truck,
                $oldData,
                $truck->getAttributes()
            );
            return $truck;
        });
    }

    public function deleteTruck($id, User $user)
    {
        return DB::transaction(function () use ($id, $user) {
            $truck = Truck::findOrFail($id);
            $oldData = $truck->getAttributes();
            $truckNumber = $truck->truck_number;
            $truck->delete();
            $this->activityLogService->logActivity(
                $user,
                'truck.deleted',
                "Deleted truck {$truckNumber}",
                $truck,
                $oldData,
                []
            );
            return true;
        });
    }
}
