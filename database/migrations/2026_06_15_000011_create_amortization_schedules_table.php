<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('amortization_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained()->cascadeOnDelete();
            $table->integer('installment_no');
            $table->date('due_date');
            $table->decimal('principal_portion', 10, 2);
            $table->decimal('interest_portion', 10, 2);
            $table->decimal('balance_after', 10, 2);
            $table->string('payment_status')->default('unpaid'); // unpaid, paid, late, partial
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('amortization_schedules');
    }
};
