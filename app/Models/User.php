<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone_number',
        'address',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function caregiver()
    {
        return $this->hasOne(Caregiver::class);
    }

    /**
     * Normalize an Indonesian mobile number for a wa.me link.
     */
    public function getWhatsappNumberAttribute(): ?string
    {
        $number = preg_replace('/\D+/', '', (string) $this->phone_number) ?? '';

        if ($number === '') {
            return null;
        }

        if (Str::startsWith($number, '0')) {
            return '62'.substr($number, 1);
        }

        if (Str::startsWith($number, '8')) {
            return '62'.$number;
        }

        return $number;
    }

    public function getWhatsappUrlAttribute(): ?string
    {
        return $this->whatsapp_number
            ? 'https://wa.me/'.$this->whatsapp_number
            : null;
    }
}
