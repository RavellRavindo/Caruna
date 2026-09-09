<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('reconciliation_status')->default('not_required')->index()->after('last_callback_at');
            $table->text('reconciliation_note')->nullable()->after('reconciliation_status');
            $table->string('refund_reference')->nullable()->after('reconciliation_note');
            $table->timestamp('reconciled_at')->nullable()->after('refund_reference');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex(['reconciliation_status']);
            $table->dropColumn([
                'reconciliation_status',
                'reconciliation_note',
                'refund_reference',
                'reconciled_at',
            ]);
        });
    }
};
