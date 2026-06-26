<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // Klien
            $table->foreignId('caregiver_id')->constrained()->cascadeOnDelete(); // Perawat
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete(); // Pasien spesifik
            $table->date('start_date');
            $table->integer('total_days');
            $table->decimal('snapshot_price', 12, 2); // Harga saat dipesan
            $table->decimal('total_amount', 12, 2);
            $table->enum('status', [
                'pending', 'approved', 'rejected', 'waiting_payment', 'paid', 'ongoing', 'waiting_confirmation', 'completed','canceled'
            ])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
