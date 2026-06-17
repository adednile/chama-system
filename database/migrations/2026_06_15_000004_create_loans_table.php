<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('chama_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->integer('term_months');
            $table->decimal('interest_rate', 5, 2)->default(0.00);
            $table->string('status')->default('pending');
            $table->text('reason')->nullable();
            $table->decimal('approved_amount', 10, 2)->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('repaid_at')->nullable();
            $table->decimal('credit_score', 3, 1)->nullable();
            $table->text('rejection_reason')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('outstanding_balance', 10, 2)->default(0);
            $table->date('maturity_date')->nullable();
            $table->timestamps();

            $table->index(['chama_id', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('loans');
    }
};