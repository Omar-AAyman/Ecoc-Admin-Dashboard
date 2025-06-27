<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['name', 'density'];

    public function tanks()
    {
        return $this->hasMany(Tank::class);
    }

    public function tankRentals()
    {
        return $this->hasMany(TankRental::class);
    }

    public function shipments()
    {
        return $this->hasMany(Shipment::class);
    }

    public function deliveries()
    {
        return $this->hasMany(Delivery::class);
    }
}
