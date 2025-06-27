<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tank extends Model
{
    protected $fillable = [
        'number',
        'cubic_meter_capacity',
        'current_level',
        'status',
        'product_id',
        'company_id',
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

    public function destinationTransactions()
    {
        return $this->hasMany(Transaction::class, 'destination_tank_id');
    }

    public function tankRentals()
    {
        return $this->hasMany(TankRental::class);
    }

    public function maxCapacity()
    {
        return $this->product ? $this->cubic_meter_capacity * $this->product->density : $this->cubic_meter_capacity;
    }
}
