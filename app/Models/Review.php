<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'user_id',
        'caregiver_id',
        'rating',
        'comment',
    ];

    // Ulasan ini milik booking yang mana?
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    // Ulasan ini ditulis oleh klien siapa?
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Ulasan ini ditujukan untuk perawat siapa?
    public function caregiver()
    {
        return $this->belongsTo(Caregiver::class);
    }
}