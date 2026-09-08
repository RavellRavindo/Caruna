<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::create([
            'name' => 'Admin Caruna',
            'email' => 'admin@caruna.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Klien Contoh',
            'email' => 'klien@caruna.com',
            'password' => bcrypt('password'),
            'role' => 'client',
            'phone_number' => '081234567890',
            'address' => 'Jl. Contoh No. 10, Jakarta',
        ]);

        $this->call([
            CaregiverSeeder::class,
        ]);
    }
}
