<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    public const RECONCILIATION_NOT_REQUIRED = 'not_required';

    public const RECONCILIATION_REFUND_REQUIRED = 'refund_required';

    public const RECONCILIATION_REFUNDED = 'refunded';

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
        'reconciliation_status',
        'reconciliation_note',
        'refund_reference',
        'reconciled_at',
    ];

    protected function casts(): array
    {
        return [
            'payment_date' => 'datetime',
            'last_callback_at' => 'datetime',
            'reconciled_at' => 'datetime',
        ];
    }

    public function requiresRefund(): bool
    {
        return $this->reconciliation_status === self::RECONCILIATION_REFUND_REQUIRED;
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}
