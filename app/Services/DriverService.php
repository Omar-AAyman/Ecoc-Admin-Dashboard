<?php

namespace App\Services;

use App\Models\User;
use App\Models\Driver;
use Illuminate\Support\Facades\DB;

class DriverService
{
    protected $activityLogService;

    public function __construct(ActivityLogService $activityLogService)
    {
        $this->activityLogService = $activityLogService;
    }

    public function getPaginatedDrivers($search = null, $perPage = 10)
    {
        $query = Driver::query();

        if ($search) {
            $query->where('name', 'like', "%$search%");
        }

        return $query->orderBy('id', 'asc')->paginate($perPage);
    }

    public function getDriver($id)
    {
        return Driver::findOrFail($id);
    }

    public function createDriver(array $data, User $user)
    {
        return DB::transaction(function () use ($data, $user) {
            $driver = Driver::create($data);
            $this->activityLogService->logActivity(
                $user,
                'driver.created',
                "Created driver {$driver->name}",
                $driver,
                [],
                $driver->getAttributes()
            );
            return $driver;
        });
    }

    public function updateDriver($id, array $data, User $user)
    {
        return DB::transaction(function () use ($id, $data, $user) {
            $driver = Driver::findOrFail($id);
            $oldData = $driver->getAttributes();
            $driver->update($data);
            $this->activityLogService->logActivity(
                $user,
                'driver.updated',
                "Updated driver {$driver->name}",
                $driver,
                $oldData,
                $driver->getAttributes()
            );
            return $driver;
        });
    }

    public function deleteDriver($id, User $user)
    {
        return DB::transaction(function () use ($id, $user) {
            $driver = Driver::findOrFail($id);
            $oldData = $driver->getAttributes();
            $driverName = $driver->name;
            $driver->delete();
            $this->activityLogService->logActivity(
                $user,
                'driver.deleted',
                "Deleted driver {$driverName}",
                $driver,
                $oldData,
                []
            );
            return true;
        });
    }
}
