<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionDocument extends Model
{
    protected $fillable = [
        'transaction_id',
        'type',
        'file_path',
        'file_name',
        'uploaded_by',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
