<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    protected $fillable = [
        'transport_type',
        'vessel_id',
        'truck_number',
        'trailer_number',
        'driver_name',
        'product_id',
        'total_quantity',
        'berth_number',
        'arrival_date',
        'status',
    ];

    protected $casts = [
        'arrival_date' => 'date',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function vessel()
    {
        return $this->belongsTo(Vessel::class);
    }
}
