<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Caregiver extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'specialization', 'price_per_day', 'is_available', 'is_verified', 'experience_years', 'gender', 'about_me', 'balance',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    // Menghitung rata-rata bintang
    public function getAverageRatingAttribute()
    {
        return $this->reviews()->avg('rating') ?? 0;
    }

    // Menghitung total jumlah ulasan
    public function getTotalReviewsAttribute()
    {
        return $this->reviews()->count();
    }
}
