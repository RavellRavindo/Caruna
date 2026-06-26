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
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            // Hubungkan ke tabel user (pemilik dompet)
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); 
            
            // 'credit' = uang masuk (tambah saldo), 'debit' = uang keluar (kurangi saldo)
            $table->enum('type', ['credit', 'debit']); 
            
            $table->decimal('amount', 15, 2); // Nominal transaksi
            $table->string('description'); // Contoh: "Pencairan Dana", "Fee Booking #12"
            $table->string('reference_id')->nullable(); // Opsional: ID Booking atau ID Withdrawal
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
    }
};
