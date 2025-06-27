<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    protected $fillable = [
        'company_id',
        'transport_type',
        'vessel_id',
        'truck_number',
        'trailer_number',
        'driver_name',
        'product_id',
        'quantity',
        'delivery_date',
        'status',
    ];

    protected $casts = [
        'delivery_date' => 'date',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

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
