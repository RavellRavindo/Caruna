<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Withdrawal extends Model
{
    use HasFactory;

    protected $fillable = [
        'caregiver_id',
        'amount',
        'bank_name',
        'account_number',
        'account_name',
        'idempotency_key',
        'status',
    ];

    // Relasi balik ke Caregiver
    public function caregiver()
    {
        return $this->belongsTo(Caregiver::class);
    }
}
