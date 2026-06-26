<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('withdrawals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('caregiver_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 15, 2); // Jumlah yang ditarik
            $table->string('bank_name'); // Nama Bank (BCA, Mandiri, dll)
            $table->string('account_number'); // Nomor Rekening
            $table->string('account_name'); // Nama Pemilik Rekening
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('withdrawals');
    }
};