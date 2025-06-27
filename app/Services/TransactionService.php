<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Delivery;
use App\Models\Shipment;
use App\Models\Tank;
use App\Models\Transaction;
use App\Models\TransactionDocument;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class TransactionService
{
    public function getTransactions(User $user)
    {
        $query = Transaction::with(['tank', 'destinationTank', 'documents']);
        if ($user->isClient()) {
            $query->whereHas('tank', function ($q) use ($user) {
                $q->where('company_id', $user->company_id);
            });
        }
        return $query->get();
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

            if (!empty($data['shipment']['transport_type'])) {
                $shipmentData = [
                    'transport_type' => $data['shipment']['transport_type'],
                    'vessel_id' => $data['shipment']['vessel_id'] ?? null,
                    'truck_number' => $data['shipment']['truck_number'] ?? null,
                    'trailer_number' => $data['shipment']['trailer_number'] ?? null,
                    'driver_name' => $data['shipment']['driver_name'] ?? null,
                    'product_id' => $data['shipment']['product_id'],
                    'total_quantity' => $data['shipment']['total_quantity'],
                    'arrival_date' => $data['shipment']['arrival_date'],
                    'status' => 'Pending',
                ];
                $shipment = Shipment::create($shipmentData);
                $shipmentId = $shipment->id;
                ActivityLog::create([
                    'user_id' => $user->id,
                    'action' => 'shipment.created',
                    'description' => "Created shipment via {$shipment->vessel_or_vehicle}",
                    'model_type' => Shipment::class,
                    'model_id' => $shipment->id,
                ]);
            }

            if (!empty($data['delivery']['transport_type'])) {
                $deliveryData = [
                    'company_id' => $data['delivery']['company_id'],
                    'transport_type' => $data['delivery']['transport_type'],
                    'vessel_id' => $data['delivery']['vessel_id'] ?? null,
                    'truck_number' => $data['delivery']['truck_number'] ?? null,
                    'trailer_number' => $data['delivery']['trailer_number'] ?? null,
                    'driver_name' => $data['delivery']['driver_name'] ?? null,
                    'product_id' => $data['delivery']['product_id'],
                    'quantity' => $data['delivery']['quantity'],
                    'delivery_date' => $data['delivery']['delivery_date'],
                    'status' => 'Pending',
                ];
                $delivery = Delivery::create($deliveryData);
                $deliveryId = $delivery->id;
                ActivityLog::create([
                    'user_id' => $user->id,
                    'action' => 'delivery.created',
                    'description' => "Created delivery for company {$delivery->company->name}",
                    'model_type' => Delivery::class,
                    'model_id' => $delivery->id,
                ]);
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
            ];

            $transaction = Transaction::create($transactionData);

            $documentTypes = [
                'measurement_report',
                'general_discharge_permit',
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

            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'transaction.created',
                'description' => "Created {$data['type']} transaction for tank {$sourceTank->number}",
                'model_type' => Transaction::class,
                'model_id' => $transaction->id,
            ]);

            return $transaction;
        });
    }
}
