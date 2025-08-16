<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TankRental extends Model
{
    protected $fillable = [
        'tank_id',
        'company_id',
        'product_id',
        'contract_duration',
        'start_date',
        'end_date',
        'details',
        'reminder_30_days_sent',
        'reminder_60_days_sent'
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

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
