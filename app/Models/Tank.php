<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tank extends Model
{
    protected $fillable = [
        'number',
        'cubic_meter_capacity',
        'current_level',
        'temperature',
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

    // Calculate max capacity (in metric tons)
    public function maxCapacity()
    {
        return $this->product ? $this->cubic_meter_capacity * $this->product->density : 0;
    }

    // Accessor for free space (in metric tons)
    public function getFreeSpaceAttribute()
    {
        $maxCapacity = $this->maxCapacity();
        return $maxCapacity - $this->current_level;
    }

    // Convert temperature from Celsius to Fahrenheit
    public function getTemperatureFahrenheitAttribute()
    {
        return $this->temperature !== null ? ($this->temperature * 9 / 5) + 32 : null;
    }
}
