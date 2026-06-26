<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Patient extends Model
{
    protected $fillable = [
        'user_id',
        'full_name',
        'gender',
        'birth_date',
        'health_condition',
        'emergency_contact',
    ];

    // Relasi ke User (Klien)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
