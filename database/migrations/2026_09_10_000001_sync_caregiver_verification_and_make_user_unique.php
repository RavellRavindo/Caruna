<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Selaraskan data lama sebelum verification_status digunakan sebagai sumber kebenaran.
        DB::table('caregivers')
            ->where('is_verified', true)
            ->update(['verification_status' => 'verified']);

        Schema::table('caregivers', function (Blueprint $table) {
            $table->unique('user_id', 'caregivers_user_id_unique');
        });
    }

    public function down(): void
    {
        Schema::table('caregivers', function (Blueprint $table) {
            $table->dropUnique('caregivers_user_id_unique');
        });
    }
};
