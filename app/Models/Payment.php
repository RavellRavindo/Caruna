<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'booking_id',
        'amount',
        'payment_method',
        'status',
        'payment_date',
        'order_id',
        'snap_token',
        'transaction_id',
        'midtrans_status',
        'fraud_status',
        'last_callback_at',
    ];

    protected function casts(): array
    {
        return [
            'payment_date' => 'datetime',
            'last_callback_at' => 'datetime',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}
