<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Tank;
use App\Models\TankRental;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TankService
{
    protected $activityLogService;

    public function __construct(ActivityLogService $activityLogService)
    {
        $this->activityLogService = $activityLogService;
    }

    public function getTanks(User $user)
    {
        $tanks = collect();
        if ($user->hasAnyRole(['super_admin', 'ceo'])) {
            $tanks = Tank::with(['product', 'company', 'tankRentals', 'transactions', 'destinationTransactions'])
                ->orderBy('id', 'asc')
                ->get();
        } elseif ($user->isClient()) {
            if ($user->company_id && Tank::where('company_id', $user->company_id)->exists()) {
                $tanks = Tank::with([
                    'product',
                    'company',
                    'tankRentals' => function ($query) use ($user) {
                        $query->where('company_id', $user->company_id);
                    },
                    'transactions' => function ($query) use ($user) {
                        $query->where('company_id', $user->company_id);
                    },
                    'destinationTransactions' => function ($query) use ($user) {
                        $query->where('company_id', $user->company_id);
                    }
                ])
                ->where('company_id', $user->company_id)
                ->orderBy('id', 'asc')
                ->get();
            } else {
                Log::warning('No company assigned or no tanks for client: ' . $user->email);
            }
        } else {
            Log::warning('User has no role or permission to view tanks: ' . $user->email);
        }

        if ($tanks->isEmpty()) {
            Log::warning('Empty tanks collection for user: ' . $user->email);
        }

        return $tanks->map(function ($tank) use ($user) {
            $maxCapacity = $tank->product && $tank->product->density ? $tank->cubic_meter_capacity * $tank->product->density : $tank->cubic_meter_capacity;
            $currentLevel = $tank->current_level ?? 0;
            $capacityUtilization = $maxCapacity > 0 ? min(($currentLevel / $maxCapacity) * 100, 100) : 0;

            // Format rental history
            $rentalHistory = $tank->tankRentals->map(function ($rental) {
                return [
                    'company' => $rental->company ? $rental->company->name : 'N/A',
                    'product' => $rental->product ? $rental->product->name : 'N/A',
                    'start_date' => $rental->start_date ? $rental->start_date->format('Y-m-d') : 'N/A',
                    'end_date' => $rental->end_date ? $rental->end_date->format('Y-m-d') : 'Ongoing',
                    'details' => $rental->details ? json_encode($rental->details) : 'N/A',
                ];
            })->values();

            // Get the most recent active rental to determine the client
            $activeRental = $tank->tankRentals->whereNull('end_date')->sortByDesc('start_date')->first();
            $client = $activeRental ? $activeRental->company->users()->where('role_id', function ($query) {
                $query->select('id')->from('roles')->where('name', 'client')->first()->id ?? null;
            })->first() : null;
            $clientImage = $client ? $client->image_url : 'N/A'; // Using the accessor from User model

            // Format transactions, splitting transfers and applying date filter for clients
            $transactions = $tank->transactions->merge($tank->destinationTransactions);
            if ($user->isClient()) {
                // Get tank IDs from rentals for the user's company
                $rentedTankIds = TankRental::where('company_id', $user->company_id)
                    ->pluck('tank_id')
                    ->unique();

                // Filter transactions to those after the start_date of any active rental
                $transactions = $transactions->filter(function ($transaction) use ($tank, $user, $rentedTankIds) {
                    if (!$rentedTankIds->contains($tank->id)) {
                        return false;
                    }
                    $activeRentals = TankRental::where('tank_id', $tank->id)
                        ->where('company_id', $user->company_id)
                        ->whereNull('end_date')
                        ->get();
                    if ($activeRentals->isEmpty()) {
                        return false;
                    }
                    return $activeRentals->contains(function ($rental) use ($transaction) {
                        return $transaction->date && $transaction->date->gt($rental->start_date);
                    });
                });
            }

            $transactions = $transactions->map(function ($transaction) use ($tank) {
                $type = $transaction->type;
                if ($type === 'transfer') {
                    $type = $tank->id === $transaction->tank_id ? 'discharge (transfer)' : 'load (transfer)';
                }
                return [
                    'id' => $transaction->id,
                    'type' => $type,
                    'quantity' => $transaction->quantity . ' mt',
                    'date' => $transaction->date ? $transaction->date->format('Y-m-d') : 'N/A',
                    'work_order_number' => $transaction->work_order_number ?? 'N/A',
                    'charge_permit_number' => $transaction->charge_permit_number ?? 'N/A',
                    'discharge_permit_number' => $transaction->discharge_permit_number ?? 'N/A',
                    'bill_of_lading_number' => $transaction->bill_of_lading_number ?? 'N/A',
                    'customs_release_number' => $transaction->customs_release_number ?? 'N/A',
                    'engineer' => $transaction->engineer ? $transaction->engineer->full_name : 'N/A',
                    'technician' => $transaction->technician ? $transaction->technician->full_name : 'N/A',
                    'company' => $transaction->company ? $transaction->company->name : 'N/A',
                    'product' => $transaction->product ? $transaction->product->name : 'N/A',
                ];
            })->values();

            return [
                'id' => $tank->number,
                'dbId' => $tank->id,
                'content' => $tank->product ? $tank->product->name : 'N/A',
                'status' => ucfirst($tank->status),
                'cubicMeterCapacity' => $tank->cubic_meter_capacity,
                'currentLevel' => $currentLevel,
                'maxCapacity' => number_format($maxCapacity, 2, '.', ''),
                'company' => $tank->company ? $tank->company->name : 'N/A',
                'capacityUtilization' => number_format($capacityUtilization, 0) . '%',
                'temperatureCelsius' => $tank->temperature !== null ? number_format($tank->temperature, 2) : 'N/A',
                'temperatureFahrenheit' => $tank->temperature_fahrenheit !== null ? number_format($tank->temperature_fahrenheit, 2) : 'N/A',
                'clientImage' => $clientImage, // New: Client's image URL
                'companyName' => $tank->company ? $tank->company->name : 'N/A', // New: Explicit company name
                'product' => $tank->product ? $tank->product->name : 'N/A', // New: Explicit product name
                'liquidColor' => match ($tank->product_id % 10) {
                    1 => ['#ef4444', '#b91c1c'],
                    2 => ['#4ade80', '#16a34a'],
                    3 => ['#60a5fa', '#2563eb'],
                    4 => ['#fb923c', '#ea580c'],
                    5 => ['#c084fc', '#9333ea'],
                    6 => ['#22d3ee', '#0891b2'],
                    7 => ['#f472b6', '#e82688'],
                    8 => ['#fcd34d', '#fbbf24'],
                    9 => ['#94a3b8', '#64748b'],
                    default => ['#d9f99d', '#a3e635'],
                },
                'rentalHistory' => $rentalHistory,
                'transactions' => $transactions,
            ];
        })->all();
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
                    throw new \Exception("Current capacity ({$data['current_level']} mt) exceeds max capacity ($maxCapacity mt) for the selected product.");
                }
            }

            // Validate temperature
            if (isset($data['temperature']) && $data['temperature'] !== null) {
                if (!is_numeric($data['temperature']) || $data['temperature'] < -50 || $data['temperature'] > 100) {
                    throw new \Exception("Temperature must be a number between -50°C and 100°C.");
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

            if (isset($data['temperature']) && $data['temperature'] !== null) {
                $this->activityLogService->logActivity(
                    $user,
                    'tank.temperature_updated',
                    "Set initial temperature for tank {$tank->number} to {$data['temperature']}°C",
                    $tank,
                    [],
                    ['temperature' => $data['temperature']]
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
                    throw new \Exception("Current capacity ({$data['current_level']} mt) exceeds max capacity ($maxCapacity mt) for the selected product.");
                }
            }

            // Validate temperature
            if (isset($data['temperature']) && $data['temperature'] !== null) {
                if (!is_numeric($data['temperature']) || $data['temperature'] < -50 || $data['temperature'] > 100) {
                    throw new \Exception("Temperature must be a number between -50°C and 100°C.");
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
                    "Manually updated current capacity for tank {$tank->number} from {$oldData['current_level']} mt to {$data['current_level']} mt by user {$user->full_name} (ID: {$user->id}) at " . Carbon::now()->toDateTimeString(),
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

            if (isset($data['temperature']) && $data['temperature'] != ($oldData['temperature'] ?? null)) {
                $this->activityLogService->logActivity(
                    $user,
                    'tank.temperature_updated',
                    "Manually updated temperature for tank {$tank->number} from " . ($oldData['temperature'] ?? 'N/A') . "°C to {$data['temperature']}°C by user {$user->full_name} (ID: {$user->id}) at " . Carbon::now()->toDateTimeString(),
                    $tank,
                    [
                        'temperature' => $oldData['temperature'] ?? null,
                        'updated_at' => Carbon::now()->toDateTimeString(),
                        'updated_by' => [
                            'user_id' => $user->id,
                            'username' => $user->full_name
                        ]
                    ],
                    [
                        'temperature' => $data['temperature'],
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
                'temperature' => null,
                'status' => 'Available',
            ];
            $tank->update($newData);
            $this->activityLogService->logActivity(
                $user,
                'tank.reset',
                "Reset tank {$tank->number} (company, product, status, current capacity, and temperature)",
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

    public function getDashboardStats(User $user)
    {
        $tanks = $this->getTanks($user);
        $totalTanks = count($tanks);
        $avgCapacityUtilization = array_sum(array_map(function ($tank) {
            return (float)str_replace('%', '', $tank['capacityUtilization']);
        }, $tanks)) / ($totalTanks ?: 1);

        $activeRentals = collect($tanks)->reduce(function ($carry, $tank) {
            return $carry + $tank['rentalHistory']->filter(function ($rental) {
                return $rental['end_date'] === 'Ongoing';
            })->count();
        }, 0);

        $completedRentals = collect($tanks)->reduce(function ($carry, $tank) {
            return $carry + $tank['rentalHistory']->filter(function ($rental) {
                return $rental['end_date'] !== 'Ongoing' && $rental['end_date'] !== 'N/A';
            })->count();
        }, 0);

        $totalDischarge = 0;
        $totalLoad = 0;
        foreach ($tanks as $tank) {
            foreach ($tank['transactions'] as $transaction) {
                $quantity = (float)str_replace(' mt', '', $transaction['quantity']);
                if (in_array($transaction['type'], ['discharging', 'discharge (transfer)'])) {
                    $totalDischarge += $quantity;
                }
                if (in_array($transaction['type'], ['loading', 'load (transfer)'])) {
                    $totalLoad += $quantity;
                }
            }
        }

        $activeRentalDays = collect($tanks)->flatMap->rentalHistory->filter(function ($rental) {
            return $rental['end_date'] === 'Ongoing';
        })->sum(function ($rental) {
            $start = Carbon::parse($rental['start_date']);
            return $start->diffInDays(Carbon::now());
        });

        $months = [];
        $utilizationData = [];
        $rentalsData = [];
        $currentDate = Carbon::now()->startOfMonth();
        for ($i = 0; $i < 5; $i++) {
            $month = $currentDate->copy()->subMonths($i);
            $months[] = $month->format('M Y');

            // Fetch tanks with rentals or transactions in the month
            $monthlyTanks = Tank::with([
                'product',
                'tankRentals' => function ($query) use ($month, $user) {
                    $query->where('start_date', '<=', $month->endOfMonth())
                          ->where(function ($q) use ($month) {
                              $q->where('end_date', '>=', $month->startOfMonth())
                                ->orWhereNull('end_date');
                          });
                    if ($user->isClient()) {
                        $query->where('company_id', $user->company_id);
                    }
                },
                'transactions' => function ($query) use ($month, $user) {
                    $query->whereBetween('date', [$month->startOfMonth(), $month->endOfMonth()]);
                    if ($user->isClient()) {
                        $query->where('company_id', $user->company_id);
                    }
                },
                'destinationTransactions' => function ($query) use ($month, $user) {
                    $query->whereBetween('date', [$month->startOfMonth(), $month->endOfMonth()]);
                    if ($user->isClient()) {
                        $query->where('company_id', $user->company_id);
                    }
                }
            ])
            ->where(function ($query) use ($month, $user) {
                $query->whereHas('tankRentals', function ($q) use ($month, $user) {
                    $q->where('start_date', '<=', $month->endOfMonth())
                      ->where(function ($q) use ($month) {
                          $q->where('end_date', '>=', $month->startOfMonth())
                            ->orWhereNull('end_date');
                      });
                    if ($user->isClient()) {
                        $q->where('company_id', $user->company_id);
                    }
                })
                ->orWhereHas('transactions', function ($q) use ($month, $user) {
                    $q->whereBetween('date', [$month->startOfMonth(), $month->endOfMonth()]);
                    if ($user->isClient()) {
                        $q->where('company_id', $user->company_id);
                    }
                })
                ->orWhereHas('destinationTransactions', function ($q) use ($month, $user) {
                    $q->whereBetween('date', [$month->startOfMonth(), $month->endOfMonth()]);
                    if ($user->isClient()) {
                        $q->where('company_id', $user->company_id);
                    }
                });
                if ($user->isClient()) {
                    $query->where('company_id', $user->company_id);
                }
            })
            ->get();

            // Calculate average utilization for tanks with activity in the month
            $totalUtilization = $monthlyTanks->avg(function ($tank) use ($month, $user) {
                $maxCapacity = $tank->product && $tank->product->density ? $tank->cubic_meter_capacity * $tank->product->density : $tank->cubic_meter_capacity;
                $currentLevel = 0;

                // Estimate utilization based on transactions in the month
                $transactions = $tank->transactions->merge($tank->destinationTransactions)
                    ->filter(function ($transaction) use ($month, $user, $tank) {
                        $isValid = Carbon::parse($transaction->date)->between($month->startOfMonth(), $month->endOfMonth());
                        if ($user->isClient()) {
                            $activeRentals = TankRental::where('tank_id', $tank->id)
                                ->where('company_id', $user->company_id)
                                ->whereNull('end_date')
                                ->get();
                            if ($activeRentals->isEmpty()) {
                                return false;
                            }
                            $isValid = $isValid && $activeRentals->contains(function ($rental) use ($transaction) {
                                return $transaction->date && $transaction->date->gt($rental->start_date);
                            });
                        }
                        return $isValid;
                    });

                foreach ($transactions as $transaction) {
                    $quantity = (float)$transaction->quantity;
                    if (in_array($transaction->type, ['loading', 'load (transfer)'])) {
                        $currentLevel += $quantity;
                    } elseif (in_array($transaction->type, ['discharging', 'discharge (transfer)'])) {
                        $currentLevel -= $quantity;
                    }
                }

                // Ensure currentLevel is non-negative and doesn't exceed maxCapacity
                $currentLevel = max(0, min($currentLevel, $maxCapacity));
                return $maxCapacity > 0 ? min(($currentLevel / $maxCapacity) * 100, 100) : 0;
            }) ?: 0;
            $utilizationData[] = number_format($totalUtilization, 1);

            // Count rentals active in the month
            $monthlyRentals = TankRental::where('start_date', '<=', $month->endOfMonth())
                ->where(function ($q) use ($month, $user) {
                    $q->where('end_date', '>=', $month->startOfMonth())
                      ->orWhereNull('end_date');
                    if ($user->isClient()) {
                        $q->where('company_id', $user->company_id);
                    }
                })->count();
            $rentalsData[] = $monthlyRentals;
        }
        $performanceTrends = [
            'labels' => array_reverse($months),
            'utilization' => array_reverse($utilizationData),
            'rentals' => array_reverse($rentalsData),
        ];

        return [
            'totalTanks' => $totalTanks,
            'avgCapacityUtilization' => number_format($avgCapacityUtilization, 2),
            'activeRentals' => $activeRentals,
            'completedRentals' => $completedRentals,
            'totalDischarge' => number_format($totalDischarge, 2),
            'totalLoad' => number_format($totalLoad, 2),
            'tanks' => $tanks,
            'performanceTrends' => $performanceTrends,
        ];
    }
}