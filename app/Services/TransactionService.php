<?php

namespace App\Services;

use App\Models\Delivery;
use App\Models\Shipment;
use App\Models\Tank;
use App\Models\TankRental;
use App\Models\Transaction;
use App\Models\TransactionDocument;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class TransactionService
{
    protected $activityLogService;

    public function __construct(ActivityLogService $activityLogService)
    {
        $this->activityLogService = $activityLogService;
    }

    /**
     * Get transactions query, filtered by user role.
     * For clients, only return transactions for tanks rented by their company (company_id matches) where the transaction date is after the start_date of any active rental (end_date is null) for that tank and company.
     *
     * @param User $user
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getTransactionsQuery(User $user, array $filters = [])
    {
        $query = Transaction::with(['tank', 'destinationTank', 'originalVessel', 'company', 'product', 'engineer', 'technician'])
            ->orderBy('id', 'desc');

        if ($user->isClient()) {
            // For clients, restrict to transactions for tanks rented by their company after any active rental's start date
            $companyId = $user->company_id; // Assumes User model has company_id
            $rentedTankIds = TankRental::where('company_id', $companyId)
                ->whereNull('end_date')
                ->pluck('tank_id')
                ->unique();

            // Log warning if multiple active rentals exist for the same tank and company
            $activeRentalsPerTank = TankRental::where('company_id', $companyId)
                ->whereNull('end_date')
                ->groupBy('tank_id')
                ->select('tank_id', DB::raw('count(*) as rental_count'))
                ->having('rental_count', '>', 1)
                ->get();
            if ($activeRentalsPerTank->isNotEmpty()) {
                Log::warning('Multiple active rentals detected for tanks: ' . $activeRentalsPerTank->pluck('tank_id')->implode(', ') . ' for company_id: ' . $companyId);
            }

            $query->where('company_id', $companyId)
                  ->where(function ($q) use ($rentedTankIds) {
                      $q->whereIn('tank_id', $rentedTankIds)
                        ->orWhereIn('destination_tank_id', $rentedTankIds);
                  })
                  ->whereExists(function ($subQuery) use ($companyId) {
                      $subQuery->select(DB::raw(1))
                               ->from('tank_rentals')
                               ->where(function ($q) {
                                   $q->whereColumn('tank_rentals.tank_id', 'transactions.tank_id')
                                     ->orWhereColumn('tank_rentals.tank_id', 'transactions.destination_tank_id');
                               })
                               ->where('tank_rentals.company_id', $companyId)
                               ->whereNull('tank_rentals.end_date')
                               ->whereColumn('transactions.date', '>', 'tank_rentals.start_date');
                  });
        }

        // Apply additional filters
        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }
        if (!empty($filters['tank_id'])) {
            $query->where('tank_id', $filters['tank_id']);
        }
        if (!empty($filters['destination_tank_id'])) {
            $query->where('destination_tank_id', $filters['destination_tank_id']);
        }
        if (!empty($filters['original_vessel_id'])) {
            $query->where('original_vessel_id', $filters['original_vessel_id']);
        }
        if ($user->isClient()) {
            // Company filter is already applied for clients
        } else if (!empty($filters['company_id'])) {
            $query->where('company_id', $filters['company_id']);
        }
        if (!empty($filters['product_id'])) {
            $query->where('product_id', $filters['product_id']);
        }
        if (!empty($filters['engineer_id'])) {
            $query->where('engineer_id', $filters['engineer_id']);
        }
        if (!empty($filters['technician_id'])) {
            $query->where('technician_id', $filters['technician_id']);
        }
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->whereHas('company', function ($q) use ($filters) {
                    $q->where('name', 'like', '%' . $filters['search'] . '%');
                })
                  ->orWhereHas('product', function ($q) use ($filters) {
                      $q->where('name', 'like', '%' . $filters['search'] . '%');
                  })
                  ->orWhereHas('tank', function ($q) use ($filters) {
                      $q->where('number', 'like', '%' . $filters['search'] . '%');
                  });
            });
        }
        if (!empty($filters['from'])) {
            $query->whereDate('date', '>=', $filters['from']);
        }
        if (!empty($filters['to'])) {
            $query->whereDate('date', '<=', $filters['to']);
        }

        return $query;
    }

    public function getStatistics(array $filters = [])
    {
        $query = Transaction::query();

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }
        if (!empty($filters['tank_id'])) {
            $query->where('tank_id', $filters['tank_id']);
        }
        if (!empty($filters['destination_tank_id'])) {
            $query->where('destination_tank_id', $filters['destination_tank_id']);
        }
        if (!empty($filters['original_vessel_id'])) {
            $query->where('original_vessel_id', $filters['original_vessel_id']);
        }
        if (!empty($filters['company_id'])) {
            $query->where('company_id', $filters['company_id']);
        }
        if (!empty($filters['product_id'])) {
            $query->where('product_id', $filters['product_id']);
        }
        if (!empty($filters['engineer_id'])) {
            $query->where('engineer_id', $filters['engineer_id']);
        }
        if (!empty($filters['technician_id'])) {
            $query->where('technician_id', $filters['technician_id']);
        }
        if (!empty($filters['transport_type'])) {
            $query->where('transport_type', $filters['transport_type']);
        }
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->whereHas('company', function ($q) use ($filters) {
                    $q->where('name', 'like', '%' . $filters['search'] . '%');
                })
                    ->orWhereHas('product', function ($q) use ($filters) {
                        $q->where('name', 'like', '%' . $filters['search'] . '%');
                    })
                    ->orWhereHas('tank', function ($q) use ($filters) {
                        $q->where('number', 'like', '%' . $filters['search'] . '%');
                    });
            });
        }
        if (!empty($filters['from'])) {
            $query->whereDate('date', '>=', $filters['from']);
        }
        if (!empty($filters['to'])) {
            $query->whereDate('date', '<=', $filters['to']);
        }

        $totalTransactions = $query->count();
        $transactionsByType = $query->select('type')
            ->get()
            ->groupBy('type')
            ->map(function ($group) {
                return [
                    'type' => $group->first()->type,
                    'count' => $group->count(),
                ];
            })->values();

        return [
            'totalTransactions' => $totalTransactions,
            'transactionsByType' => $transactionsByType,
        ];
    }

    public function getTransactionDetails($id)
    {
        return Transaction::with(['tank', 'destinationTank', 'originalVessel', 'company', 'product', 'engineer', 'technician'])
            ->findOrFail($id);
    }

    public function createTransaction(array $data, array $files, User $user)
    {
        return DB::transaction(function () use ($data, $files, $user) {
            $sourceTank = Tank::findOrFail($data['tank_id']);
            if (!$sourceTank->product && $data['type'] !== 'loading') {
                throw new \Exception('No product assigned to source tank');
            }

            if ($data['type'] === 'transfer') {
                $destTank = Tank::findOrFail($data['destination_tank_id']);
                if ($data['quantity'] > $sourceTank->current_level) {
                    throw new \Exception('Insufficient product in source tank');
                }
                if ($sourceTank->product_id !== $destTank->product_id && $destTank->current_level > 0) {
                    throw new \Exception('Destination tank contains different product');
                }
                if ($data['quantity'] > ($destTank->maxCapacity() - $destTank->current_level)) {
                    throw new \Exception('Destination tank capacity exceeded');
                }
                $sourceTank->decrement('current_level', $data['quantity']);
                $destTank->increment('current_level', $data['quantity']);
                if (!$destTank->product_id) {
                    $destTank->update(['product_id' => $sourceTank->product_id]);
                }
            } elseif ($data['type'] === 'loading') {
                if ($data['quantity'] > ($sourceTank->maxCapacity() - $sourceTank->current_level)) {
                    throw new \Exception('Tank capacity exceeded');
                }
                $sourceTank->increment('current_level', $data['quantity']);
            } else { // discharging
                if ($data['quantity'] > $sourceTank->current_level) {
                    throw new \Exception('Insufficient product in tank');
                }
                $sourceTank->decrement('current_level', $data['quantity']);
            }

            $shipmentId = null;
            $deliveryId = null;

            if ($data['type'] === 'loading' && !empty($data['shipment']['transport_type'])) {
                $shipmentData = [
                    'transport_type' => $data['shipment']['transport_type'],
                    'vessel_id' => $data['shipment']['vessel_id'] ?? null,
                    'truck_number' => $data['shipment']['truck_number'] ?? null,
                    'trailer_number' => $data['shipment']['trailer_number'] ?? null,
                    'driver_name' => $data['shipment']['driver_name'] ?? null,
                    'total_quantity' => $data['quantity'],
                    'berth_number' => $data['shipment']['berth_number'],
                    'arrival_date' => $data['date'],
                    'status' => 'Pending',
                ];
                $shipment = Shipment::create($shipmentData);
                $shipmentId = $shipment->id;
                $this->activityLogService->logActivity(
                    $user,
                    'shipment.created',
                    "Created shipment via {$shipment->transport_type}",
                    $shipment,
                    [],
                    $shipmentData
                );
            }

            if ($data['type'] === 'discharging' && !empty($data['delivery']['transport_type'])) {
                $deliveryData = [
                    'transport_type' => $data['delivery']['transport_type'],
                    'vessel_id' => $data['delivery']['vessel_id'] ?? null,
                    'truck_number' => $data['delivery']['truck_number'] ?? null,
                    'trailer_number' => $data['delivery']['trailer_number'] ?? null,
                    'driver_name' => $data['delivery']['driver_name'] ?? null,
                    'quantity' => $data['quantity'],
                    'delivery_date' => $data['date'],
                    'status' => 'Pending',
                ];
                $delivery = Delivery::create($deliveryData);
                $deliveryId = $delivery->id;
                $this->activityLogService->logActivity(
                    $user,
                    'delivery.created',
                    "Created delivery via {$delivery->transport_type}",
                    $delivery,
                    [],
                    $deliveryData
                );
            }

            $transactionData = [
                'tank_id' => $data['tank_id'],
                'type' => $data['type'],
                'destination_tank_id' => $data['destination_tank_id'] ?? null,
                'quantity' => $data['quantity'],
                'date' => $data['date'],
                'work_order_number' => $data['work_order_number'] ?? null,
                'charge_permit_number' => $data['charge_permit_number'] ?? null,
                'discharge_permit_number' => $data['discharge_permit_number'] ?? null,
                'bill_of_lading_number' => $data['bill_of_lading_number'] ?? null,
                'customs_release_number' => $data['customs_release_number'] ?? null,
                'engineer_id' => $data['engineer_id'] ?? null,
                'technician_id' => $data['technician_id'] ?? null,
                'shipment_id' => $shipmentId,
                'delivery_id' => $deliveryId,
                'company_id' => $sourceTank->company_id,
                'original_vessel_id' => $data['original_vessel_id'] ?? null,
                'product_id' => $sourceTank->product_id,
            ];

            $transaction = Transaction::create($transactionData);

            $documentTypes = [
                'measurement_report',
                'inspection_form',
                'customs_release_form',
                'charge_permit_document',
                'discharge_permit_document'
            ];
            foreach ($documentTypes as $type) {
                if (isset($files[$type]) && $files[$type]->isValid()) {
                    $file = $files[$type];
                    $fileName = $file->getClientOriginalName();
                    $filePath = $file->storeAs(
                        "transactions/{$transaction->id}",
                        "{$type}_" . time() . '_' . $fileName,
                        'public'
                    );

                    TransactionDocument::create([
                        'transaction_id' => $transaction->id,
                        'type' => $type,
                        'file_path' => $filePath,
                        'file_name' => $fileName,
                        'uploaded_by' => $user->id,
                    ]);
                }
            }

            $oldData = [];
            $newData = $transaction->only([
                'tank_id',
                'type',
                'destination_tank_id',
                'quantity',
                'date',
                'company_id',
                'product_id',
                'original_vessel_id',
                'work_order_number',
                'charge_permit_number',
                'discharge_permit_number',
                'bill_of_lading_number',
                'customs_release_number',
                'engineer_id',
                'technician_id',
                'shipment_id',
                'delivery_id'
            ]);
            $this->activityLogService->logActivity(
                $user,
                'transaction.created',
                "Created {$data['type']} transaction for tank {$sourceTank->number}",
                $transaction,
                $oldData,
                $newData
            );

            return $transaction;
        });
    }

    public function updateTransaction(Transaction $transaction, array $data, array $files, User $user)
    {
        return DB::transaction(function () use ($transaction, $data, $files, $user) {
            $oldData = $transaction->toArray();
            $oldShipment = $transaction->shipment ? $transaction->shipment->toArray() : null;
            $oldDelivery = $transaction->delivery ? $transaction->delivery->toArray() : null;
            $oldData['shipment'] = $oldShipment;
            $oldData['delivery'] = $oldDelivery;

            $originalTank = Tank::findOrFail($transaction->tank_id);
            $originalDestinationTank = $transaction->destination_tank_id ? Tank::findOrFail($transaction->destination_tank_id) : null;

            if ($transaction->type === 'loading') {
                if ($originalTank->current_level < $transaction->quantity) {
                    throw new \Exception('Cannot reverse loading: Source tank has insufficient product.');
                }
                $originalTank->current_level -= $transaction->quantity;
            } elseif ($transaction->type === 'discharging') {
                if ($originalTank->current_level + $transaction->quantity > $originalTank->maxCapacity()) {
                    throw new \Exception('Cannot reverse discharging: Source tank would exceed maximum capacity.');
                }
                $originalTank->current_level += $transaction->quantity;
            } elseif ($transaction->type === 'transfer') {
                if ($originalTank->current_level + $transaction->quantity > $originalTank->maxCapacity()) {
                    throw new \Exception('Cannot reverse transfer: Source tank would exceed maximum capacity.');
                }
                $originalTank->current_level += $transaction->quantity;
                if ($originalDestinationTank) {
                    if ($originalDestinationTank->current_level < $transaction->quantity) {
                        throw new \Exception('Cannot reverse transfer: Destination tank has insufficient product.');
                    }
                    $originalDestinationTank->current_level -= $transaction->quantity;
                    $originalDestinationTank->save();
                }
            }
            $originalTank->save();

            $newTank = Tank::findOrFail($data['tank_id']);
            $product = $newTank->product()->first();
            if (!$product) {
                throw new \Exception('No product assigned to the selected tank.');
            }
            if ($data['type'] === 'loading') {
                if ($newTank->free_space < $data['quantity']) {
                    throw new \Exception('Not enough free space in the source tank.');
                }
            } elseif ($data['type'] === 'discharging') {
                if ($newTank->current_level < $data['quantity']) {
                    throw new \Exception('Not enough product in the source tank.');
                }
            } elseif ($data['type'] === 'transfer') {
                $newDestinationTank = Tank::findOrFail($data['destination_tank_id']);
                if ($newDestinationTank->free_space < $data['quantity']) {
                    throw new \Exception('Not enough free space in the destination tank.');
                }
                if ($newTank->product_id !== $newDestinationTank->product_id) {
                    throw new \Exception('Source and destination tanks must contain the same product.');
                }
                if ($newTank->current_level < $data['quantity']) {
                    throw new \Exception('Not enough product in the source tank for transfer.');
                }
            }

            $transaction->fill([
                'type' => $data['type'],
                'tank_id' => $data['tank_id'],
                'destination_tank_id' => $data['destination_tank_id'] ?? null,
                'original_vessel_id' => $data['original_vessel_id'] ?? null,
                'company_id' => $newTank->company_id,
                'product_id' => $newTank->product_id,
                'quantity' => $data['quantity'],
                'date' => $data['date'],
                'work_order_number' => $data['work_order_number'] ?? null,
                'bill_of_lading_number' => $data['bill_of_lading_number'] ?? null,
                'customs_release_number' => $data['customs_release_number'] ?? null,
                'charge_permit_number' => $data['charge_permit_number'] ?? null,
                'discharge_permit_number' => $data['discharge_permit_number'] ?? null,
                'engineer_id' => $data['engineer_id'] ?? null,
                'technician_id' => $data['technician_id'] ?? null,
                'updated_by' => $user->id,
            ]);

            if ($data['type'] === 'loading') {
                $newTank->current_level += $data['quantity'];
                $shipmentData = [
                    'transport_type' => $data['shipment']['transport_type'],
                    'vessel_id' => $data['shipment']['vessel_id'] ?? null,
                    'truck_number' => $data['shipment']['truck_number'] ?? null,
                    'trailer_number' => $data['shipment']['trailer_number'] ?? null,
                    'driver_name' => $data['shipment']['driver_name'] ?? null,
                    'total_quantity' => $data['quantity'],
                    'berth_number' => $data['shipment']['berth_number'],
                    'arrival_date' => $data['date'],
                    'status' => $transaction->shipment->status ?? 'Pending',
                ];
                if ($transaction->shipment) {
                    $transaction->shipment->update($shipmentData);
                } else {
                    $shipment = Shipment::create($shipmentData);
                    $transaction->shipment_id = $shipment->id;
                }
            } elseif ($data['type'] === 'discharging') {
                $newTank->current_level -= $data['quantity'];
                $deliveryData = [
                    'transport_type' => $data['delivery']['transport_type'],
                    'vessel_id' => $data['delivery']['vessel_id'] ?? null,
                    'truck_number' => $data['delivery']['truck_number'] ?? null,
                    'trailer_number' => $data['delivery']['trailer_number'] ?? null,
                    'driver_name' => $data['delivery']['driver_name'] ?? null,
                    'quantity' => $data['quantity'],
                    'delivery_date' => $data['date'],
                    'status' => $transaction->delivery->status ?? 'Pending',
                ];
                if ($transaction->delivery) {
                    $transaction->delivery->update($deliveryData);
                } else {
                    $delivery = Delivery::create($deliveryData);
                    $transaction->delivery_id = $delivery->id;
                }
            } elseif ($data['type'] === 'transfer') {
                $newTank->current_level -= $data['quantity'];
                $newDestinationTank = Tank::findOrFail($data['destination_tank_id']);
                $newDestinationTank->current_level += $data['quantity'];
                $newDestinationTank->save();
            }

            $newTank->save();
            $transaction->save();

            $documentTypes = [
                'measurement_report',
                'inspection_form',
                'customs_release_form',
                'charge_permit_document',
                'discharge_permit_document'
            ];
            foreach ($documentTypes as $type) {
                if (isset($files[$type]) && $files[$type]->isValid()) {
                    $existingDoc = TransactionDocument::where('transaction_id', $transaction->id)
                        ->where('type', $type)
                        ->first();
                    if ($existingDoc) {
                        Storage::disk('public')->delete($existingDoc->file_path);
                        $existingDoc->delete();
                    }
                    $file = $files[$type];
                    $fileName = $file->getClientOriginalName();
                    $filePath = $file->storeAs(
                        "transactions/{$transaction->id}",
                        "{$type}_" . time() . '_' . $fileName,
                        'public'
                    );

                    TransactionDocument::create([
                        'transaction_id' => $transaction->id,
                        'type' => $type,
                        'file_path' => $filePath,
                        'file_name' => $fileName,
                        'uploaded_by' => $user->id,
                    ]);
                }
            }

            $newData = $transaction->toArray();
            $newData['shipment'] = $transaction->shipment ? $transaction->shipment->toArray() : null;
            $newData['delivery'] = $transaction->delivery ? $transaction->delivery->toArray() : null;

            $this->activityLogService->logActivity(
                $user,
                'transaction.updated',
                "Updated {$data['type']} transaction for tank {$newTank->number}",
                $transaction,
                $oldData,
                $newData
            );

            return $transaction;
        });
    }

    public function deleteTransaction(Transaction $transaction, User $user)
    {
        return DB::transaction(function () use ($transaction, $user) {
            $sourceTank = Tank::findOrFail($transaction->tank_id);
            $destinationTank = $transaction->destination_tank_id ? Tank::findOrFail($transaction->destination_tank_id) : null;

            // Validate tank levels before reversal
            if ($transaction->type === 'loading') {
                if ($sourceTank->current_level < $transaction->quantity) {
                    throw new \Exception('Cannot delete loading transaction: Source tank has insufficient product.');
                }
                $sourceTank->decrement('current_level', $transaction->quantity);
            } elseif ($transaction->type === 'discharging') {
                if ($sourceTank->current_level + $transaction->quantity > $sourceTank->maxCapacity()) {
                    throw new \Exception('Cannot delete discharging transaction: Source tank would exceed maximum capacity.');
                }
                $sourceTank->increment('current_level', $transaction->quantity);
            } elseif ($transaction->type === 'transfer') {
                if ($destinationTank->current_level < $transaction->quantity) {
                    throw new \Exception('Cannot delete transfer transaction: Destination tank has insufficient product.');
                }
                if ($sourceTank->current_level + $transaction->quantity > $sourceTank->maxCapacity()) {
                    throw new \Exception('Cannot delete transfer transaction: Source tank would exceed maximum capacity.');
                }
                $sourceTank->increment('current_level', $transaction->quantity);
                $destinationTank->decrement('current_level', $transaction->quantity);
                // If destination tank is now empty and has no product, clear product_id
                if ($destinationTank->current_level == 0 && $destinationTank->product_id) {
                    $destinationTank->update(['product_id' => null]);
                }
                $destinationTank->save();
            }
            $sourceTank->save();

            // Delete associated documents and their files
            $documents = TransactionDocument::where('transaction_id', $transaction->id)->get();
            foreach ($documents as $document) {
                Storage::disk('public')->delete($document->file_path);
                $document->delete();
            }

            // Delete associated shipment or delivery
            if ($transaction->shipment_id) {
                $shipment = Shipment::find($transaction->shipment_id);
                if ($shipment) {
                    $this->activityLogService->logActivity(
                        $user,
                        'shipment.deleted',
                        "Deleted shipment via {$shipment->transport_type} for transaction {$transaction->id}",
                        $shipment,
                        $shipment->toArray(),
                        []
                    );
                    $shipment->delete();
                }
            }
            if ($transaction->delivery_id) {
                $delivery = Delivery::find($transaction->delivery_id);
                if ($delivery) {
                    $this->activityLogService->logActivity(
                        $user,
                        'delivery.deleted',
                        "Deleted delivery via {$delivery->transport_type} for transaction {$transaction->id}",
                        $delivery,
                        $delivery->toArray(),
                        []
                    );
                    $delivery->delete();
                }
            }

            // Log transaction deletion
            $this->activityLogService->logActivity(
                $user,
                'transaction.deleted',
                "Deleted {$transaction->type} transaction for tank {$sourceTank->number}",
                $transaction,
                $transaction->toArray(),
                []
            );

            // Delete the transaction
            $transaction->delete();
        });
    }
}
