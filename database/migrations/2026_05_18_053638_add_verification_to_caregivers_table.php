<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('caregivers', function (Blueprint $table) {
            // Tempat simpan nama file foto
            $table->string('ktp_image')->nullable();
            $table->string('selfie_image')->nullable();
            
            // Status verifikasi: unverified, pending (menunggu admin), verified, rejected
            $table->string('verification_status')->default('unverified'); 
            
            // Jika admin menolak, alasan penolakannya disimpan di sini
            $table->text('rejection_reason')->nullable(); 
        });
    }

    public function down(): void
    {
        Schema::table('caregivers', function (Blueprint $table) {
            $table->dropColumn(['ktp_image', 'selfie_image', 'verification_status', 'rejection_reason']);
        });
    }
};