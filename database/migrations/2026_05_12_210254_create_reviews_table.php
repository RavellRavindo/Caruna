<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            // Relasi ke tabel bookings (agar 1 pesanan hanya bisa di-review 1 kali)
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            // Klien yang memberi ulasan
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); 
            // Perawat yang diberi ulasan
            $table->foreignId('caregiver_id')->constrained()->cascadeOnDelete(); 
            
            // Bintang 1 sampai 5
            $table->integer('rating'); 
            // Komentar (opsional/boleh kosong)
            $table->text('comment')->nullable(); 
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};