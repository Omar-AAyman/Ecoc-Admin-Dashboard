<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'tank_id',
        'type',
        'destination_tank_id',
        'quantity',
        'date',
        'work_order_number',
        'charge_permit_number',
        'discharge_permit_number',
        'bill_of_lading_number',
        'customs_release_number',
        'engineer_id',
        'technician_id',
        'shipment_id',
        'delivery_id',
        'company_id',
        'original_vessel_id',
        'product_id'
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function tank()
    {
        return $this->belongsTo(Tank::class);
    }

    public function destinationTank()
    {
        return $this->belongsTo(Tank::class, 'destination_tank_id');
    }

    public function shipment()
    {
        return $this->belongsTo(Shipment::class);
    }

    public function delivery()
    {
        return $this->belongsTo(Delivery::class);
    }

    public function engineer()
    {
        return $this->belongsTo(User::class, 'engineer_id');
    }

    public function technician()
    {
        return $this->belongsTo(User::class, 'technician_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function originalVessel()
    {
        return $this->belongsTo(Vessel::class, 'original_vessel_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function documents()
    {
        return $this->hasMany(TransactionDocument::class);
    }
}
