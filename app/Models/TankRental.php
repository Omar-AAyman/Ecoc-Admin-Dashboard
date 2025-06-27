<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TankRental extends Model
{
    protected $fillable = [
        'tank_id',
        'company_id',
        'user_id',
        'product_id',
        'start_date',
        'end_date',
        'details',
    ];

    protected $casts = [
        'details' => 'array',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function tank()
    {
        return $this->belongsTo(Tank::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
