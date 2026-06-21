<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mapped_mpesa_transactions', function (Blueprint $table) {
            // Stores the declared payment intent: 'contribution' or 'loan_repayment'
            $table->string('payment_type')->default('contribution')->after('status');

            // Optional reference to the specific loan being repaid
            $table->foreignId('loan_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete()
                ->after('payment_type');
        });
    }

    public function down(): void
    {
        Schema::table('mapped_mpesa_transactions', function (Blueprint $table) {
            $table->dropForeign(['loan_id']);
            $table->dropColumn(['payment_type', 'loan_id']);
        });
    }
};
