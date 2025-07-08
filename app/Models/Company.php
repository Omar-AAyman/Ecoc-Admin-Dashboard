<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = ['name', 'contact_info'];
    
    protected $casts = [
        'contact_info' => 'array',
    ];
    public function tanks()
    {
        return $this->hasMany(Tank::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function tankRentals()
    {
        return $this->hasMany(TankRental::class);
    }

    public function deliveries()
    {
        return $this->hasMany(Delivery::class);
    }
}