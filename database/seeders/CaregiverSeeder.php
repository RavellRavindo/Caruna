<?php

namespace Database\Seeders;

use App\Models\Caregiver;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CaregiverSeeder extends Seeder
{
    public function run(): void
    {
        // Caregiver 1
        $user1 = User::create([
            'name' => 'Siti Aminah',
            'email' => 'siti@caruna.com',
            'password' => Hash::make('password123'),
            'role' => 'caregiver',
            'phone_number' => '081234567891',
        ]);

        Caregiver::create([
            'user_id' => $user1->id,
            'specialization' => 'Perawatan Lansia & Demensia',
            'price_per_day' => 250000.00,
            'is_available' => true,
            'is_verified' => true,
            'verification_status' => Caregiver::VERIFICATION_VERIFIED,
            'experience_years' => 5,
            'gender' => 'Perempuan',
            'about_me' => 'Saya perawat tersertifikasi yang sabar dan berpengalaman mendampingi lansia.',
            'balance' => 0.00, // Tambahkan ini
        ]);

        // Caregiver 2
        $user2 = User::create([
            'name' => 'Ahmad Fauzi',
            'email' => 'ahmad@caruna.com',
            'password' => Hash::make('password123'),
            'role' => 'caregiver',
            'phone_number' => '081234567892',
        ]);

        Caregiver::create([
            'user_id' => $user2->id,
            'specialization' => 'Pemulihan Pasca Operasi (Mobilitas)',
            'price_per_day' => 350000.00,
            'is_available' => true,
            'is_verified' => true,
            'verification_status' => Caregiver::VERIFICATION_VERIFIED,
            'experience_years' => 3,
            'gender' => 'Laki-laki',
            'about_me' => 'Ahli fisioterapi dasar dan terbiasa membantu mobilitas fisik.',
            'balance' => 750000.00, // Beri saldo awal untuk testing fitur Withdraw nanti
        ]);
    }
}
