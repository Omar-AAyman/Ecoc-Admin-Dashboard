<?php

namespace App\Services;

use App\Models\Delivery;
use App\Models\Shipment;
use App\Models\Tank;
use App\Models\Transaction;
use App\Models\TransactionDocument;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TransactionService
{
    protected $activityLogService;

    public function __construct(ActivityLogService $activityLogService)
    {
        $this->activityLogService = $activityLogService;
    }

    /**
     * Get filtered transactions query.
     *
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getTransactionsQuery(array $filters = [])
    {
        $query = Transaction::with(['tank', 'destinationTank', 'originalVessel', 'company', 'product', 'engineer', 'technician'])
            ->orderBy('id', 'desc');

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

    /**
     * Get statistics for transactions.
     *
     * @param array $filters
     * @return array
     */
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
    /**
     * Get transaction details for modal.
     *
     * @param int $id
     * @return Transaction
     */
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
            // Capture original state for logging
            $oldData = $transaction->toArray();
            $oldShipment = $transaction->shipment ? $transaction->shipment->toArray() : null;
            $oldDelivery = $transaction->delivery ? $transaction->delivery->toArray() : null;
            $oldData['shipment'] = $oldShipment;
            $oldData['delivery'] = $oldDelivery;

            // Reverse original tank level changes
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

            // Validate new tank and quantity
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

            // Update transaction
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

            // Update tank levels
            if ($data['type'] === 'loading') {
                $newTank->current_level += $data['quantity'];
                $transaction->shipment()->update(
                    [
                        'transport_type' => $data['shipment']['transport_type'],
                        'vessel_id' => $data['shipment']['vessel_id'] ?? null,
                        'truck_number' => $data['shipment']['truck_number'] ?? null,
                        'trailer_number' => $data['shipment']['trailer_number'] ?? null,
                        'driver_name' => $data['shipment']['driver_name'] ?? null,
                        'total_quantity' => $data['quantity'] ?? null,
                        'berth_number' => $data['shipment']['berth_number'] ?? null,
                        'arrival_date' => $data['date'] ?? null,
                        'status' => 'Pending' ?? null,
                    ]
                );
                $transaction->delivery()->delete(); // Remove delivery if type changed
            } elseif ($data['type'] === 'discharging') {
                $newTank->current_level -= $data['quantity'];
                $transaction->delivery()->update(
                    [
                        'transport_type' => $data['delivery']['transport_type'],
                        'vessel_id' => $data['delivery']['vessel_id'] ?? null,
                        'truck_number' => $data['delivery']['truck_number'] ?? null,
                        'trailer_number' => $data['delivery']['trailer_number'] ?? null,
                        'driver_name' => $data['delivery']['driver_name'] ?? null,
                        'quantity' => $data['quantity'] ?? null,
                        'delivery_date' => $data['date'] ?? null,
                        'status' => 'Pending' ?? null,
                    ]
                );
                $transaction->shipment()->delete(); // Remove shipment if type changed
            } elseif ($data['type'] === 'transfer') {
                $newDestinationTank = Tank::findOrFail($data['destination_tank_id']);
                $newTank->current_level -= $data['quantity'];
                $newDestinationTank->current_level += $data['quantity'];
                if ($newDestinationTank->current_level > $newDestinationTank->maxCapacity()) {
                    throw new \Exception('Transfer would exceed maximum capacity in destination tank.');
                }
                $newDestinationTank->save();
                $transaction->shipment()->delete();
                $transaction->delivery()->delete();
            }

            if ($newTank->current_level < 0) {
                throw new \Exception('Transaction would result in negative level in source tank.');
            }
            $newTank->save();
            $transaction->save();

            // Handle document uploads
            $this->handleDocuments($transaction, $files);

            // Prepare new data for logging
            $newData = $transaction->toArray();
            $newData['shipment'] = $transaction->shipment ? $transaction->shipment->toArray() : null;
            $newData['delivery'] = $transaction->delivery ? $transaction->delivery->toArray() : null;

            // Log the update
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

    protected function handleDocuments(Transaction $transaction, array $files)
    {
        foreach ($files as $type => $file) {
            if ($file) {
                $existingDocument = $transaction->documents()->where('type', $type)->first();
                if ($existingDocument) {
                    Storage::delete($existingDocument->file_path);
                    $existingDocument->delete();
                }

                $path = $file->store('documents', 'public');
                $transaction->documents()->create([
                    'type' => $type,
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'mime_type' => $file->getMimeType(),
                ]);
            }
        }
    }
}
