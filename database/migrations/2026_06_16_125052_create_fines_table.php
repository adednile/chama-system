<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('fines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('chama_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->string('type');
            $table->string('status')->default('unpaid');
            $table->date('due_date');
            $table->date('paid_at')->nullable();
            $table->text('description')->nullable();
            $table->string('billing_cycle')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('reference_type')->nullable();
            $table->timestamps();

            $table->index(['chama_id', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('fines');
    }
};