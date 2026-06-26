<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 
        'caregiver_id', 
        'patient_id', 
        'start_date', 
        'total_days', 
        'snapshot_price', 
        'total_amount', 
        'status'
    ];

    public function getEndDateAttribute()
    {
        return Carbon::parse($this->start_date)->addDays($this->total_days)->format('Y-m-d');
    }

    public function patient() {
        return $this->belongsTo(Patient::class);
    }

    public function caregiver() {
        return $this->belongsTo(Caregiver::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }
}
