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
        Schema::create('caregivers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('specialization');
            $table->decimal('price_per_day', 12, 2);
            $table->boolean('is_available')->default(true); 
            $table->boolean('is_verified')->default(false); 
            $table->integer('experience_years')->default(0); 
            $table->enum('gender', ['Laki-laki', 'Perempuan']);
            $table->text('about_me')->nullable();
            $table->decimal('balance', 15, 2)->default(0);
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('caregivers');
    }
};
