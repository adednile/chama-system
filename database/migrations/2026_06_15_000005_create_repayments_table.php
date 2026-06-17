<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('repayments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained()->cascadeOnDelete();
            $table->decimal('repayment_amount', 10, 2);
            $table->date('repayment_date');
            $table->decimal('remaining_balance', 10, 2);
            $table->timestamps();

            $table->index(['loan_id', 'repayment_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('repayments');
    }
};
