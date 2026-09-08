<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('order_id')->nullable()->unique();
            $table->text('snap_token')->nullable();
            $table->string('transaction_id')->nullable()->unique();
            $table->string('midtrans_status')->nullable();
            $table->string('fraud_status')->nullable();
            $table->timestamp('last_callback_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropUnique(['order_id']);
            $table->dropUnique(['transaction_id']);
            $table->dropColumn([
                'order_id',
                'snap_token',
                'transaction_id',
                'midtrans_status',
                'fraud_status',
                'last_callback_at',
            ]);
        });
    }
};
