<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Caregiver extends Model
{
    use HasFactory;

    public const VERIFICATION_UNVERIFIED = 'unverified';

    public const VERIFICATION_PENDING = 'pending';

    public const VERIFICATION_VERIFIED = 'verified';

    public const VERIFICATION_REJECTED = 'rejected';

    protected $fillable = [
        'user_id', 'specialization', 'price_per_day', 'is_available', 'is_verified', 'experience_years', 'gender', 'about_me', 'balance',
        'verification_status', 'rejection_reason',
    ];

    protected function casts(): array
    {
        return [
            'price_per_day' => 'decimal:2',
            'balance' => 'decimal:2',
            'is_available' => 'boolean',
            'is_verified' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Caregiver $caregiver): void {
            if (! $caregiver->verification_status) {
                $caregiver->verification_status = $caregiver->is_verified
                    ? self::VERIFICATION_VERIFIED
                    : self::VERIFICATION_UNVERIFIED;
            }

            // verification_status adalah sumber kebenaran. Kolom lama is_verified
            // tetap disinkronkan agar data lama dan query eksternal tidak menyimpang.
            $caregiver->is_verified = $caregiver->verification_status === self::VERIFICATION_VERIFIED;

            if (! $caregiver->is_verified) {
                $caregiver->is_available = false;
            }
        });
    }

    public function isVerified(): bool
    {
        return $this->verification_status === self::VERIFICATION_VERIFIED;
    }

    public function scopeVerified($query)
    {
        return $query->where('verification_status', self::VERIFICATION_VERIFIED);
    }

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
}
