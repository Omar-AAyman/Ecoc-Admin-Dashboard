<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Tank;
use App\Models\TankRental;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TankService
{
    protected $activityLogService;

    public function __construct(ActivityLogService $activityLogService)
    {
        $this->activityLogService = $activityLogService;
    }

    public function getTanks(User $user)
    {
        if ($user->hasAnyRole(['super_admin', 'ceo'])) {
            return Tank::with(['product', 'company'])->orderBy('id', 'asc')->get();
        }

        if ($user->isClient()) {
            if (!$user->company_id || !Tank::where('company_id', $user->company_id)->exists()) {
                return new Collection();
            }
            return Tank::with(['product', 'company'])
                ->where('company_id', $user->company_id)
                ->orderBy('id', 'asc')
                ->get();
        }

        return new Collection();
    }

    public function getPaginatedTanks(User $user, $perPage = 10, $search = null)
    {
        $query = Tank::with(['product', 'company'])->orderBy('id', 'asc');

        if ($user->hasAnyRole(['super_admin', 'ceo'])) {
            // No filtering for super_admin or ceo
        } elseif ($user->isClient()) {
            if (!$user->company_id || !Tank::where('company_id', $user->company_id)->exists()) {
                return $query->paginate($perPage);
            }
            $query->where('company_id', $user->company_id);
        } else {
            return $query->paginate($perPage);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('number', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%")
                    ->orWhereHas('product', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        return $query->paginate($perPage);
    }

    public function getAssignedTanks(User $user)
    {
        $query = Tank::with(['product', 'company'])->whereNotNull('company_id')->orderBy('id', 'asc');
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
            // Validate current_level against max_capacity
            if (isset($data['product_id']) && $data['product_id'] && isset($data['current_level']) && $data['current_level'] > 0) {
                $product = \App\Models\Product::findOrFail($data['product_id']);
                $maxCapacity = $data['cubic_meter_capacity'] * $product->density;
                if ($data['current_level'] > $maxCapacity) {
                    throw new \Exception("Current level ({$data['current_level']} mt) exceeds max capacity ($maxCapacity mt) for the selected product.");
                }
            }

            // Set status based on company_id or product_id
            $data['status'] = (isset($data['company_id']) && $data['company_id']) || (isset($data['product_id']) && $data['product_id']) ? 'In Use' : 'Available';
            $tank = Tank::create($data);

            // Create TankRental if company_id is set
            if (isset($data['company_id']) && $data['company_id']) {
                TankRental::create([
                    'tank_id' => $tank->id,
                    'company_id' => $data['company_id'],
                    'product_id' => $data['product_id'] ?? null,
                    'start_date' => Carbon::now(),
                ]);
            }

            $this->activityLogService->logActivity(
                $user,
                'tank.created',
                "Created tank {$tank->number}",
                $tank,
                [],
                $tank->getAttributes()
            );

            if (isset($data['current_level']) && $data['current_level'] > 0) {
                $this->activityLogService->logActivity(
                    $user,
                    'tank.level_updated',
                    "Set initial level for tank {$tank->number} to {$data['current_level']} mt",
                    $tank,
                    [],
                    ['current_level' => $data['current_level']]
                );
            }

            return $tank;
        });
    }

    public function updateTank($id, array $data, User $user)
    {
        return DB::transaction(function () use ($id, $data, $user) {
            $tank = Tank::findOrFail($id);
            if ($tank->current_level > 0 && isset($data['product_id']) && $tank->product_id != $data['product_id']) {
                throw new \Exception('Cannot change product in a non-empty tank');
            }

            // Validate current_level against max_capacity
            if (isset($data['product_id']) && $data['product_id'] && isset($data['current_level']) && $data['current_level'] > 0) {
                $product = \App\Models\Product::findOrFail($data['product_id']);
                $cubicMeterCapacity = $data['cubic_meter_capacity'] ?? $tank->cubic_meter_capacity;
                $maxCapacity = $cubicMeterCapacity * $product->density;
                if ($data['current_level'] > $maxCapacity) {
                    throw new \Exception("Current level ({$data['current_level']} mt) exceeds max capacity ($maxCapacity mt) for the selected product.");
                }
            }

            // Set status based on company_id or product_id
            $data['status'] = (isset($data['company_id']) && $data['company_id']) || (isset($data['product_id']) && $data['product_id']) ? 'In Use' : 'Available';

            // Handle TankRental updates only if company_id has changed or is being unset
            $activeRental = TankRental::where('tank_id', $tank->id)
                ->whereNull('end_date')
                ->first();

            if ((isset($data['company_id']) && $data['company_id'] != $tank->company_id) || (array_key_exists('company_id', $data) && !isset($data['company_id']))) {
                // End existing rental if company_id changes or is set to null
                if ($activeRental) {
                    $activeRental->update(['end_date' => Carbon::now()]);
                    $this->activityLogService->logActivity(
                        $user,
                        'tank_rental.ended',
                        "Ended rental for tank {$tank->number} with company ID " . ($activeRental->company_id ?? 'None'),
                        $tank,
                        $activeRental->getAttributes(),
                        ['end_date' => Carbon::now()]
                    );
                }

                // Create new rental if company_id is set and not null
                if (isset($data['company_id']) && $data['company_id']) {
                    $newRental = TankRental::create([
                        'tank_id' => $tank->id,
                        'company_id' => $data['company_id'],
                        'product_id' => $data['product_id'] ?? null,
                        'start_date' => Carbon::now(),
                    ]);
                    $this->activityLogService->logActivity(
                        $user,
                        'tank_rental.created',
                        "Created new rental for tank {$tank->number} with company ID {$data['company_id']}",
                        $tank,
                        [],
                        $newRental->getAttributes()
                    );
                }
            }

            $oldData = $tank->getAttributes();
            $tank->update($data);
            $this->activityLogService->logActivity(
                $user,
                'tank.updated',
                "Updated tank {$tank->number} with new details by user {$user->full_name} (ID: {$user->id}) at " . Carbon::now()->toDateTimeString(),
                $tank,
                $oldData,
                $tank->getAttributes()
            );

            if (isset($data['current_level']) && $data['current_level'] != $oldData['current_level']) {
                $this->activityLogService->logActivity(
                    $user,
                    'tank.level_updated',
                    "Manually updated current level for tank {$tank->number} from {$oldData['current_level']} mt to {$data['current_level']} mt by user {$user->full_name} (ID: {$user->id}) at " . Carbon::now()->toDateTimeString(),
                    $tank,
                    [
                        'current_level' => $oldData['current_level'],
                        'updated_at' => Carbon::now()->toDateTimeString(),
                        'updated_by' => [
                            'user_id' => $user->id,
                            'username' => $user->full_name
                        ]
                    ],
                    [
                        'current_level' => $data['current_level'],
                        'updated_at' => Carbon::now()->toDateTimeString(),
                        'updated_by' => [
                            'user_id' => $user->id,
                            'username' => $user->full_name
                        ]
                    ]
                );
            }

            return $tank;
        });
    }

    public function resetTank($id, User $user)
    {
        return DB::transaction(function () use ($id, $user) {
            $tank = Tank::findOrFail($id);
            $oldData = $tank->getAttributes();

            // End active rental if any
            $activeRental = TankRental::where('tank_id', $tank->id)
                ->whereNull('end_date')
                ->first();
            if ($activeRental) {
                $activeRental->update(['end_date' => Carbon::now()]);
                $this->activityLogService->logActivity(
                    $user,
                    'tank_rental.ended',
                    "Ended rental for tank {$tank->number} with company ID " . ($activeRental->company_id ?? 'None'),
                    $tank,
                    $activeRental->getAttributes(),
                    ['end_date' => Carbon::now()]
                );
            }

            $newData = [
                'company_id' => null,
                'product_id' => null,
                'current_level' => 0,
                'status' => 'Available',
            ];
            $tank->update($newData);
            $this->activityLogService->logActivity(
                $user,
                'tank.reset',
                "Reset tank {$tank->number} (company, product, status, and current level)",
                $tank,
                $oldData,
                $newData
            );
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
            if ($tank->tankRentals()->exists()) {
                throw new \Exception('Cannot delete a tank with rental history');
            }
            $oldData = $tank->getAttributes();
            $tankNumber = $tank->number;
            $tank->delete();
            $this->activityLogService->logActivity(
                $user,
                'tank.deleted',
                "Deleted tank {$tankNumber}",
                $tank,
                $oldData,
                []
            );
            return true;
        });
    }
}
